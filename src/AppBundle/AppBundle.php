<?php

namespace AppBundle;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class AppBundle extends Bundle
{
    /**
     * @var ContainerInterface
     */
    private static $containerInstance = null;

    /**
     * Overrides the parent function to store a copy of the container in a static context so that it can be used by
     * classes in the bundle that are not passed a container object
     *
     * @param ContainerInterface $container
     */
    public function setContainer(ContainerInterface $container = null){
        parent::setContainer($container);
        self::$containerInstance = $container;
    }

    /**
     * Allows classes in this bundle to retrieve the container (to allow them to use Symfony features such as
     * Dependency Injection) from a static context so that they do not need to be passed a container object
     *
     * @return ContainerInterface
     */
    public static function getContainer(){
        return self::$containerInstance;
    }
}
