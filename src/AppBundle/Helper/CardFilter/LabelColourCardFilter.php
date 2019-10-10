<?php

namespace AppBundle\Helper\CardFilter;


use AppBundle\Helper\TrelloClient\Model\Card;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;

class LabelColourCardFilter extends AbstractCardFilter
{

    /**
     * @var string
     */
    private $targetColour;

    /**
     *
     * @param InputInterface  $input
     * @param OutputInterface $output
     * @param string          $boardId
     *
     * @return CardFilterInterface
     * @throws \Exception
     */
    static public function setUp(InputInterface $input, OutputInterface $output, $boardId)
    {
        $options = [
            'black' => 'black',
            'blue' => 'blue',
            'green' => 'green',
            'lime' => 'lime',
            'orange' => 'orange',
            'pink' => 'pink',
            'purple' => 'purple',
            'red' => 'red',
            'sky' => 'sky',
            'yellow' => 'yellow',
        ];
        $filterOptionQuestion = new ChoiceQuestion(
            'Select Label Colour to filter by:',
            array_merge(array_keys($options), ['<- Back'])
        );
        $questionHelper = new QuestionHelper();
        $selectedColour = $questionHelper->ask($input, $output, $filterOptionQuestion);
        if($selectedColour == '<- Back'){
            throw new \Exception('Back option selected');
        }

        $filter = new self();
        $filter->targetColour =  $selectedColour;
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
        foreach ($card->getLabels() as $cardLabel){
            if($cardLabel->getColor() == $this->getTargetColour()){
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
        return 'Label Colour';
    }

    /**
     * Should return a short description (around 50 characters or less) of what this specific instantiation of the filter
     * is filtering for
     *
     * @return string
     */
    public function getConfigDescription()
    {
        return 'Colour: '.ucfirst($this->getTargetColour());
    }

    /**
     * @return string
     */
    public function getTargetColour(): string
    {
        return $this->targetColour;
    }
}