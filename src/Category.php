<?php

namespace Crew\Unsplash;

class Category extends Endpoint
{
	private $photos = [];

	/**
	 * Retrieve the a Category object from the id specified
	 *
	 * @param  integer $id Id of the category to find
	 * @return Category
	 *
	 * @example Crew\Unsplash\Category::find(2);
	 */
	public static function find($id)
	{
		$category = json_decode(self::get("categories/{$id}"), true);
		
		return new self($category);
	}

	/**
	 * Retrieve all the categories on a specific page.
	 * The function retrieve an ArrayObject that contain Category object.
	 * 
	 * @param  integer $page Page from which the categories need to be retrieve
	 * @param  integer $per_page Number of element in a page
	 * @return ArrayObject of Category
	 *
	 * @example Crew\Unsplash\Category::all(2, 20);
	 * @example Crew\Unsplash\Category::all(5);
	 * @example Crew\Unsplash\Category::all();
	 */
	public static function all($page = 1, $per_page = 10)
	{
		$categories = json_decode(self::get("categories", ['query' => ['page' => $page, 'per_page' => $per_page]]), true);

		$categories = array_map(function ($category) {return new self($category);}, $categories);

		return new \ArrayObject($categories);
	}

	/**
	 * Retrieve all the photos for a specific category on a specific page.
	 * The function retrieve an ArrayObject that contain Photo object.
	 * 
	 * @param  integer $page Page from which the photos need to be retrieve
	 * @param  integer $per_page Number of element in a page
	 * @return ArrayObject of Photo
	 */
	public function photos($page = 1, $per_page = 10)
	{
		if (! isset($this->photos["{$page}-{$per_page}"])) {
			$photos = json_decode(self::get("categories/{$this->id}/photos", ['query' => ['page' => $page, 'per_page' => $per_page]]), true);
		
			$this->photos["{$page}-{$per_page}"] = array_map(function ($photo) {return new Photo($photo);}, $photos);
		}

		return new \ArrayObject($this->photos["{$page}-{$per_page}"]);
	}
}