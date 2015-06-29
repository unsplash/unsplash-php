<?php

namespace Crew\Unsplash;

class Photo extends Endpoint
{
	private $photographer;
	
	/**
	 * Retrieve the a photo object from the id specified
	 *
	 * @param  string $id Id of the photo to find
	 * @return Photo
	 *
	 * @example Crew\Unsplash\Photo::find('fd234f');
	 */
	public static function find($id)
	{
		$photo = json_decode(self::get("photos/{$id}"), true);
		
		return new self($photo);
	}

	/**
	 * Retrieve all the photos on a specific page.
	 * The function retrieve an ArrayObject that contain Photo object.
	 * 
	 * @param  integer $page Page from which the photos need to be retrieve
	 * @param  integer $per_page Number of element in a page
	 * @return ArrayObject of Photo
	 *
	 * @example Crew\Unsplash\Photo::all(2, 20);
	 * @example Crew\Unsplash\Photo::all(5);
	 * @example Crew\Unsplash\Photo::all();
	 */
	public static function all($page = 1, $per_page = 10)
	{
		$photos = json_decode(self::get("photos", ['query' => ['page' => $page, 'per_page' => $per_page]]), true);

		$photos = array_map(function ($photo) {return new self($photo);}, $photos);

		return new \ArrayObject($photos);
	}

	/**
	 * Retrieve all the photos on a specific page depending on search results
	 * The function retrieve an ArrayObject that contain Photo object.
	 *
	 * @param  string  $search Retrieve photos matching the search word
	 * @param  integer $category Retrieve photos matching the category id
	 * @param  integer $page Page from which the photos need to be retrieve
	 * @param  integer $per_page Number of element in a page
	 * @return ArrayObject of Photo
	 *
	 * @example Crew\Unsplash\Photo::search('coffee');
	 * @example Crew\Unsplash\Photo::all('coffee', 3);
	 */
	public static function search($search, $category = null, $page = 1, $per_page = 10)
	{
		$photos = json_decode(self::get("photos/search", ['query' => ['query' => $search, 'category' => $category, 'page' => $page, 'per_page' => $per_page]]), true);

		$photos = array_map(function ($photo) {return new self($photo);}, $photos);

		return new \ArrayObject($photos);
	}

	/**
	 * Create a new photo. The user need to connect to his account and give the right to write_photo
	 * 
	 * @param  string $filePath Path of the file need to upload
	 * @return Photo
	 */
	public static function create($filePath)
	{
		$file = fopen($filePath, 'r');

		$photo = json_decode($this->post("photos", ['multipart' => [['name' => 'photo', 'contents' => $file]]]));

		return new self($photo);
	}

	/**
	 * Retrieve the user object of the one who take the photo
	 * 
	 * @return User
	 */
	public function photographer()
	{
		if (! isset($this->photographer)) {
			$this->photographer = User::find($this->user['username']);
		}

		return $this->photographer;
	}
}