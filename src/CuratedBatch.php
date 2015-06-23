<?php

namespace Crew\Unsplash;

class CuratedBatch extends Endpoint
{
	public function find($curatedBatchId)
	{
		return $this->get("curated_batches/{$curatedBatchId}");
	}

	public function findAll($page = 1, $per_page = 10)
	{
		return $this->get("curated_batches", ['page' => $page, 'per_page' => $per_page]);
	}

	public function photos($curatedBatchId)
	{
		return $this->get("curated_batches/{$curatedBatchId}/photos");
	}
}