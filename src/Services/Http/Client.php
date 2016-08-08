<?php

namespace Bitbucket\Services\Http;

use GuzzleHttp\Client as GuzzleClient;
use Psr\Http\Message\ResponseInterface;

class Client
{
    /**
     * Api response codes.
     */
    const HTTP_RESPONSE_OK              = 200;
    const HTTP_RESPONSE_CREATED         = 201;
    const HTTP_RESPONSE_NO_CONTENT      = 204;
    const HTTP_RESPONSE_BAD_REQUEST     = 400;
    const HTTP_RESPONSE_UNAUTHORIZED    = 401;
    const HTTP_RESPONSE_FORBIDDEN       = 403;
    const HTTP_RESPONSE_NOT_FOUND       = 404;

    const ENDPOINT = 'https://api.bitbucket.org/2.0';

    const FORMAT = 'json';

    /**
     * @var array
     */
    protected $options = [
        'verify' => false,
    ];

    /**
     * @var GuzzleClient
     */
    protected $client;

    /**
     * Client constructor.
     */
    public function __construct()
    {
        $this->client = new GuzzleClient();
    }

    /**
     * @param array $options
     */
    public function addOptions(array $options)
    {
        $this->options += array_merge($this->options, $options);
    }

    /**
     * {@inheritDoc}
     */
    public function get($endpoint, $params = array())
    {
        if (is_array($params) && count($params) > 0) {
            $endpoint .= (strpos($endpoint, '?') === false ? '?' : '&').http_build_query($params, '', '&');
            $params = array();
        }

        return $this->request($endpoint, $params, 'GET');
    }

    /**
     * {@inheritDoc}
     */
    public function post($endpoint, $params = array())
    {
        return $this->request($endpoint, $params, 'POST');
    }

    /**
     * {@inheritDoc}
     */
    public function put($endpoint, $params = array())
    {
        return $this->request($endpoint, $params, 'PUT');
    }

    /**
     * {@inheritDoc}
     */
    public function delete($endpoint, $params = array())
    {
        return $this->request($endpoint, $params, 'DELETE');
    }

    /**
     * {@inheritDoc}
     */
    public function request($endpoint, $params = array(), $method = 'GET')
    {
        $url = $this->getApiBaseUrl().'/'.$endpoint;

        // change the response format
        if (strpos($url, 'format=') === false) {
            $url .= (strpos($url, '?') === false ? '?' : '&').'format='.$this->getResponseFormat();
        }

        // add a default content-type if none was set
        if (in_array(strtoupper($method), array('POST', 'PUT')) && empty($headers['Content-Type'])) {
            $this->addOptions([
                'Content-Type' => 'application/x-www-form-urlencoded'
            ]);
        }

        if (!empty($params)) {
            $this->addOptions([
                'form_params' => $params
            ]);
        }

        $response = $this->client->request($method, $url, $this->options);

        return $this->processResponse($response);
    }

    /**
     * @return GuzzleClient
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * @return string
     */
    public function getResponseFormat()
    {
        return self::FORMAT;
    }

    /**
     * {@inheritDoc}
     */
    public function getApiBaseUrl()
    {
        return self::ENDPOINT;
    }

    protected function processResponse(ResponseInterface $response)
    {
        switch ($response->getStatusCode()) {
            case self::HTTP_RESPONSE_OK:
            case self::HTTP_RESPONSE_CREATED:
                return $response->getBody()->getContents();
                break;

            case self::HTTP_RESPONSE_NO_CONTENT:
                return true;
                break;

            case self::HTTP_RESPONSE_BAD_REQUEST:
                return $response;

            case self::HTTP_RESPONSE_UNAUTHORIZED:
                throw new \Exception("Unauthorized: Authentication required");
                break;

            case self::HTTP_RESPONSE_FORBIDDEN:
                throw new \Exception("Not enough permissions.");
                break;

            case self::HTTP_RESPONSE_NOT_FOUND:
                return false;
                break;

            default:
                return $response;
                break;
        }
    }
}
