<?php

namespace AppBundle\Helper\TrelloClient\Model;

use Doctrine\Common\Collections\ArrayCollection;
use JMS\Serializer\Annotation as Serializer;

class Card
{

    /**
     * The ID of the card
     *
     * @var string
     * @Serializer\Type("string")
     */
    private $id;

    /**
     * Whether the card is closed (archived). Note: Archived lists and boards do not cascade archives to cards. A
     * card can have closed: false but be on an archived board.
     *
     * @var bool
     * @Serializer\Type("bool")
     */
    private $closed;

    /**
     * The datetime of the last activity on the card.
     * Note: There are activities that update dateLastActivity that do not create a corresponding action. For instance,
     * updating the name field of a checklist item on a card does not create an action but does update the card and
     * board's dateLastActivity value.
     *
     * @var string
     * @Serializer\Type("string")
     * @Serializer\SerializedName("dateLastActivity")
     * @Serializer\Type("DateTime<'Y-m-d\TH:i:s.uT'>")
     *
     */
    private $dateLastActivity;

    /**
     * The description for the card. Up to 16384 chars.
     *
     * @var string
     * @Serializer\SerializedName("desc")
     * @Serializer\Type("string")
     */
    private $description;

    /**
     * The due date on the card, if one exists
     *
     * @var \DateTime
     * @Serializer\Type("DateTime<'Y-m-d\TH:i:s.uT'>")
     */
    private $due;

    /**
     * Whether the due date has been marked complete
     *
     * @var bool
     * @Serializer\Type("boolean")
     * @Serializer\SerializedName("dueComplete")
     */
    private $dueComplete;

    /**
     * Array of label objects on this card
     *
     * @var ArrayCollection|Label[]
     * @Serializer\Type("ArrayCollection<AppBundle\Helper\TrelloClient\Model\Label>")
     */
    private $labels;

    /**
     * Name of the card
     *
     * @var string
     * @Serializer\Type("string")
     */
    private $name;

    /**
     * Position of the card in the list
     *
     * @var int
     * @Serializer\SerializedName("pos")
     * @Serializer\Type("integer")
     */
    private $position;

    /**
     * URL to the card without the name slug
     *
     * @var string
     * @Serializer\Type("string")
     * @Serializer\SerializedName("shortUrl")
     */
    private $shortUrl;

    /**
     * @var ArrayCollection|Member[]
     * @Serializer\Type("ArrayCollection<AppBundle\Helper\TrelloClient\Model\Member>")
     */
    private $members;

    /**
     * Card constructor.
     */
    public function __construct()
    {
        $this->labels = new ArrayCollection();
        $this->members = new ArrayCollection();
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return bool
     */
    public function isClosed()
    {
        return $this->closed;
    }

    /**
     * @return string
     */
    public function getDateLastActivity()
    {
        return $this->dateLastActivity;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @return \DateTime
     */
    public function getDue()
    {
        return $this->due;
    }

    /**
     * @return bool
     */
    public function isDueComplete()
    {
        return $this->dueComplete;
    }

    /**
     * @return Label[]|ArrayCollection
     */
    public function getLabels()
    {
        return $this->labels;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return int
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * @return string
     */
    public function getShortUrl()
    {
        return $this->shortUrl;
    }

    /**
     * @return Member[]|ArrayCollection
     */
    public function getMembers()
    {
        return $this->members;
    }

    public function deserializeDateLastActivity($raw)
    {
        $this->dateLastActivity = \DateTime::createFromFormat(
            'Y-m-d\TH:i:s.uT',
            $raw
        );
    }
}