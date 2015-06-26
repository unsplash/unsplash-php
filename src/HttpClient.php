<?php

namespace Crew\Unsplash;

use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Handler\CurlHandler;
use GuzzleHttp\Middleware;

use GuzzleHttp\Psr7\Request;

class HttpClient
{
	private $httpClient;

	public function __construct($authorization)
	{
		$this->httpClient = new Client(['handler' => $this->setHandler($authorization)]);
	}

	public function send($request, $params)
	{
		return $this->httpClient->send($request, $params);
	}

	private function setHandler($authorization)
	{
		$stack = new HandlerStack();

		$stack->setHandler(new CurlHandler());

		// Set authorization headers
		$this->authorization = $authorization;
		$stack->push(Middleware::mapRequest(function (Request $request) {
		    return $request->withHeader('Authorization', $this->authorization);
		}), 'set_authorization_header');
		
		$stack->push(Middleware::mapRequest(function (Request $request) {
			$uri = $request->getUri()->withHost('api.staging.unsplash.com')->withScheme('http');

		    return $request->withUri($uri);
		}), 'set_host');

		return $stack;
	}
}