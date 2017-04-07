<?php

namespace Crew\Unsplash;

/**
 * Class Search
 * @package Crew\Unsplash
 */
class Search extends Endpoint
{
    /**
     * Retrieve a single page of photo results depending on search results
     * Returns ArrayObject that contain PageResult object.
     *
     * @param  string  $search   Search terms.
     * @param  integer $page     Page from which the photos need to be retrieve
     * @param  integer $per_page Number of element in a page
     * @return PageResult
     */
    public static function photos($search, $page = 1, $per_page = 10)
    {
        $photos = self::get(
            "/search/photos",
            [ 'query' => [
                    'query' => $search,
                    'page' => $page,
                    'per_page' => $per_page
                ]
            ]
        );

        return self::getPageResult($photos->getBody(), $photos->getHeaders(), Photo::class);
    }

    /**
     * Retrieve a single page of collection results depending on search results
     * Returns ArrayObject that contain PageResult object.
     *
     * @param  string  $search   Search terms.
     * @param  integer $page     Page from which the photos need to be retrieve
     * @param  integer $per_page Number of element in a page
     * @return PageResult
     */
    public static function collections($search, $page = 1, $per_page = 10)
    {
        $collections = self::get(
            "/search/collections",
            ['query' => [
                    'query' => $search,
                    'page' => $page,
                    'per_page' => $per_page
                ]
            ]
        );

        return self::getPageResult($collections->getBody(), $collections->getHeaders(), Collection::class);
    }

    /**
     * Retrieve a single page of user results depending on search results
     * Returns ArrayObject that contain PageResult object.
     *
     * @param  string  $search   Search terms.
     * @param  integer $page     Page from which the photos need to be retrieve
     * @param  integer $per_page Number of element in a page
     * @return PageResult
     */
    public static function users($search, $page = 1, $per_page = 10)
    {
        $users = self::get(
            "/search/users",
            ['query' => [
                    'query' => $search,
                    'page' => $page,
                    'per_page' => $per_page
                ]
            ]
        );

        return self::getPageResult($users->getBody(), $users->getHeaders(), User::class);
    }
}
