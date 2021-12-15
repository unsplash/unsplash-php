<?php

namespace Unsplash;

/**
 * Class PageResult
 * @package Unsplash
 */
class PageResult implements \ArrayAccess
{
    /**
     * @var string
     */
    private $resultClassName = '';

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
    private $results;

    /**
     * @var array
     */
    private $headers;

    /**
     * PageResult constructor.
     *
     * @param array  $results
     * @param int    $total
     * @param int    $totalPages
     * @param array  $headers
     * @param string $className
     */
    public function __construct(array $results, $total, $totalPages, array $headers, $className)
    {
        $this->results = $results;
        $this->total = $total;
        $this->totalPages = $totalPages;
        $this->headers = $headers;
        $this->resultClassName = $className;
    }

    /**
     * @return string
     */
    public function getResultClassName()
    {
        return $this->resultClassName;
    }

    /**
     * @return int
     */
    public function getTotal()
    {
        return $this->total;
    }

    /**
     * @return int
     */
    public function getTotalPages()
    {
        return $this->totalPages;
    }

    /**
     * @return array
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * @return array
     */
    public function getResults()
    {
        return $this->results;
    }

    /**
     * @return ArrayObject
     * @throws \Exception
     */
    public function getArrayObject()
    {
        $className = $this->getResultClassName();

        $results = array_map(function (array $record) use ($className) {
            return new $className($record);
        }, $this->results);

        return new ArrayObject($results, $this->getHeaders());
    }

    /**
     * {@inheritdoc}
     */
    #[\ReturnTypeWillChange]
    public function offsetExists($offset)
    {
        return isset($this->results[$offset]);
    }

    /**
     * {@inheritdoc}
     */
    #[\ReturnTypeWillChange]
    public function offsetGet($offset)
    {
        return $this->results[$offset];
    }

    /**
     * {@inheritdoc}
     */
    #[\ReturnTypeWillChange]
    public function offsetSet($offset, $value)
    {
        $this->results[$offset] = $value;
    }

    /**
     * {@inheritdoc}
     */
    #[\ReturnTypeWillChange]
    public function offsetUnset($offset)
    {
        unset($this->results[$offset]);
    }
}
