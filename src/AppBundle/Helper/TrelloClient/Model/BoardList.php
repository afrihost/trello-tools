<?php

namespace AppBundle\Helper\TrelloClient\Model;

use JMS\Serializer\Annotation as Serializer;

/**
 * This would be called, 'List' but that's a reserved word in PHP...
 */
class BoardList
{
    /**
     * The ID of the list
     *
     * @var string
     * @Serializer\Type("string")
     */
    private $id;

    /**
     * @var string
     * @Serializer\Type("string")
     */
    private $name;

    /**
     * Whether the list is closed (archived)
     *
     * @var boolean
     * @Serializer\Type("boolean")
     */
    private $closed;

    /**
     * The position of the list on the board
     *
     * @var int
     * @Serializer\SerializedName("pos")
     * @Serializer\Type("integer")
     */
    private $position;

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return bool
     */
    public function isClosed()
    {
        return $this->closed;
    }

    /**
     * @return int
     */
    public function getPosition()
    {
        return $this->position;
    }
}