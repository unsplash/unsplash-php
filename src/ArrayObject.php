<?php

namespace Crew\Unsplash;

class ArrayObject extends \ArrayObject
{
	private $headers;
	private $pages; 
	private $basePages = array(
		self::LAST => null,
		self::PREV => null,
		self::NEXT => null,
		self::FIRST => null
	);

	const LAST = 'last';
	const PREV = 'prev';
	const NEXT = 'next';
	const FIRST = 'first';

	const LINK = 'Link';
	const TOTAL = 'X-Total';
	const PER_PAGE = 'X-Per-Page';
 

	public function __construct($input, $headers)
	{
		$this->headers = $headers;
		// Preload pages array to be used from other method
		if (isset($headers[self::LINK][0])) {
			$this->generatePages();
		}

		parent::__construct($input);
	}

	/**
	 * Total page for this call
	 * @return int Total page
	 */
	public function totalPages()
	{
		return ceil($this->headers[self::TOTAL]/$this->headers[self::PER_PAGE]);
	}

	/**
	 * Current page number base on the Link header
	 * @return int Current page number
	 */
	public function currentPage()
	{
		if (isset($this->pages[self::NEXT])) {
			$page = $this->pages[self::NEXT] - 1;
		} else {
			$page = $this->pages[self::PREV] + 1;
		}

		return $page;
	}

	/**
	 * Retrive array contrain all pages for each position
	 * @return array
	 */
	public function getPages()
	{
		return array_merge($this->basePages, $this->pages);
	}

	/**
	 * Return an array with containing the page number for all position
	 * last, previous, next, first
	 * If the page number for a specific position doesn't exist (i.e it is the current page),
	 * the position will return null
	 * @return array
	 */
	private function generatePages()
	{
		$links = explode(',', $this->headers[self::LINK][0]);

		foreach ($links as $link) {
			// Run two preg_math to retrieve specific element in the string
			// snce the page attributes is not always at the same position,
			// we can't retireve both information in the same regex
			preg_match('/page=([^&>]*)/', $link, $page);
			preg_match('/rel="([a-z]+)"/', $link, $rel);

			if ($page[1] !== null && $rel[1] !== null) {
				$this->pages[$rel[1]] = $page[1];
			}
		}

		$this->pagesProcessed = true;

		return $this->pages;
	}
}