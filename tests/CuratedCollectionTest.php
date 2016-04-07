<?php

namespace Crew\Unsplash\Tests;

use \Crew\Unsplash as Unsplash;
use \VCR\VCR;

class CuratedCollectionTest extends BaseTest
{
    public function setUp()
    {
        parent::setUp();

        $connection = new Unsplash\Connection($this->provider, $this->accessToken);
        Unsplash\HttpClient::$connection = $connection;
    }

    public function testFindCuratedCollection()
    {
        VCR::insertCassette('curated_collections.yml');

        $curatedBatch = Unsplash\CuratedCollection::find(68);

        VCR::eject();

        $this->assertEquals('68', $curatedBatch->id);
    }

    /**
     * @expectedException Crew\Unsplash\Exception
     * @expectedExceptionCode 404
     */
    public function testErrorOnNoCategory()
    {
        VCR::insertCassette('curated_collections.yml');

        $curatedBatch = Unsplash\CuratedCollection::find(300);

        VCR::eject();
    }

    public function testFindAllCuratedCollection()
    {
        VCR::insertCassette('curated_collections.yml');

        $curatedBatches = Unsplash\CuratedCollection::all();

        VCR::eject();

        $this->assertEquals(10, $curatedBatches->count());
    }

    public function testFindCuratedBatchPhotos()
    {
        VCR::insertCassette('curated_collections.yml');

        $curatedBatch = Unsplash\CuratedCollection::find(68);
        $photos = $curatedBatch->photos();

        VCR::eject();

        $this->assertEquals(9, $photos->count());
    }
}