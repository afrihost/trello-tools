<?php

namespace AppBundle\Helper\CardFilter;


use AppBundle\Helper\TrelloClient\Model\Card;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

interface CardFilterInterface
{
    /**
     * Initialization of the filter should be implemented in this function. This may include asking the user for input.
     * If setup needs to be cancelled, then an exception can be thrown for the filter not to be created
     *
     * @param InputInterface  $input
     * @param OutputInterface $output
     * @param string          $boardId
     *
     * @return CardFilterInterface
     */
    static public function setUp(InputInterface $input, OutputInterface $output, $boardId);

    /**
     * Should return TRUE if the provided card passes this filter and FALSE otherwise
     *
     * @param Card $card
     *
     * @return boolean
     */
    public function satisfiedBy(Card $card);

    /**
     * Should return a unique, static friendly name for the filter to identify it in interfaces
     *
     * @return string
     */
    static public function getName();

    /**
     * Should return a short description (around 50 characters or less) of what this specific instantiation of the filter
     * is filtering for
     *
     * @return string
     */
    public function getConfigDescription();
}