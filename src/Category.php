<?php

namespace Crew\Unsplash;

class Category extends Endpoint
{
	public function find($cateogyId)
	{
		return $this->get("categories/{$cateogyId}");
	}

	public function findAll($page = 1, $per_page = 10)
	{
		return $this->get("categories", ['page' => $page, 'per_page' => $per_page]);
	}

	public function photos($cateogyId, $page = 1, $per_page = 10)
	{
		return $this->get("categories/{$cateogyId}/photos", ['page' => $page, 'per_page' => $per_page]);
	}
}