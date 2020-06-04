<?php

namespace Unsplash\Tests;

use \Unsplash as Unsplash;
use \VCR\VCR;

class UserTest extends BaseTest
{
    public function setUp(): void
    {
        parent::setUp();

        $connection = new Unsplash\Connection($this->provider, $this->accessToken);
        Unsplash\HttpClient::$connection = $connection;
    }

    public function testFindUser()
    {
        VCR::insertCassette('users.json');
        $user = Unsplash\User::find('dechuck');
        VCR::eject();

        $this->assertEquals('dechuck', $user->username);
    }

    public function testFindUnknownUser()
    {
        $this->expectException(\Unsplash\Exception::class);
        VCR::insertCassette('users.json');
        Unsplash\User::find('badbadnotgooduser');
        VCR::eject();
    }

    public function testFindCurrentUser()
    {
        VCR::insertCassette('users.json');
        $user = Unsplash\User::current();
        VCR::eject();

        $this->assertNotEmpty($user->uploads_remaining);
    }

    public function testFindCurrentUserOnUnconnectedUser()
    {
        $this->expectException(\Unsplash\Exception::class);
        $connection = new Unsplash\Connection($this->provider);
        Unsplash\HttpClient::$connection = $connection;

        VCR::insertCassette('users.json');
        Unsplash\User::current();
        VCR::eject();
    }

    public function testFindUserPhotos()
    {
        VCR::insertCassette('users.json');
        $user = Unsplash\User::find('anniespratt');
        $photos = $user->photos();
        VCR::eject();

        $this->assertEquals(10, $photos->count());
    }

    public function testFindUserPhotosByOrder()
    {
        VCR::insertCassette('users.json');
        $user = Unsplash\User::find('lukechesser');
        $photos = $user->photos(1, 5, 'oldest');
        VCR::eject();

        $this->assertEquals(5, $photos->count());
    }

    public function testFindUserCollections()
    {
        VCR::insertCassette('users.json');
        $user = Unsplash\User::find('unsplash');
        $collections = $user->collections();
        VCR::eject();

        $this->assertEquals('Unsplash Awards 2019 – Finalists', $collections[0]->title);
    }

    public function testFindUserPrivateCollection()
    {
        VCR::insertCassette('users.json');
        $user = Unsplash\User::current();
        $collections = $user->collections();
        VCR::eject();

        $this->assertEquals('The Final Frontier', $collections[1]->title);
    }

    public function testUpdateUser()
    {
        VCR::insertCassette('users.json');
        $user = Unsplash\User::find('dechuck');
        $user->update(['instagram_username' => 'dechuck123']);
        VCR::eject();

        $this->assertEquals('dechuck123', $user->instagram_username);
    }

    public function testFindUserLikedPhoto()
    {
        VCR::insertCassette('users.json');
        $user = Unsplash\User::find('unsplash', 10, 'oldest');
        $likes = $user->likes();
        $this->assertCount(10, $likes);
        VCR::eject();

        $this->assertNotEmpty($likes);
    }

    public function testGetUserPortfolio()
    {
        VCR::insertCassette('users.json');
        $userPortfolioLink = Unsplash\User::portfolio('hughbertd');
        $this->assertEquals('http://hughbertd.github.io', $userPortfolioLink);
        VCR::eject();
    }

    public function testUserStatistics()
    {
        VCR::insertCassette('users.json');
        $user = Unsplash\User::find('hughbertd');
        $statistics = $user->statistics();
        $this->assertArrayHasKey('downloads', $statistics);
        $this->assertArrayHasKey('views', $statistics);
        $this->assertArrayHasKey('likes', $statistics);
        VCR::eject();
    }
}
