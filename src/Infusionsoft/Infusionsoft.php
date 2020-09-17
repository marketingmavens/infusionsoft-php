<?php

namespace Infusionsoft;

use DateTime;
use DateTimeZone;
use UnexpectedValueException;

/**
 * Class Infusionsoft
 *
 * @package Infusionsoft
 */
class Infusionsoft
{

    /**
     * @var string URL all XML-RPC requests are sent to
     */
    protected $url = 'https://api.infusionsoft.com/crm/xmlrpc/v1';

    /**
     * @var string URL a user visits to authorize an access token
     */
    protected $auth = 'https://signin.infusionsoft.com/app/oauth/authorize';

    /**
     * @var string Base URL of all API requests
     */
    protected $baseUri = 'https://api.infusionsoft.com/crm';

    /**
     * @var string URL used to request an access token
     */
    protected $tokenUri = 'https://api.infusionsoft.com/token';

    /**
     * @var string
     */
    protected $clientId;

    /**
     * @var string
     */
    protected $clientSecret;

    /**
     * @var string
     */
    protected $redirectUri;

    /**
     * @var array Cache for services so they aren't created multiple times
     */
    protected $apis = [];

    /**
     * @var Http\ClientInterface
     */
    protected $httpClient;

    /**
     * @var Http\SerializerInterface
     */
    protected $serializer;

    /**
     * @var boolean
     */
    public $needsEmptyKey = true;

    /**
     * @var Token
     */
    protected $token;

