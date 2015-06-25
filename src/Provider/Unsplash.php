<?php

namespace Crew\Unsplash\Provider;

use League\OAuth2\Client\Provider\AbstractProvider;
use League\OAuth2\Client\Token\AccessToken;
use League\OAuth2\Client\Entity\User;

class Unsplash extends AbstractProvider
{
	public $authorizationHeader = 'Bearer';
	public $scopes = ['public', 'read_user'];

	public function urlAuthorize()
	{
		return 'http://staging.unsplash.com/oauth/authorize';
	}

	public function urlAccessToken()
	{
		return 'http://staging.unsplash.com/oauth/token';
	}

	public function urlUserDetails(AccessToken $token)
	{
		return "http://api.staging.unsplash.com/me?access_token={$token}";
	}

	public function userDetails($response, AccessToken $token)
	{
		$user = new User();

		$user->exchangeArray([
            'uid' => $response->uuid,
            'name' => $response->first_name . ' ' . $response->last_name,
            'firstname' => $response->first_name,
            'lastname' => $response->last_name,
            'email' => $response->email
        ]);

        return $user;
	}

	public function userUid($response, AccessToken $token)
    {
        return $response->uuid;
    }
}