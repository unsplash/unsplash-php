<?php

namespace Crew\Unsplash;

use League\OAuth2\Client\Provider\AbstractProvider;
use League\OAuth2\Client\Token\AccessToken;
use League\OAuth2\Client\Entity\User;

class Provider extends AbstractProvider
{
	/**
	 * The basic authorization header is a bearer token
	 * @var string
	 */
	public $authorizationHeader = 'Bearer';

	/**
	 * Define the default scope of the wrapper
	 * @var array
	 */
	public $scopes = ['public'];

	/**
	 * Define the authorize url
	 * 
	 * @return string
	 */
	public function urlAuthorize()
	{
		return 'http://staging.unsplash.com/oauth/authorize';
	}

	/**
	 * Define the access token url
	 * 
	 * @return string
	 */
	public function urlAccessToken()
	{
		return 'http://staging.unsplash.com/oauth/token';
	}

	/**
	 * Define the current user details url
	 * 
	 * @return string
	 */
	public function urlUserDetails(AccessToken $token)
	{
		return "http://api.staging.unsplash.com/me?access_token={$token}";
	}

	/**
	 * @param  GuzzleHttp\Psr7\Response $response Http response
	 * @param  AccessToken $token Access token information of the current user
	 * @return stdClass
	 */
	public function userDetails($response, AccessToken $token)
	{
		$user = new User();

		$user->exchangeArray([
            'uid' => $response->uuid,
            'name' => $response->first_name . ' ' . $response->last_name,
            'firstname' => $response->first_name,
            'lastname' => $response->last_name
        ]);

        return $user;
	}

	/**
	 * @param  GuzzleHttp\Psr7\Response $response Http response
	 * @param  AccessToken $token Access token information of the current user
	 * @return string
	 */
	public function userUid($response, AccessToken $token)
    {
        return $response->uuid;
    }
}