<?php

namespace Crew\Unsplash;

use GuzzleHttp\Client;

class Endpoint
{
	private $connection = null;
	private $client = null;
	private $headers = null;
	private $statusCode = null;

	public function __construct(Connection $connection)
	{
		$this->connection = $connection;
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

	protected function get($path, $query = [])
	{
		$this->renewConnectionIfNeeded();

		$res = $this->client->get($path, ['query' => $query, 'headers' => ['Authorization' => $this->connection->getAuthorizationToken()]]);

		$this->headers = $res->getHeaders();
		$this->statusCode = $res->getStatusCode();

		return json_decode($res->getBody(), true);
	}

	protected function post($path, $params = [], $query = [])
	{
		$this->renewConnectionIfNeeded();

		$res = $this->client->post($path, [$params], ['query' => $query, 'headers' => ['Authorization' => $this->connection->getAuthorizationToken()]]);

		$this->headers = $res->getHeaders();
		$this->statusCode = $res->getStatusCode();

		return $res->getStatusCode() >= 200 & $res->getStatusCode() < 300;
	}

	private function renewConnectionIfNeeded()
	{
		print($this->connection->tokenHasExpired());
		if ($this->connection->tokenHasExpired()) {
			$this->connection->regenerateToken();
		}
	}
}