<?php

namespace Crew\Unsplash;

use \League\OAuth2\Client\Grant\RefreshToken;
use League\OAuth2\Client\Token\AccessToken;

class Connection
{
  private $provider;
  private $token;

  /**
   * @param Provider $provider OAuth2 provider object to interact with the Unsplash API
   * @param \League\OAuth2\Client\Token\AccessToken|null $token Token information if one already exists for the user
   */
  public function __construct(Provider $provider, AccessToken $token = null)
  {
    $this->provider = $provider;
    $this->token = $token;
  }

  /**
   * Retrieve the URL that generates the authorization code
   *
   * @param  array $scopes Scopes to include in the authorization URL
   * @return string
   */
  public function getConnectionUrl($scopes = [])
  {
    if (! empty($scopes)) {
      $this->provider->setScopes($scopes);
    }

    return $this->provider->getAuthorizationUrl();
  }

  /**
   * Generate a new access token object from an authorization code
   *
   * @param  string $code Authorization code provided by the Unsplash OAuth2 service
   * @return \League\OAuth2\Client\Token\AccessToken
   */
  public function generateToken($code)
  {
    $this->token = $this->provider->getAccessToken('authorization_code', [
      'code' => $code
    ]);

    return $this->token;
  }

  /**
   * Assign a new access token to the connection object
   *
   * @param AccessToken $token Access token to assign to the connection object
   */
  public function setToken(AccessToken $token)
  {
    $this->token = $token;
  }

  /**
   * Refresh an expired token and generate a new AccessToken object.
   *
   * @return \League\OAuth2\Client\Token\AccessToken|null
   */
  public function refreshToken()
  {
    if (is_null($this->token) || is_null($this->token->getRefreshToken())) {
      // @todo return an error
      return null;
    }

    $grant = new RefreshToken();
    $refreshToken = $this->provider->getAccessToken($grant, [
      'refresh_token' => $this->token->getRefreshToken()
    ]);

    $this->token = $refreshToken;


    return $this->token;
  }

  /**
   * Generate the authorization string to pass in via the http header.
   * Check if a token is linked to the connection object and use the client ID if there is not.
   * The method will also refresh the access token if it has expired.
   *
   * @return string
   */
  public function getAuthorizationToken()
  {
    $authorizationToken = "Client-ID {$this->provider->getClientId()}";

    if (! is_null($this->token)) {
      // Validate if the token object link to this class is expire
      // refresh it if it's the case
      if ($this->token->hasExpired()) {
        $this->refreshToken();
      }

      $authorizationToken = "Bearer {$this->token->getToken()}";
    }

    return $authorizationToken;
  }
}
