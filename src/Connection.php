<?php

namespace Crew\Unsplash;

use \League\OAuth2\Client\Grant\RefreshToken;
use League\OAuth2\Client\Token\AccessToken;

class Connection
{
	private $provider;
	public $token;

	/**
	 * @param Provider Oauth2 provider to interact with the Unsplash API
	 * @param League\OAuth2\Client\Token\AccessToken|null Token information if some are already create for the user
	 */
	public function __construct(Provider $provider, AccessToken $token = null)
	{
		$this->provider = $provider;
		$this->token = $token;
	}

	/**
	 * Retrieve the url that generate the authorization code
	 * 
	 * @return string
	 */
	public function getConnectionUrl()
	{
		return $this->provider->getAuthorizationUrl();
	}

	/**
	 * Generate a new access token object from an authorization code
	 * 
	 * @param  string $code Authorization code provide by the Unsplash Oauth2 service
	 * @return League\OAuth2\Client\Token\AccessToken
	 */
	public function generateToken($code)
	{
		$this->token = $this->provider->getAccessToken('authorization_code', [
	        'code' => $code
	    ]);

	    return $this->token;
	}

	/**
	 * Set a new access token to the connection object
	 * 
	 * @param AccessToken $token Access token to add to the connection object
	 */
	public function setToken(AccessToken $token)
	{
		$this->token = $token;
	}

	/**
	 * Refresh an expired token from the refresh token.
	 * Generate a new AccessToken object from the stdClass provide
	 * by the getAccessToken method
	 * 
	 * @return League\OAuth2\Client\Token\AccessToken
	 */
	public function refreshToken()
	{
		if (is_null($this->token) || is_null($this->token->refreshToken)) {
			// @todo return an error
			return null;
		}

		$grant = new RefreshToken();
		$refreshToken = $this->provider->getAccessToken($grant, [
			'refresh_token' => $this->token->refreshToken
		]);

		$this->token = new AccessToken((array)$refreshToken);
		
		return $this->token;
	}

	/**
	 * Generate the authorization string pass in the header
	 * Validate if a token is linked to the connection object.
	 * Use the client id if it's not the case.
	 * The method also refresh the acces token if it's expired
	 * 
	 * @return String
	 */
	public function getAuthorizationToken()
	{
		$authorizationToken = "Client-ID {$this->provider->client_id}";

		if (! is_null($this->token)) {
			// Validate if the token object link to this class is expire
			// refresh it if it's the case
			if (isset($this->token->expires) && $this->token->expires < time()) {
				$this->refreshToken();
			}

			$authorizationToken = "Bearer {$this->token->accessToken}";
		}

		return $authorizationToken;
	}
}