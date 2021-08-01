<?php

namespace Unsplash;

/**
 * Class Topic
 * @package Unsplash
 * @property int $id
 * @property array $user
 */
class Topic extends Endpoint
{
    /**
     * Retrieve the a topic object from the ID or slug specified
     *
     * @param  string $id ID of the photo
     * @return Photo
     */
    public static function find($id)
    {
        $photo = json_decode(self::get("/topics/{$id}")->getBody(), true);

        return new self($photo);
    }

    /**
     * Retrieve all the topics on a specific page.
     * Returns an ArrayObject that contains Topic objects.
     *
     * @param  string $ids  Limit to only matching topic ids or slugs. (Optional; Comma separated string)
     * @param  integer $page Page from which the photos need to be retrieve
     * @param  integer $per_page Number of element in a page
     * @param string $order_by Order in which to retrieve photos
     * @return ArrayObject of Photos
     */
    public static function all($ids= null, $page = 1, $per_page = 10, $order_by = 'latest')
    {

        $query = [
            'query' => ['page' => $page, 'per_page' => $per_page, 'order_by' => $order_by]
        ];

        if (! empty($ids)) {
            $query['ids'] = $ids;
        }

        $topics = self::get("/topics", $query);

        $topicsArray = self::getArray($topics->getBody(), get_called_class());

        return new ArrayObject($topicsArray, $topics->getHeaders());
    }

    /**
     * Retrieve all the photos on a specific topic.
     * Returns an ArrayObject that contains Photo objects.
     *
     * @param  string $id  The topics’s ID or slug. Required.
     * @param  integer $page Page from which the photos need to be retrieve
     * @param  integer $per_page Number of element in a page
     * @param  string $orientation  Filter by photo orientation. (Optional; Valid values: landscape, portrait, squarish)
     * @param string $order_by Order in which to retrieve photos
     * @return ArrayObject of Photos
     */
    public static function photos($id, $page = 1, $per_page = 10, $order_by = 'latest', $orientation = null)
    {

        $query = [
            'query' => ['page' => $page, 'per_page' => $per_page, 'order_by' => $order_by]
        ];

        if (! empty($orientation)) {
            $query['orientation'] = $orientation;
        }

        $photos = self::get("/topics/{$id}/photos", $query);

        $photosArray = self::getArray($photos->getBody(), Photo::class);

        return new ArrayObject($photosArray, $photos->getHeaders());
    }


}
