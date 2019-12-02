<?php

namespace AppBundle\Helper\TrelloClient;


use AppBundle\Helper\TrelloClient\Model\Board;
use AppBundle\Helper\TrelloClient\Model\BoardList;
use AppBundle\Helper\TrelloClient\Model\Card;
use AppBundle\Helper\TrelloClient\Model\Label;
use AppBundle\Helper\TrelloClient\Model\Member;
use Doctrine\Common\Collections\ArrayCollection;
use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;
use JMS\Serializer\DeserializationContext;
use JMS\Serializer\Serializer;

class TrelloClient
{

    /**
     * @var Client
     */
    private $guzzleClient;

    /**
     * Provided for your application by Trello
     *
     * @var string
     */
    private $key;

    /**
     * API Token authorised via Trello's auth process
     *
     * @var string
     */
    private $token;


    /**
     * @var Serializer
     */
    private $serializer;

    /**
     * TrelloClient constructor.
     *
     * @param string     $key
     * @param string     $token
     * @param Serializer $serializer
     */
    public function __construct($key, $token, Serializer $serializer)
    {
        $this->key = $key;
        $this->token = $token;
        $this->serializer = $serializer;

        $this->guzzleClient = new Client([
            'base_uri' => 'https://api.trello.com/1/',
            RequestOptions::CONNECT_TIMEOUT => 10,
            RequestOptions::TIMEOUT => '30',
            RequestOptions::HTTP_ERRORS => true
        ]);
    }

    /**
     * @param $boardId
     *
     * @return Board
     */
    public function getBoard($boardId)
    {
        $queryParameters = [
            'fields' => 'id,name,shortUrl,dateLastActivity',
        ];
        return $this->makeRequest(
            'boards/'.$boardId,
            'GET',
            'AppBundle\Helper\TrelloClient\Model\Board',
            $queryParameters
        );
    }

    /**
     * @param string $boardId
     *
     * @return Card[]|ArrayCollection
     */
    public function getBoardCards($boardId)
    {
        $queryParameters = [
          'members' => true,
        ];
        return $this->makeRequest(
            'boards/'.$boardId.'/cards',
            'GET',
            'ArrayCollection<AppBundle\Helper\TrelloClient\Model\Card>',
            $queryParameters
        );
    }

    /**
     * @param $boardId
     *
     * @return BoardList[]|ArrayCollection
     */
    public function getBoardLists($boardId)
    {
        $queryParameters = [
            'cards' => 'none',
        ];
        return $this->makeRequest(
            'boards/'.$boardId.'/lists',
            'GET',
            'ArrayCollection<AppBundle\Helper\TrelloClient\Model\BoardList>',
            $queryParameters
        );
    }

    /**
     * @param $boardId
     *
     * @return Member[]|ArrayCollection
     */
    public function  getBoardMembers($boardId)
    {
        $queryParameters = [
            'fields' => 'id,avatarUrl,initials,fullName,username',
        ];
        return $this->makeRequest(
            'boards/'.$boardId.'/members',
            'GET',
            'ArrayCollection<AppBundle\Helper\TrelloClient\Model\Member>',
            $queryParameters
        );
    }

	/**
	 * @param $boardId
	 *
	 * @return Label[]\ArrayCollection
	 */
    public function getBoardLabels($boardId)
    {
	    $queryParameters = [
		    'fields' => 'all',
	    ];
	    return $this->makeRequest(
		    'boards/'.$boardId.'/labels',
		    'GET',
		    'ArrayCollection<AppBundle\Helper\TrelloClient\Model\Label>',
		    $queryParameters
	    );
    }


    protected function makeRequest($path, $method = 'GET', $expectedResponseClass = null, array $parameters = [])
    {
        // Add Auth Parameters
        $parameters['key'] = $this->getKey();
        $parameters['token'] = $this->getToken();

        $requestOptions = [
            RequestOptions::QUERY => $parameters
        ];
        $response = $this->getGuzzleClient()->request($method, $path, $requestOptions);
        $rawBody = $response->getBody()->getContents();

        // Attempt to de-serialize response into objects if an expected class has been provided
        // dump(json_decode($rawBody, true));
        if(!is_null($expectedResponseClass)){
            $deserializationContext = DeserializationContext::create()
                ->enableMaxDepthChecks();
            return $this->getSerializer()->deserialize(
                $rawBody,
                $expectedResponseClass,
                'json',
                $deserializationContext
            );
        }

        return $rawBody;
    }

    /**
     * @return Client
     */
    protected function getGuzzleClient()
    {
        return $this->guzzleClient;
    }

    /**
     * @return string
     */
    protected function getKey()
    {
        return $this->key;
    }

    /**
     * @return string
     */
    protected function getToken()
    {
        return $this->token;
    }

    /**
     * @return Serializer
     */
    protected function getSerializer()
    {
        return $this->serializer;
    }
}
