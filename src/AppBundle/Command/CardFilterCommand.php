<?php

namespace AppBundle\Command;


use AppBundle\Helper\CardFilter\CardFilterInterface;
use AppBundle\Helper\TrelloClient\Model\BoardList;
use AppBundle\Helper\TrelloClient\Model\Card;
use AppBundle\Helper\TrelloClient\TrelloClient;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\Question;

class CardFilterCommand extends ContainerAwareCommand
{
    const MENU_OPTION_ADD_FILTER = "Add Filter";
    const MENU_OPTION_REMOVE_FILTER = "Remove Filter";
    const MENU_OPTION_PRINT_CARDS = "Print Cards";
    const MENU_OPTION_EXIT = "Exit";

    /**
     * @var string
     */
    private $boardId;

    /**
     * @var TrelloClient
     */
    private $trelloClient;

    /**
     * @var Card[]|ArrayCollection
     */
    private $boardCards;

    /**
     * @var BoardList[]|ArrayCollection
     */
    private $boardLists;

    /**
     * @var Card[]|ArrayCollection
     */
    private $filteredCards;

    /**
     * @var CardFilterInterface[]|ArrayCollection
     */
    private $filters;

    protected function configure()
    {
        $this->setName('filter:cards')
            ->addOption('board_id', 'b', InputOption::VALUE_REQUIRED, 'Trello ID of the Board to '.
                'retrieve cards from', '9ZScVBSt') // default 'current-work'
            ->setDescription('Retrieves all the cards on a board and allows custom filters to be applied client-side');
    }

    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        $this->trelloClient = $this->getContainer()->get('trello_client');
        $this->filteredCards = new ArrayCollection();
        $this->filters = new ArrayCollection();

