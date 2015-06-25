<?php

namespace Crew\Unsplash;

use GuzzleHttp\Client;

class Endpoint
{
	private $connection = null;
	private $client = null;
	private $headers = null;
	private $statusCode = null;

	public function __construct(Provider\Unsplash $provider, \stdClass $token = null)
	{
		$this->connection = new Connection($provider, $token);
		$this->client = new Client(['base_uri' => 'http://api.staging.unsplash.com']);
	}

	public function getHeaders($headerKey = null)
	{
		$header = $this->headers;

		if (! is_null($headerKey) && isset($this->headers[$headerKey])) {
			if (is_array($this->headers[$headerKey]) && count($this->headers[$headerKey]) == 1) {
				$header = $this->headers[$headerKey][0];
			} else {
				$header = $this->headers[$headerKey];
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
		return $this->getBody();
	}

	protected function get($path, $query = [])
	{
		$res = $this->client->get($path, ['query' => $query, 'headers' => ['Authorization' => $this->connection->getAuthorizationToken()]]);

		$this->setResVariable($res);

		return json_decode($res->getBody(), true);
	}

	protected function post($path, $params = [], $query = [], $multipart = [])
	{
		$res = $this->client->post($path, ['query' => $query, 'headers' => ['Authorization' => $this->connection->getAuthorizationToken()], 'form_params' => $params, 'multipart' => $multipart]);

		$this->setResVariable($res);

		return json_decode($res->getBody(), true);
	}

	protected function put($path, $params)
	{
		$res = $this->client->put($path, ['headers' => ['Authorization' => $this->connection->getAuthorizationToken()], 'form_params' => $params]);

		$this->setResVariable($res);

		return json_decode($res->getBody(), true);
	}


	private function setResVariable($res)
	{
		$this->headers = $res->getHeaders();
		$this->statusCode = $res->getStatusCode();
		$this->body = $res->getBody();
	}

	public function setHttpClient($client)
	{
		$this->client = $client;
	}
}