<?php

namespace Crew\Unsplash;

class Photo extends Endpoint
{
	public function find($photoId)
	{
		return $this->get("photos/{$photoId}");
	}

	public function findAll($page = 1, $per_page = 10)
	{
		return $this->get("photos", ['page' => $page, 'per_page' => $per_page]);
	}

	public function search($search, $category = null, $page = 1, $per_page = 10)
	{
		return $this->get("photos/search", ['query' => $search, 'category' => $category, 'page' => $page, 'per_page' => $per_page]);
	}

	public function create($filename, $file)
	{
		return $this->post("photos", ['multipart' => ['name' => $filename, 'contents' => $file]]);
	}
}