    /**
     * @param array $config
     */
    public function __construct($config = [])
    {
        if (isset($config['client_id'])) {
            $this->clientId = $config['client_id'];
        }

        if (isset($config['client_secret'])) {
            $this->clientSecret = $config['client_secret'];
        }

        if (isset($config['redirect_uri'])) {
            $this->redirectUri = $config['redirect_uri'];
        }
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @param string $url
     *
     * @return string
     */
    public function setUrl(string $url)
    {
        $this->url = $url;

        return $this;
    }

    /**
     * @return string
     */
    public function getBaseUrl()
    {
        return $this->baseUri;
    }

    /**
     * @param $url
     *
     * @return $this
     */
    public function setBaseUrl($url)
    {
        $this->baseUri = $url;

        return $this;
    }

    /**
     * @return string
     */
    public function getAuth()
    {
        return $this->auth;
    }

    /**
     * @param string $auth
     *
     * @return string
     */
    public function setAuth(string $auth)
    {
        $this->auth = $auth;

        return $this;
    }

    /**
     * @return string
     */
    public function getTokenUri()
    {
        return $this->tokenUri;
    }

    /**
     * @param string $tokenUri
     */
    public function setTokenUri($tokenUri)
    {
        $this->tokenUri = $tokenUri;
    }

    /**
     * @return string
     */
    public function getClientId()
    {
        return $this->clientId;
    }

    /**
     * @param string $clientId
     *
     * @return string
     */
    public function setClientId(string $clientId)
    {
        $this->clientId = $clientId;

        return $this;
    }

    /**
     * @return string
     */
    public function getClientSecret()
    {
        return $this->clientSecret;
    }

    /**
     * @param string $clientSecret
     *
     * @return string
     */
    public function setClientSecret($clientSecret)
    {
        $this->clientSecret = $clientSecret;

        return $this;
    }

    /**
     * @return string
     */
    public function getRedirectUri()
    {
        return $this->redirectUri;
    }

    /**
     * @param string $redirectUri
     *
     * @return string
     */
    public function setRedirectUri($redirectUri)
    {
        $this->redirectUri = $redirectUri;

        return $this;
    }

    /**
     * @return Http\ClientInterface
     */
    public function getHttpClient()
    {
        if ( ! $this->httpClient) {
            return new Http\InfusionsoftClient();
        }

        return $this->httpClient;
    }

    /**
     * @param Token $token
     */
    public function setToken($token)
    {
        $this->token = $token;
    }

    /**
     * @return Token
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * @return string
     */
    public function getAuthorizationUrl($state = null)
    {
        $params = [
            'client_id'     => $this->clientId,
            'redirect_uri'  => $this->redirectUri,
            'response_type' => 'code',
            'scope'         => 'full'
        ];

        if ( ! is_null($state) && $state !== null && is_string($state)) {
            $params['state'] = (string)$state;
        }

        return $this->auth . '?' . http_build_query($params);
    }

    /**
     * @param string $code
     *
     * @return array
     * @throws InfusionsoftException
     */
    public function requestAccessToken($code)
    {
        $params = [
            'client_id'     => $this->clientId,
            'client_secret' => $this->clientSecret,
            'code'          => $code,
            'grant_type'    => 'authorization_code',
            'redirect_uri'  => $this->redirectUri,
        ];

        $client = $this->getHttpClient();

        $tokenInfo = $client->request('POST', $this->tokenUri, [
            'body'    => http_build_query($params),
            'headers' => ['Content-Type' => 'application/x-www-form-urlencoded']
        ]);

        $this->setToken(new Token(json_decode($tokenInfo, true)));

        return $this->getToken();
    }

    /**
     * @return array
     * @throws InfusionsoftException
     */
    public function refreshAccessToken()
    {
        $headers = [
            'Authorization' => 'Basic ' . base64_encode($this->clientId . ':' . $this->clientSecret),
            'Content-Type'  => 'application/x-www-form-urlencoded'
        ];

        $params = [
            'grant_type'    => 'refresh_token',
            'refresh_token' => $this->getToken()->getRefreshToken(),
        ];

        $client = $this->getHttpClient();

        $tokenInfo = $client->request('POST', $this->tokenUri, [
            'body'    => http_build_query($params),
            'headers' => $headers
        ]);

        $this->setToken(new Token(json_decode($tokenInfo, true)));

        return $this->getToken();
    }

    /**
     * @param Http\ClientInterface $client
     */
    public function setHttpClient($client)
    {
        $this->httpClient = $client;
    }

    /**
     * @return Http\SerializerInterface
     */
    public function getSerializer()
    {
        if ( ! $this->serializer) {
            return new Http\InfusionsoftSerializer();
        }

        return $this->serializer;
    }

    /**
     * @param Http\SerializerInterface $serializer
     */
    public function setSerializer(Http\SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
    }

    /**
     * Checks if the current token is null or expired
     *
     * @return boolean
     */
    public function isTokenExpired()
    {
        $token = $this->getToken();

        if ( ! is_object($token)) {
            return true;
        }

        return $token->isExpired();
    }

    /**
     * @return mixed
     * @throws InfusionsoftException
     */
    public function request()
    {
        // Before making the request, we can make sure that the token is still
        // valid by doing a check on the end of life.
        $token = $this->getToken();
        if ($this->isTokenExpired()) {
            throw new TokenExpiredException;
        }

        $url = $this->url . '?' . http_build_query(['access_token' => $token->getAccessToken()]);

        $params = func_get_args();
        $method = array_shift($params);

        // Some older methods in the API require a key parameter to be sent
        // even if OAuth is being used. This flag can be made false as it
        // will break some newer endpoints.
        if ($this->needsEmptyKey) {
            $params = array_merge(['key' => $token->getAccessToken()], $params);
        }

        // Reset the empty key flag back to the default for the next request
        $this->needsEmptyKey = true;

        $client = $this->getSerializer();
        $response = $client->request($method, $url, $params, $this->getHttpClient());

        return $response;
    }

    /**
     * @param string $method
     * @param string $url
     * @param array  $params
     *
     * @return mixed
     * @throws TokenExpiredException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function restfulRequest($method, $url, $params = [])
    {
        // Before making the request, we can make sure that the token is still
        // valid by doing a check on the end of life.
        $token = $this->getToken();
        if ($this->isTokenExpired()) {
            throw new TokenExpiredException;
        }

        $client = $this->getHttpClient();
        $full_params = [];

        if (strtolower($method) === 'get' || strtolower($method) === 'delete') {
            $params = array_merge(['access_token' => $token->getAccessToken()], $params);
            $url = $url . '?' . http_build_query($params);
        } else {
            $url = $url . '?' . http_build_query(['access_token' => $token->getAccessToken()]);
            $full_params['body'] = json_encode($params);
        }

        $full_params['headers'] = [
            'Content-Type' => 'application/json',
        ];

        $response = (string)$client->request($method, $url, $full_params);

        return json_decode($response, true);
    }

    /**
     * @param DateTime|string $datetime
     *
     * @return string
     * @throws \Exception
     */
    public function formatDate($datetime = 'now')
    {
        if ( ! $datetime instanceof DateTime) {
            $datetime = new DateTime($datetime, new DateTimeZone('America/New_York'));
        }

        return $datetime->format('Y-m-d\TH:i:s');
    }

    /**
     * @param $name
     *
     * @return mixed
     * @throws \UnexpectedValueException
     */
    public function __get($name)
    {
        $services = [
            'affiliatePrograms',
            'affiliates',
            'contacts',
            'data',
            'discounts',
            'emails',
            'files',
            'funnels',
            'invoices',
            'merchants',
            'orders',
            'products',
            'search',
            'tags',
            'shipping',
            'webForms',
            'webTracking'
        ];

        if (method_exists($this, $name) and in_array($name, $services)) {
            return $this->{$name}();
        }

        throw new UnexpectedValueException(sprintf('Invalid property: %s', $name));
    }

    /**
     * @return \Infusionsoft\Api\AffiliateProgramService
     */
    public function affiliatePrograms()
    {
        return $this->getApi('AffiliateProgramService');
    }

    /**
     * @return \Infusionsoft\Api\AffiliateService
     */
    public function affiliates()
    {
        return $this->getApi('AffiliateService');
    }

    /**
     * @param string $api
     *
     * @return \Infusionsoft\Api\ContactService | \Infusionsoft\Api\Rest\ContactService
     */
    public function contacts($api = 'rest')
    {
        if ($api == 'xml') {
            return $this->getApi('ContactService');
        }

        return $this->getRestApi('ContactService');
    }

    /**
     * @return \Infusionsoft\Api\DataService
     */
    public function data()
    {
        return $this->getApi('DataService');
    }

    /**
     * @return \Infusionsoft\Api\DiscountService
     */
    public function discounts()
    {
        return $this->getApi('DiscountService');
    }

    /**
     * @return \Infusionsoft\Api\CreditCardSubmissionService
     */
    public function creditCards()
    {
        return $this->getApi('CreditCardSubmissionService');
    }

    /**
     * @param string $api
     *
     * @return \Infusionsoft\Api\APIEmailService | \Infusionsoft\Api\Rest\EmailService
     */
    public function emails($api = 'rest')
    {
        if ($api == 'xml') {
            return $this->getApi('APIEmailService');
        }

        return $this->getRestApi('EmailService');
    }

    /**
     * @param string $api
     *
     * @return \Infusionsoft\Api\FileService | \Infusionsoft\Api\Rest\FileService
     */
    public function files($api = 'rest')
    {
        if ($api == 'xml') {
            return $this->getApi('FileService');
        }

        return $this->getRestApi('FileService');
    }

    /**
     * @return \Infusionsoft\Api\FunnelService
     */
    public function funnels()
    {
        return $this->getApi('FunnelService');
    }

    /**
     * @return \Infusionsoft\Api\InvoiceService
     */
    public function invoices()
    {
        return $this->getApi('InvoiceService');
    }

    /**
     * @return \Infusionsoft\Api\Rest\MerchantService
     */
    public function merchants()
    {
        return $this->getRestApi('MerchantService');
    }

    /**
     * @param string $api
     *
     * @return \Infusionsoft\Api\OrderService | \Infusionsoft\Api\Rest\OrderService
     */
    public function orders($api = 'rest')
    {
        if ($api == 'xml') {
            return $this->getApi('OrderService');
        }

        return $this->getRestApi('OrderService');
    }

    /**
     * @param string $api
     *
     * @return \Infusionsoft\Api\ProductService | \Infusionsoft\Api\Rest\ProductService
     */
    public function products($api = 'rest')
    {
        if ($api == 'xml') {
            return $this->getApi('ProductService');
        }

        return $this->getRestApi('ProductService');
    }

    /**
     * @return \Infusionsoft\Api\Rest\ResthookService
     */
    public function resthooks()
    {
        return $this->getRestApi('ResthookService');
    }

    /**
     * @return \Infusionsoft\Api\SearchService
     */
    public function search()
    {
        return $this->getApi('SearchService');
    }

    /**
     * @return \Infusionsoft\Api\ShippingService
     */
    public function shipping()
    {
        return $this->getApi('ShippingService');
    }

    /**
     * @return \Infusionsoft\Api\WebFormService
     */
    public function webForms()
    {
        return $this->getApi('WebFormService');
    }

    /**
     * @return \Infusionsoft\Api\WebTrackingService
     */
    public function webTracking()
    {
        return $this->getApi('WebTrackingService');
    }

    /**
     * @return \Infusionsoft\Api\Rest\TaskService
     */
    public function tasks()
    {
        return $this->getRestApi('TaskService');
    }

    /**
     * @return \Infusionsoft\Api\Rest\NoteService
     */
    public function notes()
    {
        return $this->getRestApi('NoteService');
    }

    /**
     * @return \Infusionsoft\Api\Rest\AppointmentService
     */
    public function appointments()
    {
        return $this->getRestApi('AppointmentService');
    }

    /**
     * @return \Infusionsoft\Api\Rest\CustomFieldService
     */
    public function customfields()
    {
        return $this->getRestApi('CustomFieldService');
    }

    /**
     * @return \Infusionsoft\Api\Rest\TagService
     */
    public function tags()
    {
        return $this->getRestApi('TagService');
    }

    /**
     * @return \Infusionsoft\Api\Rest\TransactionService
     */
    public function transactions()
    {
        return $this->getRestApi('TransactionService');
    }

    /**
     * @return \Infusionsoft\Api\Rest\CampaignService
     */
    public function campaigns()
    {
        return $this->getRestApi('CampaignService');
    }

    /**
     * @return \Infusionsoft\Api\Rest\CampaignService
     */
    public function companies()
    {
        return $this->getRestApi('CompanyService');
    }

    /**
     * @return \Infusionsoft\Api\Rest\UserInfoService
     */
    public function userinfo()
    {
        return $this->getRestApi('UserInfoService');
    }

    /**
     * @return \Infusionsoft\Api\Rest\OpportunityService
     */
    public function opportunities()
    {
        return $this->getRestApi('OpportunityService');
    }

    /**
     * @return \Infusionsoft\Api\Rest\SubscriptionService
     */
    public function subscriptions()
    {
        return $this->getRestApi('SubscriptionService');
    }

    /**
     * Returns the requested class name, optionally using a cached array so no
     * object is instantiated more than once during a request.
     *
     * @param string $class
     *
     * @return mixed
     */
    public function getApi($class)
    {
        $class = '\Infusionsoft\Api\\' . $class;

        if ( ! isset($this->apis[$class])) {
            $this->apis[$class] = new $class($this);
        }

        return $this->apis[$class];
    }

    /**
     * Returns the requested class name, optionally using a cached array so no
     * object is instantiated more than once during a request.
     *
     * @param string $class
     *
     * @return mixed
     */
    public function getRestApi($class)
    {
        $class = '\Infusionsoft\Api\Rest\\' . $class;

        return new $class($this);
    }

}

