<?php

namespace Crew\Unsplash;

class PageResult implements \ArrayAccess
{
    /**
     * @var int
     */
    private $total = 0;
    /**
     * @var int
     */
    private $totalPages = 0;

    /**
     * @var array
     */
    private $results = [];

    /**
     * @return int
     */
    public function getTotal()
    {
        return $this->total;
    }

    /**
     * @param int $total
     */
    public function setTotal($total)
    {
        $this->total = $total;
    }

    /**
     * @return int
     */
    public function getTotalPages()
    {
        return $this->totalPages;
    }

    /**
     * @param int $totalPages
     */
    public function setTotalPages($totalPages)
    {
        $this->totalPages = $totalPages;
    }

    /**
     * @return array
     */
    public function getResults()
    {
        return $this->results;
    }

    /**
     * @param array $results
     */
    public function setResults(array $results)
    {
        $this->results = $results;
    }

    /**
     * {@inheritdoc}
     */
    public function offsetExists($offset)
    {
        return isset($this->results[$offset]);
    }

    /**
     * {@inheritdoc}
     */
    public function offsetGet($offset)
    {
        return $this->results[$offset];
    }

    /**
     * {@inheritdoc}
     */
    public function offsetSet($offset, $value)
    {
        $this->results[$offset] = $value;
    }

    /**
     * {@inheritdoc}
     */
    public function offsetUnset($offset)
    {
        unset($this->results[$offset]);
    }
}