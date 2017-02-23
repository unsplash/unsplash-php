<?php

namespace Crew\Unsplash\Tests;

use \Crew\Unsplash as Unsplash;
use GuzzleHttp\Tests\Server;
use GuzzleHttp\Psr7\Response;
use \VCR\VCR;

class SearchTest extends BaseTest
{
    public function setUp()
    {
        parent::setUp();

        $connection = new Unsplash\Connection($this->provider, $this->accessToken);
        Unsplash\HttpClient::$connection = $connection;
    }

    public function testSearchPhotos()
    {
        VCR::insertCassette('search.yml');

        $photos = Unsplash\Search::photos("paris");

        VCR::eject();

        $this->assertEquals(10, $photos->count());
    }

    public function testSearchCollections()
    {
        VCR::insertCassette('search.yml');

        $photos = Unsplash\Search::collections("paris");

        VCR::eject();

        $this->assertEquals(33, $photos->count());
    }

    public function testSearchUsers()
    {
        VCR::insertCassette('search.yml');

        $photos = Unsplash\Search::users("dechuck");

        VCR::eject();

        $this->assertEquals(1, $photos->count());
    }
}