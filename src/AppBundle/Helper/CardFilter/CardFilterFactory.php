<?php

namespace AppBundle\Helper\CardFilter;


use Doctrine\Common\Collections\ArrayCollection;

class CardFilterFactory
{

    /**
     * @var string[]|ArrayCollection
     */
    private $filterClasses;

    /**
     * CardFilterFactory constructor.
     * @throws \Exception
     */
    public function __construct()
    {
        $this->filterClasses = new ArrayCollection([
            'AppBundle\Helper\CardFilter\ListCardFilter',
            'AppBundle\Helper\CardFilter\NoMemberCardFilter'
        ]);

        $this->validateFilterClasses();
    }

    /**
     * Checks that all the configured classes are valid card filters
     * @throws \Exception
     */
    protected function validateFilterClasses()
    {
        $existingFilterNames = [];
        foreach ($this->filterClasses as $class){
            if(!is_subclass_of($class, CardFilterInterface::class)){
                throw new \Exception($class.' does not implement the CardFilterInterface');
            }

            if(in_array($class::getName(), $existingFilterNames)){
                throw new \Exception('More thane one filter uses the name \''.$class::geName().'\'');
            }
            $existingFilterNames[] = $class::getName();
        }
    }

    /**
     * @return ArrayCollection|string[]
     */
    public function getFilterClasses()
    {
        return $this->filterClasses;
    }
}