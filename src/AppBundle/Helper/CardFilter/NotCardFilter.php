<?php

namespace AppBundle\Helper\CardFilter;


use AppBundle\Helper\TrelloClient\Model\Card;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;

class NotCardFilter extends AbstractCardFilter
{

    /**
     * @var CardFilterInterface
     */
    private $wrappedFilter;

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

        $output->writeln('Another filter must be chosen to be negated by this filter');
        $filter = new self();
        $filter->wrappedFilter = $cardFilterFactory->interactiveMake($input, $output, $boardId);
        $output->writeln($filter->wrappedFilter::getName().' filter wrapped with a negation');
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
        return !$this->getWrappedFilter()->satisfiedBy($card);
    }

    /**
     * Should return a unique, static friendly name for the filter to identify it in interfaces
     *
     * @return string
     */
    static public function getName()
    {
        return 'Not';
    }

    /**
     * Should return a short description (around 50 characters or less) of what this specific instantiation of the filter
     * is filtering for
     *
     * @return string
     */
    public function getConfigDescription()
    {
        return 'NOT( '.$this->getWrappedFilter()->getConfigDescription().' )';
    }

    /**
     * @return CardFilterInterface
     */
    protected function getWrappedFilter()
    {
        return $this->wrappedFilter;
    }
}