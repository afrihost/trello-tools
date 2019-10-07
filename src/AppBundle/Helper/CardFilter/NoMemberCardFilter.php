<?php

namespace AppBundle\Helper\CardFilter;


use AppBundle\Helper\TrelloClient\Model\Card;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class NoMemberCardFilter extends AbstractCardFilter
{

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     * @param string          $boardId
     *
     * @return CardFilterInterface
     */
    static public function setUp(InputInterface $input, OutputInterface $output, $boardId)
    {
        $output->writeln('No configuration of '.self::getName().' filter required');
        return new self();
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
        return $card->getMembers()->isEmpty();
    }

    /**
     * Should return a unique, static friendly name for the filter to identify it in interfaces
     *
     * @return string
     */
    static public function getName()
    {
        return 'Has No Members';
    }

    /**
     * Should return a short description (around 50 characters or less) of what this specific instantiation of the filter
     * is filtering for
     *
     * @return string
     */
    public function getConfigDescription()
    {
        return 'Any card that has no members';
    }
}