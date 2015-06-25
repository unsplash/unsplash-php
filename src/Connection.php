<?php

namespace Crew\Unsplash;

use Crew\Unsplash\Provider;
use \League\OAuth2\Client\Grant\RefreshToken;

class Connection
{
	private $provider;
	private $token;

	public function __construct(Provider\Unsplash $provider, \stdClass $token = null)
	{
		$this->provider = $provider;
		$this->token = $token;
	}

	public function getConnectionUrl()
	{
		return $this->provider->getAuthorizationUrl();
	}

	public function generateToken($code)
	{
		$this->token = $this->provider->getAccessToken('authorization_code', [
	        'code' => $code
	    ]);

	    return $this->token;
	}

	public function setToken(\stdClass $token)
	{
		$this->token = $token;
	}

	public function refreshToken()
	{
		if (is_null($this->token)) {
			// @todo return an error
			return null;
		}

		$grant = new RefreshToken();

		$this->token = $this->provider->getAccessToken($grant, [
			'refresh_token' => $this->token->refreshToken
		]);

		return $this->token;
	}

	public function getAuthorizationToken()
	{
		$authorizationToken = "Client-ID {$this->provider->client_id}";

		if (! is_null($this->token)) {
			// Validate if the token object link to this class is expire
			// refresh it if it's the case
			if ($this->token->expires < time()) {
				$this->refreshToken();
			}

			$authorizationToken = "Bearer {$this->token->accessToken}";
		}

		return $authorizationToken;
	}
}