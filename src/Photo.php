<?php

namespace Crew\Unsplash;

class Photo extends Endpoint
{
	private $photographer;
	
	/**
	 * Retrieve the a photo object from the ID specified
	 *
	 * @param  string $id ID of the photo
	 * @return Photo
	 */
	public static function find($id)
	{
		$photo = json_decode(self::get("photos/{$id}")->getBody(), true);
		
		return new self($photo);
	}

	/**
	 * Retrieve all the photos on a specific page.
	 * Returns an ArrayObject that contains Photo objects.
	 * 
	 * @param  integer $page Page from which the photos need to be retrieve
	 * @param  integer $per_page Number of element in a page
	 * @return ArrayObject of Photos
	 */
	public static function all($page = 1, $per_page = 10)
	{
		$photos = self::get("photos", ['query' => ['page' => $page, 'per_page' => $per_page]]);

		$photosArray = self::getArray($photos->getBody(), get_called_class());

		return new ArrayObject($photosArray, $photos->getHeaders());
	}

	/**
	 * Retrieve all the photos on a specific page depending on search results
	 * Returns ArrayObject that contain Photo object.
	 *
	 * @param  string  $search Retrieve photos matching the search term.
	 * @param  integer $category Retrieve photos matching the category ID
	 * @param  integer $page Page from which the photos need to be retrieved
	 * @param  integer $per_page Number of elements on a page
	 * @return ArrayObject of Photos
	 */
	public static function search($search, $category = null, $page = 1, $per_page = 10)
	{
		$photos = self::get("photos/search", ['query' => ['query' => $search, 'category' => $category, 'page' => $page, 'per_page' => $per_page]]);

		$photosArray = self::getArray($photos->getBody(), get_called_class());

		return new ArrayObject($photosArray, $photos->getHeaders());
	}

	/**
	 * Create a new photo. The user needs to connect their account and authorize the write_photo permission scope.
	 * 
	 * @param  string $filePath Path of the file to upload
	 * @return Photo
	 */
	public static function create($filePath)
	{
		$file = fopen($filePath, 'r');

		$photo = json_decode(self::post("photos", ['multipart' => [['name' => 'photo', 'contents' => $file]]])->getBody(), true);

		return new self($photo);
	}

	/**
	 * Retrieve the user that uploaded the photo. 
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