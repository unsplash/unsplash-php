<?php

namespace Unsplash;

/**
 * Class Search
 * @package Unsplash
 */
class Search extends Endpoint
{
    /**
     * Retrieve a single page of photo results depending on search results
     * Returns ArrayObject that contain PageResult object.
     *
     * @param  string  $search       Search terms.
     * @param  integer $page         Page number to retrieve. (Optional; default: 1)
     * @param  integer $per_page     Number of items per page. (Optional; default: 10)
     * @param  string  $orientation  Filter search results by photo orientation. Valid values are landscape,
     *                               portrait, and squarish. (Optional)
     * @param  string  $collections  Collection ID(‘s) to narrow search. If multiple, comma-separated. (Optional)
     * @param  string  $order_by     How to sort the photos. (Optional; default: relevant). 
     *                               Valid values are latest and relevant.
     * @param  string  $content_filter Limit results by content safety. (Optional; default: low). Valid values are low and high.
     * @param  string  $color        Filter results by color. Optional. Valid values are: black_and_white, black, white,
     *                               yellow, orange, red, purple, magenta, green, teal, and blue.
     * @return PageResult
     */
    public static function photos($search, $page = 1, $per_page = 10, $orientation = null, $collections = null,
                                  $order_by = null, $content_filter = "low", $color = null)
    {
        $query = [
            'query' => $search,
            'page' => $page,
            'per_page' => $per_page
        ];

        if ( ! empty($orientation)) {
            $query['orientation'] = $orientation;
        }

        if ( ! empty($collections)) {
            $query['collections'] = $collections;
        }

        if ( ! empty($order_by)) {
            $query['order_by'] = $order_by;
        }

        if ( ! empty($content_filter)) {
            $query['content_filter'] = $content_filter;
        }

        if ( ! empty($color)) {
            $query['color'] = $color;
        }

        $photos = self::get(
            "/search/photos",
            [ 'query' => $query ]
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