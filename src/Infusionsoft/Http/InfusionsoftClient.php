<?php

namespace Infusionsoft\Http;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use Psr\Http\Message\UriInterface;
use GuzzleHttp\Exception\GuzzleException;
use fXmlRpc\Transport\HttpAdapterTransport;
use GuzzleHttp\Exception\BadResponseException;
use Http\Adapter\Guzzle7\Client as AdapterClient;
use Http\Message\MessageFactory\DiactorosMessageFactory;

/**
 * Class InfusionsoftClient
 *
 * @package Infusionsoft\Http
 */
class InfusionsoftClient implements ClientInterface
{

    /** @var Client */
    public $client;

    /**
     * InfusionsoftClient constructor.
     */
    public function __construct()
    {
        $this->client = new Client();
    }


    /**
     * @return \fXmlRpc\Transport\TransportInterface
     */
    public function getXmlRpcTransport()
    {
        return new HttpAdapterTransport(
            new DiactorosMessageFactory(),
            new AdapterClient($this->client)
        );
    }

    /**
     * Sends a request to the given URI and returns the raw response.
     *
     * @param string              $method HTTP method.
     * @param string|UriInterface $uri URI object or string.
     * @param array               $options Request options to apply. See \GuzzleHttp\RequestOptions.
     *
     * @throws GuzzleException
     */
    public function request($method, $uri = '', array $options = [])
    {
        if ( ! isset($options['headers'])) {
            $options['headers'] = [];
        }

        if ( ! isset($options['body'])) {
            $options['body'] = null;
        }

        try {
            $request = new Request($method, $uri, $options['headers'], $options['body']);

            $response = $this->client->send($request);

            return $response->getBody()->getContents();
        } catch (BadResponseException $e) {
            throw new HttpException($e->getMessage(), $e->getCode(), $e);
        }
    }
}
