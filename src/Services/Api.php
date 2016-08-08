<?php

namespace Bitbucket\Services;

use Bitbucket\Services\Http\Authentication\Basic;
use Bitbucket\Services\Http\Client;

class Api
{

    /**
     * @var Client
     */
    protected $httpClient;

    /**
     * Authentication object
     * @var Basic
     */
    protected $auth;

    /**
     * Api constructor.
     */
    public function __construct()
    {
        $this->httpClient = new Client();
    }

    /**
     * @param Basic $basicAuthentication
     */
    public function setCredentials(Basic $basicAuthentication)
    {
        $this->auth = $basicAuthentication;
        $this->httpClient->addOptions([
            'auth' => [
                $basicAuthentication->getUsername(),
                $basicAuthentication->getPassword()
            ]
        ]);
    }

    /**
     * @return Basic
     */
    public function getCredentials()
    {
        return $this->auth;
    }

    /**
     * @access public
     * @return Client
     */
    public function getClient()
    {
        return $this->httpClient;
    }

    /**
     * @param $endpoint
     * @param array $params
     * @return mixed|\Psr\Http\Message\ResponseInterface
     */
    public function requestGet($endpoint, $params = array())
    {
        return $this->getClient()->get($endpoint, $params);
    }

    /**
     * @param $endpoint
     * @param array $params
     * @return mixed|\Psr\Http\Message\ResponseInterface
     */
    public function requestPost($endpoint, $params = array())
    {
        return $this->getClient()->post($endpoint, $params);
    }

    /**
     * @param $endpoint
     * @param array $params
     * @return mixed|\Psr\Http\Message\ResponseInterface
     */
    public function requestPut($endpoint, $params = array())
    {
        return $this->getClient()->put($endpoint, $params);
    }

    /**
     * @param $endpoint
     * @param array $params
     * @return mixed|\Psr\Http\Message\ResponseInterface
     */
    public function requestDelete($endpoint, $params = array())
    {
        return $this->getClient()->delete($endpoint, $params);
    }

    /**
     * @param $method
     * @param $endpoint
     * @param $params
     * @return mixed|\Psr\Http\Message\ResponseInterface
     */
    protected function doRequest($method, $endpoint, $params)
    {
        return $this->getClient()->request($endpoint, $params, $method);
    }

    /**
     * Convert JSON to array with error check
     *
     * @access protected
     * @param  string $body JSON data
     * @return array
     *
     * @throws \InvalidArgumentException
     */
    protected function decodeJSON($body)
    {
        $params = json_decode($body, true);

        if (!is_array($params) || (JSON_ERROR_NONE !== json_last_error())) {
            throw new \InvalidArgumentException('Invalid JSON data provided.');
        }

        return $params;
    }
}
