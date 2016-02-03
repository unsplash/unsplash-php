<?php

namespace Crew\Unsplash;

use League\OAuth2\Client\Provider\AbstractProvider;
use League\OAuth2\Client\Token\AccessToken;
use League\OAuth2\Client\Entity\User;
use Psr\Http\Message\ResponseInterface;

class Provider extends AbstractProvider
{
  /**
   * Define the authorize URL
   *
   * @return string
   */
  public function getBaseAuthorizationUrl()
  {
    return 'https://unsplash.com/oauth/authorize';
  }

  /**
   * Define the access token url
   *
   * @return string
   */
  public function getBaseAccessTokenUrl(array $params)
  {
    return 'https://unsplash.com/oauth/token';
  }

  /**
   * Define the current user details url
   *
   * @param AccessToken $token
   * @return string
   */
  public function getResourceOwnerDetailsUrl(AccessToken $token)
  {
    return "https://api.unsplash.com/me?access_token={$token}";
  }

  public function getDefaultScopes()
  {
    return ['public', 'read_user'];
  }

  public function getClientId()
  {
    return $this->clientId;
  }

  protected function checkResponse(ResponseInterface $response, $data)
  {
    if (! empty($data['error'])) {
      $message = $data['error'].": ".$data['error_description'];
      throw new Exception([$message]);
    }
  }

  protected function createResourceOwner(array $response, AccessToken $token)
  {
    $user = new \Crew\Unsplash\User([
      'id' => $response['id'],
      'name' => $response['first_name'] . ' ' . $response['last_name'],
      'firstname' => $response['first_name'],
      'lastname' => $response['last_name']
    ]);

    return $user;
  }

  /**
   * The default authorization header uses a bearer token
   * @var string
   */
  protected function getAuthorizationHeaders($token = null)
  {
    return ['Bearer'];
  }

  /**
   * Define the scopes separator for the url
   * @var string
   */
  protected function getScopeSeparator()
  {
    return ' ';
  }
}
