<?php

namespace AppBundle\Helper\CardFilter;


use AppBundle\Helper\TrelloClient\Model\Card;
use AppBundle\Helper\TrelloClient\Model\Member;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;

class MemberCardFilter extends AbstractCardFilter
{
    /**
     * @var Member
     */
    private $targetMember;

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
        $boardMembers = $trelloClient->getBoardMembers($boardId);

        $options = [];
        foreach($boardMembers as $boardMember ){
            $options[$boardMember->getFullName()] = $boardMember;
        }
        $filterOptionQuestion = new ChoiceQuestion(
            'Select Member to filter by:',
            array_merge(array_keys($options), ['<- Back'])
        );
        $questionHelper = new QuestionHelper();
        $selectedMemberName = $questionHelper->ask($input, $output, $filterOptionQuestion);
        if($selectedMemberName == '<- Back'){
            throw new \Exception('Back option selected');
        }

        $filter = new self();
        $filter->targetMember =  $options[$selectedMemberName];

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
        foreach ($card->getMembers() as $cardMember){
            if ($cardMember->getId() == $this->getTargetMember()->getId()){
                return true;
            }
        }
        return false;
    }

    /**
     * Should return a unique, static friendly name for the filter to identify it in interfaces
     *
     * @return string
     */
    static public function getName()
    {
        return 'Has Member';
    }

    /**
     * Should return a short description (around 50 characters or less) of what this specific instantiation of the filter
     * is filtering for
     *
     * @return string
     */
    public function getConfigDescription()
    {
        return 'Member: '.$this->getTargetMember()->getFullName();
    }

    /**
     * @return Member
     */
    protected function getTargetMember(): Member
    {
        return $this->targetMember;
    }
}