<?php

namespace Crew\Unsplash;

class User extends Endpoint
{
	private $photos;

	/**
	 * Retrieve a User object from the username specified
	 *
	 * @param  string $username Username of the user
	 * @return User
	 */
	public static function find($username)
	{
		$user = json_decode(self::get("users/{$username}")->getBody(), true);
		
		return new self($user);
	}

	/**
	 * Retrieve all the photos for a specific user on a given page.
	 * Returns an ArrayObject that contains Photo objects.
	 * 
	 * @param  integer $page Page from which the photos are to be retrieved
	 * @param  integer $per_page Number of elements on a page
	 * @return ArrayObject of Photos
	 */
	public function photos($page = 1, $per_page = 10)
	{
		if (! isset($this->photos["{$page}-{$per_page}"])) {
			$photos = self::get("users/{$this->username}/photos", ['query' => ['page' => $page, 'per_page' => $per_page]]);
		
			$this->photos["{$page}-{$per_page}"] = [
				'body' => self::getArray($photos->getBody(), __NAMESPACE__.'\\Photo'),
				'headers' => $photos->getHeaders()
			];
		}

		return new ArrayObject($this->photos["{$page}-{$per_page}"]['body'], $this->photos["{$page}-{$per_page}"]['headers']);
	}

 	/**
	 * Retrieve a User object of the logged-in user.
	 *
	 * @return User
	 */
	public static function current()
	{
		$user = json_decode(self::get("me")->getBody(), true);
		
		return new self($user);
	}

	/**
	 * Update specific parameters on the logged-in user
	 * 
	 * @param  Array $parameters Array containing the parameters to update
	 * @return void
	 */
	public function update(Array $parameters)
	{
		$user = json_decode(self::put("me", ['json' => $parameters])->getBody(), true);

		parent::update($user);
	}
}