<?php

namespace Unsplash\Tests;

use \Unsplash as Unsplash;
use \VCR\VCR;

/**
 * Class StatTest
 * @package Unsplash\Tests
 */
class StatTest extends BaseTest
{
    /**
     * @var array
     */
    protected $total = [];

    public function setUp(): void
    {
        parent::setUp();

        $connection = new Unsplash\Connection($this->provider, $this->accessToken);
        Unsplash\HttpClient::$connection = $connection;

        $this->total = [
            "photo_downloads" => 0
        ];
    }

    public function testFindTotalStats()
    {
        VCR::insertCassette('stats.json');

        $totalStats = Unsplash\Stat::total();

        VCR::eject();

        $this->assertEquals($this->total['photo_downloads'], $totalStats->photo_downloads);
    }
}
