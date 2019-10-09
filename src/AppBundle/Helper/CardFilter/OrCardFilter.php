<?php

namespace AppBundle\Helper\CardFilter;


use AppBundle\Helper\TrelloClient\Model\Card;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;

class OrCardFilter extends AbstractCardFilter
{

    /**
     * @var CardFilterInterface[]|ArrayCollection
     */
    private $wrappedFilters;

    /**
     * OrCardFilter constructor.
     */
    public function __construct()
    {
        $this->wrappedFilters = new ArrayCollection();
    }


    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     * @param string          $boardId
     *
     * @return CardFilterInterface
     * @throws \Exception
     */
    static public function setUp(InputInterface $input, OutputInterface $output, $boardId)
    {
        $cardFilterFactory = self::getContainer()->get('card_filter_factory');
        $filter = new self();

        while(true){
            $output->writeln('Choose a Filter to be logically ORed with other filters');

            try{
                $filter->wrappedFilters->add($cardFilterFactory->interactiveMake($input, $output, $boardId));
            } catch (\Exception $e){
                $output->writeln('OR Filter set up interrupted: '.$e->getMessage());
            }

            $exitQuestion = new ChoiceQuestion(
                'Would you like to add another Filter to logically OR?',
                ['y', 'n']
            );
            $questionHelper = new QuestionHelper();
            $selection = $questionHelper->ask($input, $output, $exitQuestion);
            if ($selection == 'n'){
                return $filter;
            }
        }
        return $filter;
    }

    /**
     * Should return TRUE if the provided card passes this filter and FALSE otherwise
     *
     * @param Card $card
     *
     * @return boolean
     */
    public function satisfiedBy(Card $card)
    {
        foreach ($this->getWrappedFilters() as $candidateFilter){
            if($candidateFilter->satisfiedBy($card)){
                return true;
            }
        }
        return false; // failed all filters
    }

    /**
     * Should return a unique, static friendly name for the filter to identify it in interfaces
     *
     * @return string
     */
    static public function getName()
    {
        return 'OR';
    }

    /**
     * Should return a short description (around 50 characters or less) of what this specific instantiation of the filter
     * is filtering for
     *
     * @return string
     */
    public function getConfigDescription()
    {
        $filterDescriptions = [];
        foreach ($this->getWrappedFilters() as $candidateFilter){
            $filterDescriptions[] = '('.$candidateFilter->getConfigDescription().')';
        }
        return implode(' OR ', $filterDescriptions);
    }

    /**
     * @return CardFilterInterface[]|ArrayCollection
     */
    protected function getWrappedFilters()
    {
        return $this->wrappedFilters;
    }
}