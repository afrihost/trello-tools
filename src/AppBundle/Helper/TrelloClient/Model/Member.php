<?php

namespace AppBundle\Helper\TrelloClient\Model;


use JMS\Serializer\Annotation as Serializer;

class Member
{

    /**
     * The ID of the member
     *
     * @var string
     * @Serializer\Type("string")
     */
    private $id;


    /**
     * The username for the member. What is shown in '@mentions' for example
     *
     * @var string
     * @Serializer\Type("string")
     */
    private $username;

    /**
     * The full display name for the member
     *
     * @var string
     * @Serializer\Type("string")
     * @Serializer\SerializedName("fullName")
     */
    private $fullName;

    /**
     * The member's initials, used for display when there isn't an avatar set
     *
     * @var string
     * @Serializer\Type("string")
     */
    private $initials;

    /**
     * The URL of the current avatar being used, regardless of whether it is a gravatar or uploaded avatar.
     *
     * @var string
     * @Serializer\Type("string")
     * @Serializer\SerializedName("avatarUrl")
     */
    private $avatarUrl;

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
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @return string
     */
    public function getFullName()
    {
        return $this->fullName;
    }

    /**
     * @return string
     */
    public function getInitials()
    {
        return $this->initials;
    }

    /**
     * @return string
     */
    public function getAvatarUrl()
    {
        return $this->avatarUrl;
    }
}