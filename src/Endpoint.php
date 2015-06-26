<?php

namespace Crew\Unsplash;

use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Uri;

class Endpoint
{
	private $connection = null;
	private $client = null;
	private $headers = null;
	private $statusCode = null;

	private $acceptedHttpMethod = ['get', 'post', 'put'];

	public function __construct(Provider\Unsplash $provider, \stdClass $token = null)
	{
		$this->connection = new Connection($provider, $token);

		$this->setHttpClient();
	}

	public function getHeaders($headerKey = null)
	{
		$header = $this->headers;

		if (! is_null($headerKey) && isset($this->headers[$headerKey])) {
			if (is_array($this->headers[$headerKey]) && count($this->headers[$headerKey]) == 1) {
				$header = $this->headers[$headerKey][0];
			}
		}

		return $header;
	}

	public function getStatusCode()
	{
		return $this->statusCode;
	}

	public function getBody()
	{
		return $this->body;
	}

	public function isGoodRequest()
	{
		return $this->statusCode >= 200 && $this->statusCode < 300;
	}

	public function __call($method, $arguments)
	{
		if (in_array($method, $this->acceptedHttpMethod)) {
			$uri = $arguments[0];
			$params = isset($arguments[1]) ? $arguments[1] : [];

			$response = $this->client->send(
				new Request($method, new Uri($uri)),
				$params
			);

			$this->setResVariable($response);

			return $this->getBody();
		}
	}

	private function setResVariable($response)
	{
		$this->headers = $response->getHeaders();
		$this->statusCode = $response->getStatusCode();
		$this->body = json_decode($response->getBody()->getContents(), true);
	}

	/**
	 * By default the http client is the client who's build in the constructor
	 * But it is possible to pass a different client if neccessary.
	 */
	public function setHttpClient($client = null)
	{
		if (! is_null($client)) {
			$this->client = $client;
		} else {
			$this->client = new HttpClient($this->connection->getAuthorizationToken());
		}
	}
}