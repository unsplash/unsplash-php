<?php

namespace Crew\Unsplash;

class CuratedBatch extends Endpoint
{
	private $photos;
	private $curator;

	/**
	 * Retrieve a specific curated batch
	 * 
	 * @param  int $id Id of the currated batch to retrieve
	 * @return CuratedBatch
	 */
	public static function find($id)
	{
		$curatedBatch = json_decode(self::get("curated_batches/{$id}"), true);
		
		return new self($curatedBatch);
	}

	/**
	 * Retrieve all the curated batches for a given pages
	 * 
	 * @param  integer $page Page from which the curated batches need to be retrieve
	 * @param  integer $per_page Number of element in a page
	 * @return ArrayObject of CuratedBatch
	 *
	 * @example Crew\Unsplash\CuratedBatch::all(2, 20);
	 * @example Crew\Unsplash\CuratedBatch::all(5);
	 * @example Crew\Unsplash\CuratedBatch::all();
	 */
	public static function all($page = 1, $per_page = 10)
	{
		$curatedBatches = json_decode(self::get("curated_batches", ['query' => ['page' => $page, 'per_page' => $per_page]]), true);

		$curatedBatches = array_map(function ($curatedBatch) {return new self($curatedBatch);}, $curatedBatches);

		return new \ArrayObject($curatedBatches);
	}

	/**
	 * Retrieve all the photos for a specific curated batches on a specific page.
	 * The function retrieve an ArrayObject that contain Photo object.
	 * 
	 * @return ArrayObject of Photo
	 */
	public function photos()
	{
		if (! isset($this->photos)) {
			$photos = json_decode(self::get("curated_batches/{$this->id}/photos"), true);

			$this->photos = array_map(function ($photo) {return new Photo($photo);}, $photos);
		}

		return new \ArrayObject($this->photos);
	}
}