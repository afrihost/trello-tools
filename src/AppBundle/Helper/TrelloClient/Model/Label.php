<?php

namespace AppBundle\Helper\TrelloClient\Model;


use JMS\Serializer\Annotation as Serializer;

class Label
{
    /**
     * The ID of the label
     *
     * @var string
     * @Serializer\Type("string")
     */
    private $id;

    /**
     * The optional name of the label (0 - 16384 chars)
     *
     * @var string
     * @Serializer\Type("string")
     */
    private $name;

    /**
     * The color of the label. One of: yellow, purple, blue, red, green, orange, black, sky, pink, lime, null
     * (null means no color, and the label will not show on the front of cards)
     *
     * @var string
     * @Serializer\Type("string")
     */
    private $color;

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
     * @return string
     */
    public function getColor()
    {
        return $this->color;
    }
}