<?php

namespace Unsplash\Tests;

use \Unsplash as Unsplash;
use \VCR\VCR;

/**
 * Class PhotoTest
 * @package Unsplash\Tests
 */
class PhotoTest extends BaseTest
{
    public function setUp(): void
    {
        parent::setUp();
        $connection = new Unsplash\Connection($this->provider, $this->accessToken);
        Unsplash\HttpClient::$connection = $connection;
        Unsplash\HttpClient::$utmSource = 'test';
    }

    public function testFindPhoto()
    {
        VCR::insertCassette('photos.json');
        $photo = Unsplash\Photo::find('ZUaqqMxtxYk');
        VCR::eject();

        $this->assertEquals('ZUaqqMxtxYk', $photo->id);
    }

    public function testFindAllPhotos()
    {
        VCR::insertCassette('photos.json');
        $photos = Unsplash\Photo::all();
        VCR::eject();

        $this->assertEquals(10, $photos->count());
    }

    public function testPhotographer()
    {
        VCR::insertCassette('photos.json');
        $photo = Unsplash\Photo::find('ZUaqqMxtxYk');
        $photographer = $photo->photographer();
        VCR::eject();

        $this->assertEquals($photo->user['username'], $photographer->username);
    }

    public function testRandomPhoto()
    {
        VCR::insertCassette('photos.json');
        $photo = Unsplash\Photo::random();
        VCR::eject();

        $this->assertEquals('P7Lh0usGcuk', $photo->id);
    }

    public function testRandomPhotoWithFilters()
    {
        VCR::insertCassette('photos.json');
        $filters = [
            'featured' => true,
            'username' => 'andy_brunner',
            'query'    => 'ice',
            'w'        => 100,
            'h'        => 100
        ];
        $photo = Unsplash\Photo::random($filters);
        VCR::eject();

        $this->assertEquals('_-BxCUIRjuE', $photo->id);
        $this->assertEquals(
            'https://images.unsplash.com/photo-1478253218275-d88ba5a9b6c7?ixlib=rb-1.2.1&q=80&fm=jpg&crop=entropy&cs=tinysrgb&w=100&h=100&fit=crop&ixid=eyJhcHBfaWQiOjEyMDd9',
            $photo->urls['custom']
        );
    }

    public function testLikePhoto()
    {
        VCR::insertCassette('photos.json');
        $photo = Unsplash\Photo::find('_yT_vva8zSc');
        $like = $photo->like();
        VCR::eject();

        $this->assertTrue($like);
    }

    public function testUnlikePhoto()
    {
        VCR::insertCassette('photos.json');
        $photo = Unsplash\Photo::find('_yT_vva8zSc');
        $photo->like();
        $unlike = $photo->unlike();
        VCR::eject();

        $this->assertTrue($unlike);
    }

    public function testStatisticsForPhoto()
    {
        VCR::insertCassette('photos.json');
        $photo = Unsplash\Photo::find('ZUaqqMxtxYk');
        $response = $photo->statistics();
        $this->assertInstanceOf('ArrayObject', $response);
        $this->assertArrayHasKey('id', $response);
        $this->assertArrayHasKey('downloads', $response);
        $this->assertArrayHasKey('views', $response);
        $this->assertArrayHasKey('likes', $response);
        VCR::eject();
    }

    public function testDownloadLinkForPhoto()
    {
        VCR::insertCassette('photos.json');
        $photo = Unsplash\Photo::find('ZUaqqMxtxYk');
        $link = $photo->download();
        $this->assertIsString($link);
        $this->assertNotFalse(filter_var($link, FILTER_VALIDATE_URL));
        VCR::eject();
    }

    public function testUpdatePhoto()
    {
        VCR::insertCassette('photos.json');
        $photo = Unsplash\Photo::find('GQcfdBoVB_g');
        $photo->update(['exif' => ['focal_length' => 10]]);
        $this->assertEquals(10, $photo->exif['focal_length']);
        VCR::eject();
    }

    public function testAllPhotosOrderedLatest()
    {
        VCR::insertCassette('photos.json');
        $photos = Unsplash\Photo::all(1, 10, 'latest');
        VCR::eject();

        $this->assertEquals(10, $photos->count());
    }
}
