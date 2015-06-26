<?php

namespace Crew\Unsplash;

class User extends Endpoint
{
	public function find($userId)
	{
		return $this->get("users/{$userId}");
	}

	public function photos($userId, $page = 1, $per_page = 10)
	{
		return $this->get("users/{$userId}/photos", ['query' => ['page' => $page, 'per_page' => $per_page]]);
	}

	public function current()
	{
		return $this->get("me");
	}

	public function update(Array $parameters)
	{
		return $this->put("me", ['json' => $parameters]);
	}
}