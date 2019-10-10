<?php

namespace AppBundle\Helper\CardFilter;


use AppBundle\Helper\TrelloClient\Model\Card;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

class ActivitySinceCardFilter extends AbstractCardFilter
{

    /**
     * @var \DateTime
     */
    private $targetDate;

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     * @param string          $boardId
     *
     * @return CardFilterInterface
     */
    static public function setUp(InputInterface $input, OutputInterface $output, $boardId)
    {
        $question = new Question('Provide date to consider activity since (any value supported by strtotime() is supported): ');
        $questionHelper = new QuestionHelper();
        $dateString = $questionHelper->ask($input, $output, $question);

        $filter = new self();
        $filter->targetDate = new \DateTime($dateString);
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
        return ($card->getDateLastActivity() >= $this->getTargetDate());
    }

    /**
     * Should return a unique, static friendly name for the filter to identify it in interfaces
     *
     * @return string
     */
    static public function getName()
    {
        return 'Activity Since';
    }

    /**
     * Should return a short description (around 50 characters or less) of what this specific instantiation of the filter
     * is filtering for
     *
     * @return string
     */
    public function getConfigDescription()
    {
        return 'Since: '.$this->getTargetDate()->format('Y-m-d H:i:s');
    }

    /**
     * @return \DateTime
     */
    protected function getTargetDate(): \DateTime
    {
        return $this->targetDate;
    }
}