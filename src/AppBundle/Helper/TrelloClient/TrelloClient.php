<?php

namespace AppBundle\Helper\TrelloClient;


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