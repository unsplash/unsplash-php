<?php

namespace Unsplash\Tests;

use \Unsplash as Unsplash;
use \VCR\VCR;

/**
 * Class CollectionTest
 * @package Unsplash\Tests
 */
class CollectionTest extends BaseTest
{
    public function setUp(): void
    {
        parent::setUp();
        $connection = new Unsplash\Connection($this->provider, $this->accessToken);
        Unsplash\HttpClient::$connection = $connection;
    }

    public function testFindAllCollections()
    {
        VCR::insertCassette('collections.json');

        $collections = Unsplash\Collection::all();

        VCR::eject();

        $this->assertEquals(10, $collections->count());
    }

    public function testFindCollection()
    {
        VCR::insertCassette('collections.json');

        $collection = Unsplash\Collection::find(201);

        VCR::eject();

        $this->assertEquals(201, $collection->id);
    }

    public function testFindPhotosCollections()
    {
        VCR::insertCassette('collections.json');

        $collection = Unsplash\Collection::find(201);
        $photos = $collection->photos();

        VCR::eject();

        $this->assertGreaterThan(1, $photos->count());
    }

    public function testCreateCollection()
    {
        VCR::insertCassette('collections.json');

        $collection = Unsplash\Collection::create('test collection', 'basic description', true);

        VCR::eject();

        $this->assertEquals('test collection', $collection->title);
        $this->assertNotNull($collection->id);
    }

    public function testUpdateCollection()
    {
        VCR::insertCassette('collections.json');

        $collection = Unsplash\Collection::create('test collection', 'basic description', true);
        $collection->update(['description' => 'updated basic description']);

        VCR::eject();

        $this->assertEquals('updated basic description', $collection->description);
    }

    public function testDestroyCollection()
    {
        VCR::insertCassette('collections.json');

        $collection = Unsplash\Collection::create('test collection', 'basic description', true);
        $collection->destroy();

        VCR::eject();
    }

    public function testRemovePhotoFromCollection()
    {
        VCR::insertCassette('collections.json');

        $collection = Unsplash\Collection::create(
            'test collection remove photo',
            'basic description',
            true
        );

        $collection->add('iDZt9nmvOWk');
        $collection->remove('iDZt9nmvOWk');
        $photos = $collection->photos();

        VCR::eject();

        $this->assertEquals(0, $photos->count());
    }

    public function testGetRelatedCollections()
    {
        VCR::insertCassette('collections.json');
        $collection = Unsplash\Collection::find(201);
        $relatedCollections = $collection->related();
        $this->assertCount(3, $relatedCollections);
        VCR::eject();
    }
}
