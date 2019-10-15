<?php

namespace AppBundle\Helper\TrelloClient\Model;


use JMS\Serializer\Annotation as Serializer;

class Board
{
    /**
     * The ID of the board
     *
     * @var string
     * @Serializer\Type("string")
     */
    private $id;

    /**
     * The name of the board
     *
     * @var string
     * @Serializer\Type("string")
     */
    private $name;

    /**
     * URL for the board using only its shortMongoID
     *
     * @var string
     * @Serializer\Type("string")
     * @Serializer\SerializedName("shortUrl")
     */
    private $shortUrl;

    /**
     *
     * @var \DateTime
     * @Serializer\Type("DateTime<'Y-m-d\TH:i:s.uT'>")
     * @Serializer\SerializedName("dateLastActivity")
     */
    private $dateLastActivity;

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getShortUrl(): string
    {
        return $this->shortUrl;
    }

    /**
     * @return \DateTime
     */
    public function getDateLastActivity(): \DateTime
    {
        return $this->dateLastActivity;
    }
}