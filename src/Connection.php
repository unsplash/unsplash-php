<?php

namespace Crew\Unsplash;

use GuzzleHttp\Client as Client;

class Connection
{
	private $credentials;
	private $client;

	public function __construct($credentials)
	{
		$this->credentials = $credentials;
		$this->client = new Client();
	}

	public function generateToken($token, $grant_type = 'authorization_code')
	{
		print $grant_type;
		$query = $this->getTokenQuery($token, $grant_type);

		$res = $this->client->post("http://staging.unsplash.com/oauth/token?{$query}");

		$body = json_decode($res->getBody(), true);

		$this->credentials->access_token = $body['access_token'];
		$this->credentials->refresh_token = $body['refresh_token'];
		$this->credentials->expire_at = $body['created_at'] + $body['expires_in'];

		return $body;
	}

	public function getAuthorizationToken()
	{
		$authorizationToken = "Client-ID {$this->credentials->client_id}";

		if (! is_null($this->credentials->access_token)) {
			$authorizationToken = "Bearer {$this->credentials->access_token}";
		}

		return $authorizationToken;
	}

	public function getConnectionUrl()
	{
		$htmlQuery = http_build_query($this->credentials->toArray());

		return "http://api.staging.unsplash.com/oauth/authorize?{$htmlQuery}";
	}

	private function getTokenQuery($token, $grant_type = 'authorization_code')
	{
		$params = [
			'client_id' => $this->credentials->client_id,
			'client_secret' => $this->credentials->client_secret,
		];

		if ($grant_type == 'authorization_code') {
			$params += [
				'code' => $token,
				'redirect_uri' => $this->credentials->redirect_uri,
				'grant_type' => 'authorization_code',
				'scope' => $this->credentials->scope ?: 'public'
			];
		} else {
			$params += [
				'grant_type' => 'refresh_token',
				'refresh_token' => $token
			];
		}

		return http_build_query($params);
	}

	public function hasTokenInformations()
	{
		if (isset($this->credentials->access_token, $this->credentials->refresh_token, $this->credentials->expire_at)) {
			return true;
		}

		return false;
	}

	public function tokenHasExpired()
	{
		if ($this->hasTokenInformations() && $this->credentials->expire_at < time()) {
			return true;
		}

		return false;
	}
}