<?php

namespace Crew\Unsplash;

class User extends Endpoint
{
	private $photos;

	/**
	 * Retrieve the a User object from the username specified
	 *
	 * @param  string $username Username of the user to find
	 * @return User
	 *
	 * @example Crew\Unsplash\User::find('lukechesser');
	 */
	public static function find($username)
	{
		$user = json_decode(self::get("users/{$username}"), true);
		
		return new self($user);
	}

	/**
	 * Retrieve all the photos for a specific user on a specific page.
	 * The function retrieve an ArrayObject that contain Photo object.
	 * 
	 * @param  integer $page Page from which the photos need to be retrieve
	 * @param  integer $per_page Number of element in a page
	 * @return ArrayObject of Photo
	 */
	public function photos($page = 1, $per_page = 10)
	{
		if (! isset($this->photos["{$page}-{$per_page}"])) {
			$photos = json_decode(self::get("users/{$this->username}/photos", ['query' => ['page' => $page, 'per_page' => $per_page]]), true);
		
			$this->photos["{$page}-{$per_page}"] = array_map(function ($photo) {return new Photo($photo);}, $photos);
		}

		return new \ArrayObject($this->photos["{$page}-{$per_page}"]);
	}

 	/**
	 * Retrieve the a User object of the connect user.
	 *
	 * @param  string $username Username of the user to find
	 * @return User
	 *
	 * @example Crew\Unsplash\User::current();
	 */
	public static function current()
	{
		$user = json_decode(self::get("me"), true);
		
		return new self($user);
	}

	/**
	 * Update specific parameters on a user
	 * 
	 * @param  Array $parameters Array containing the parameters to update on a user
	 * @return void
	 */
	public function update(Array $parameters)
	{
		$user = json_decode($this->put("me", ['json' => $parameters]), true);

		parent::update($user);
	}
}