        $this->boardId = $input->getOption('board_id');
    }


    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('Loading cards from API...');
        $this->boardCards = $this->getTrelloClient()->getBoardCards($this->getBoardId());
        $output->writeln('Loading lists from API...');
        $this->boardLists = $this->getTrelloClient()->getBoardLists($this->getBoardId());

        $continue = true;
        while ($continue){
            $this->filteredCards = $this->filterCards($this->getBoardCards(), $this->getFilters());
            $option = $this->printMenuOptions($input, $output);
            switch ($option){
                case self::MENU_OPTION_ADD_FILTER:
                    $this->addFilter($input, $output);
                    break;
                case self::MENU_OPTION_REMOVE_FILTER:
                    $this->removeFilter($input, $output);
                    break;
                case self::MENU_OPTION_PRINT_CARDS:
                    $this->printFilteredCards($output);
                    break;
                case self::MENU_OPTION_EXIT:
                    $continue = false;
                    break;
            }
            $output->writeln('+----------------------------------------------------------------+'.PHP_EOL);
        }

        $output->writeln('Bye o/');
    }

    protected function printMenuOptions(InputInterface $input, OutputInterface $output)
    {

        $output->writeln('Total Cards: '.$this->getBoardCards()->count().' Filtered Cards: '.
            $this->getFilteredCards()->count());

        // Output list of active filters
        if(!$this->getFilters()->isEmpty()){
            $output->writeln('Active Filters');

            $table = new Table($output);
            $table->setStyle('compact');
            $table->setColumnWidths([15, 50]);
            $rows = [];
            foreach ($this->getFilters() as $filter){
                $rows[] = [$filter->getName(), ' - '.$filter->getConfigDescription()];
            }
            $table->setRows($rows);
            $table->render();
        }

        // Display prompt for next action
        $menuOptions = new ChoiceQuestion(
            'Options:',
            [
                self::MENU_OPTION_ADD_FILTER,
                self::MENU_OPTION_REMOVE_FILTER,
                self::MENU_OPTION_PRINT_CARDS,
                self::MENU_OPTION_EXIT
            ]
        );
        $questionHelper = $this->getHelper('question');
        return  $questionHelper->ask($input, $output, $menuOptions);
    }

    /**
     * @param OutputInterface $output
     *
     * @throws \Exception
     */
    protected function printFilteredCards(OutputInterface $output)
    {
        $output->writeln('Printing filtered Cards...');
        foreach ($this->getFilteredCards() as $card)
        {
            $table = new Table($output);
            $table->setStyle('compact');
            $table->setColumnWidths([8, 50]);

            $rows = [];
            $rows[] = ['Name:', $card->getName()];

            $list = $this->getBoardListById($card->getIdList());
            $rows[] = ['List:', $list->getName()];

            $memberNames = array_map(function ($member){return $member->getFullName();}, $card->getMembers()->toArray());
            $rows[] = ['Members:', (empty($memberNames) ? '-' : implode(', ', $memberNames))];

            $rows[] = ['Last Activity: ', (empty($card->getDateLastActivity()) ? '-': $card->getDateLastActivity()->format('Y-m-d H:i:s'))];

            // TODO Add indication of dueComplete field
            $rows[] = ['Due:', (is_null($card->getDue()) ? '-' : $card->getDue()->format('Y-m-d H:i:s'))];

            $labelNames = [];
            foreach ($card->getLabels() as $label){
                $labelName = empty($label->getName()) ? ucfirst($label->getColor()) : $label->getName(); // Label name is optional
                $labelName .= ' ('.ucfirst($label->getColor()).')'; // TODO see if this can be replaced with console colours
                $labelNames[] = $labelName;
            }
            $rows[] = ['Labels:', (empty($labelNames) ? '-' : implode(', ', $labelNames))];

            $rows[] = ['URL:', $card->getShortUrl()];

            $table->setRows($rows);
            $table->render();

            $output->writeln(' ');
        }
    }

    protected function addFilter(InputInterface $input, OutputInterface $output)
    {
        // Display option of filters that can be added
        $cardFilterFactory = $this->getContainer()->get('card_filter_factory');
        try{
            $filter = $cardFilterFactory->interactiveMake($input, $output, $this->getBoardId());
            $this->getFilters()->add($filter);
            $output->writeln('Filter added');
        }catch (\Exception $e){
            $output->writeln('Filter set up interrupted: '.$e->getMessage());
        }
    }

    protected function removeFilter(InputInterface $input, OutputInterface $output)
    {
        if($this->getFilters()->isEmpty()){
            $output->writeln('There are no configured filters to remove');
            return;
        }

        // Display options (emulating ChoiceQuestion but with safer options)
        $output->writeln('Select filter to remove:');
        foreach ($this->getFilters() as $index => $filter){
            $output->writeln('  [<info>'.$index.'</info>] '.$filter::getName().' ('.$filter->getConfigDescription().')');
        }
        $output->writeln('  [<info>b</info>] <- Back');
        $question = new Question('');
        $numberFilters = $this->getFilters()->count();

        $filterIndexes = $this->getFilters()->getKeys();
        $question->setValidator(function($answer) use ($numberFilters, $filterIndexes) {
            if($answer != 'b' && !in_array($answer, $filterIndexes)){
                throw new \RuntimeException('Invalid option selected');
            }
            return $answer;
        });
        $questionHelper = $this->getHelper('question');
        $output->write(' > ');
        $selectedIndex = $questionHelper->ask($input, $output, $question);
        if($selectedIndex == 'b'){ // back option selected
            return;
        }

        $this->getFilters()->remove($selectedIndex);
        $output->writeln('Configured filter removed');
    }

    /**
     * @param ArrayCollection|Card[]                $input
     * @param ArrayCollection|CardFilterInterface[] $filters
     *
     * @return ArrayCollection|CardFilterInterface[]
     */
    protected function filterCards(ArrayCollection $input, ArrayCollection $filters)
    {
        $filteredCards = [];
        foreach($input as $card){
            foreach ($filters as $filter){
                if(!$filter->satisfiedBy($card)){
                    continue 2; // iterate outer foreach to next card
                }
            }
            // If card as passed all the filters, add it to the filtered list
            $filteredCards[] = $card;
        }
        return new ArrayCollection($filteredCards);
    }

    /**
     * @return TrelloClient
     */
    protected function getTrelloClient()
    {
        return $this->trelloClient;
    }

    /**
     * @return Card[]|ArrayCollection
     */
    protected function getBoardCards()
    {
        return $this->boardCards;
    }

    /**
     * @return Card[]|ArrayCollection
     */
    protected function getFilteredCards()
    {
        return $this->filteredCards;
    }

    /**
     * @return CardFilterInterface[]|ArrayCollection
     */
    protected function getFilters()
    {
        return $this->filters;
    }

    /**
     * @return BoardList[]|ArrayCollection
     */
    public function getBoardLists()
    {
        return $this->boardLists;
    }

    /**
     * @param $listId
     *
     * @return BoardList|mixed
     * @throws \Exception
     */
    protected function getBoardListById($listId)
    {
        foreach($this->getBoardLists() as $list){
            if($list->getId() == $listId){
                return $list;
            }
        }
        throw new \Exception('Board does not contain list with ID: '.$listId);
    }

    /**
     * @return string
     */
    protected function getBoardId()
    {
        return $this->boardId;
    }
}