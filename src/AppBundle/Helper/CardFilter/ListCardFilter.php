<?php

namespace AppBundle\Helper\CardFilter;


use AppBundle\Helper\TrelloClient\Model\BoardList;
use AppBundle\Helper\TrelloClient\Model\Card;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;

class ListCardFilter extends AbstractCardFilter
{

    /**
     * @var BoardList
     */
    private $targetList;

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
        $trelloClient = self::getContainer()->get('trello_client');
        $lists = $trelloClient->getBoardLists($boardId);

        $listOptions = [];
        foreach($lists as  $list ){
            $listOptions[$list->getName()] = $list;
        }
        $filterOptionQuestion = new ChoiceQuestion(
            'Select List to filter by:',
            array_merge(array_keys($listOptions), ['<- Back'])
        );
        $questionHelper = new QuestionHelper();
        $selectedListName = $questionHelper->ask($input, $output, $filterOptionQuestion);
        if($selectedListName == '<- Back'){
            throw new \Exception('Back option selected');
        }

        $filter = new self();
        $filter->targetList =  $listOptions[$selectedListName];

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
        return $card->getIdList() == $this->getTargetList()->getId();
    }

    /**
     * Should return a unique, static friendly name for the filter to identify it in interfaces
     *
     * @return string
     */
    static public function getName()
    {
        return 'In List';
    }

    /**
     * Should return a short description (around 50 characters or less) of what this specific instantiation of the filter
     * is filtering for
     *
     * @return string
     */
    public function getConfigDescription()
    {
        return 'List Name: '.$this->getTargetList()->getName();
    }

    /**
     * @return BoardList
     */
    protected function getTargetList()
    {
        return $this->targetList;
    }
}