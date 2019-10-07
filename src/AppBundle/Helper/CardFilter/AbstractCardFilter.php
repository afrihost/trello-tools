<?php
namespace AppBundle\Helper\CardFilter;

use AppBundle\AppBundle;
use Psr\Container\ContainerInterface;

abstract class AbstractCardFilter implements CardFilterInterface
{

    /**
     * @return ContainerInterface
     */
    static protected function getContainer()
    {
        return AppBundle::getContainer();
    }
}