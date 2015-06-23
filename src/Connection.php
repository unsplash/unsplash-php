<?php

namespace Crew\Unsplash;

use GuzzleHttp\Client as Client;

class Connection
{
	private $credentials;
	private $client;

	public function __construct($clientId, $clientSecret, $redirectUri = null, $access_token = null, $refresh_token = null, $expire_at = null)
	{
		$this->credentials = new Credential([
			'client_id' => $clientId,
			'client_secret' => $clientSecret,
			'redirect_uri' => $redirectUri,
			'access_token' => $access_token,
			'refresh_token' => $refresh_token,
			'expire_at' => $expire_at
		]);

		$this->client = new Client(['base_uri' => 'http://staging.unsplash.com']);
	}

	public function generateToken($token, $grant_type = 'authorization_code')
	{
		$query = $this->getTokenQuery($token, $grant_type);

		$res = $this->client->post("oauth/token?{$query}");

		$body = json_decode($res->getBody(), true);

		$this->credentials->access_token = $body['access_token'];
		$this->credentials->refresh_token = $body['refresh_token'];
		$this->credentials->expire_at = $body['created_at'] + $body['expires_in'];

		return $body;
	}

	public function regenerateToken()
	{
		$this->generateToken($this->credentials->refresh_token, 'refresh_token');
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
		return !is_null($this->credentials->refresh_token) && !is_null($this->credentials->expire_at);
	}

	public function tokenHasExpired()
	{
		return $this->hasTokenInformations() && $this->credentials->expire_at <= time();
	}
}