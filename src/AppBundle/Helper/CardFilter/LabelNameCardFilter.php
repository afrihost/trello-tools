<?php


namespace AppBundle\Helper\CardFilter;


use AppBundle\Helper\TrelloClient\Model\Card;
use AppBundle\Helper\TrelloClient\Model\Label;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;

class LabelNameCardFilter extends AbstractCardFilter
{
	/**
	 * @var Label
	 */
	private $targetLabel;

	/**
	 * Initialization of the filter should be implemented in this function. This may include asking the user for input.
	 * If setup needs to be cancelled, then an exception can be thrown for the filter not to be created
	 *
	 * @param InputInterface $input
	 * @param OutputInterface $output
	 * @param string $boardId
	 *
	 * @return CardFilterInterface
	 * @throws \Exception
	 */
	static public function setUp(InputInterface $input, OutputInterface $output, $boardId)
	{
		$trelloClient = self::getContainer()->get('trello_client');

		$boardLabels = $trelloClient->getBoardLabels($boardId);
		$options = [];
		$count = 1;
		foreach($boardLabels as $boardLabel ){
			$labelName = $boardLabel->getName();
			if ( isset($options[ $boardLabel->getName() ]) && ( isset( $labelName ) && !empty( $boardLabel->getName()) ) )
			{
				$options[$count.' '.$boardLabel->getName() ] = $boardLabel;
			}
			elseif( isset( $labelName ) && !empty( $boardLabel->getName()) )
			{
				$options[$count.' '.$boardLabel->getName()] = $boardLabel;
			}
			$count ++;
			 //$options[$boardLabel->getId()] = $boardLabel;
		}
		$filterOptionQuestion = new ChoiceQuestion(
			'Select Label to filter by:',
			array_merge(array_keys($options), ['<- Back'])
		);
		$questionHelper = new QuestionHelper();
		$selectedLabelName = $questionHelper->ask($input, $output, $filterOptionQuestion);
		if($selectedLabelName == '<- Back'){
			throw new \Exception('Back option selected');
		}

		$filter = new self();
		$filter->targetLabel =  $options[$selectedLabelName];

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
			if ($cardLabel->getId() == $this->getTargetLabel()->getId()){
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
		return 'Has Label';
	}

	/**
	 * Should return a short description (around 50 characters or less) of what this specific instantiation of the filter
	 * is filtering for
	 *
	 * @return string
	 */
	public function getConfigDescription()
	{
		return 'Label: '.$this->getTargetLabel()->getName();
	}

	private function getTargetLabel()
	{
		return $this->targetLabel;
	}
}
