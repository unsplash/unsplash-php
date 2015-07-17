<?php

namespace Crew\Unsplash;

use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Handler\CurlHandler;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Uri;

use GuzzleHttp\Psr7\Request;

class HttpClient
{
	private $httpClient;
	private $host = 'api.unsplash.com';
	private $scheme = 'https';

	/**
	 * Crew\Unsplash\Connection object link to the HttpClient
	 * Need to be set to the class before running anything else
	 *
	 * @example Crew\Unsplash\HttpClient::$connection = new Crew\Unsplash\Connection();
	 * @var Crew\Unsplash\Connection
	 */
	public static $connection;

	/**
	 * Generate a new http client variable. Retrieve the authorization token generate by the $connection object
	 */
	public function __construct()
	{
		$this->httpClient = new Client(['handler' => $this->setHandler(self::$connection->getAuthorizationToken())]);
	}

	/**
	 * Send a http request through the http client.
	 * Generate a new request method in whith the http metho and the uri is passed
	 * 
	 * @param  string $method http method to be trigger
	 * @param  array $argument Array containing the uri to send the request and the parameters of the request
	 * @return GuzzleHttp\Psr7\Response
	 */
	public function send($method, $arguments)
	{
		$uri = $arguments[0];
		$params = isset($arguments[1]) ? $arguments[1] : [];

		$response = $this->httpClient->send(
			new Request($method, new Uri($uri)),
			$params
		);

		return $response;
	}

	/**
	 * Generate a new handler that will manage the http request.
	 *
	 * Some middleware are also set to manage the authorization header and 
	 * the request URI
	 * 
	 * @param string $authorization Authorization code to pass in the header
	 * @return GuzzleHttp\HandlerStack
	 */
	private function setHandler($authorization)
	{
		$stack = new HandlerStack();

		$stack->setHandler(new CurlHandler());

		// Set authorization headers
		$this->authorization = $authorization;
		$stack->push(Middleware::mapRequest(function (Request $request) {
		    return $request->withHeader('Authorization', $this->authorization);
		}), 'set_authorization_header');
		
		// Set the request ui
		$stack->push(Middleware::mapRequest(function (Request $request) {
			$uri = $request->getUri()->withHost($this->host)->withScheme($this->scheme);

		    return $request->withUri($uri);
		}), 'set_host');

		return $stack;
	}
}