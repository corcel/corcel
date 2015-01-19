<?php

/**
 * Corcel\PostBuilder
 *
 * @author Junior Grossi <juniorgro@gmail.com>
 */

namespace Corcel;

use Illuminate\Database\Eloquent\Builder;

class PostBuilder extends Builder
{
    /**
     * Get only posts with a custom status
     *
     * @param string $postStatus
     * @return \Corcel\PostBuilder
     */
    public function status($postStatus)
    {
        return $this->where('post_status', $postStatus);
    }

    /**
     * Get only published posts
     *
     * @return \Corcel\PostBuilder
     */
    public function published()
    {
        return $this->status('publish');
    }

    /**
     * Get only posts from a custo post type
     *
     * @param string $type
     * @return \Corcel\PostBuilder
     */
    public function type($type)
    {
        return $this->where('post_type', $type);
    }

    public function taxonomy($taxonomy, $term)
    {
        return $this->whereHas('taxonomies', function($query) use ($taxonomy, $term) {
            $query->where('taxonomy', $taxonomy)->whereHas('term', function($query) use ($term) {
                $query->where('slug', $term);
            });
        });
    }

    /**
     * Get only posts with a specific slug
     *
     * @param string slug
     * @return \Corcel\PostBuilder
     */
    public function slug($slug)
    {
        return $this->where('post_name', $slug);
    }

    /**
     * Overrides the paginate() method to a custom and simple way.
     *
     * @param int $perPage
     * @param int $currentPage
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function paginate($perPage = 10, $currentPage = 1)
    {
        $skip = $currentPage * $perPage - $perPage;
        return $this->skip($skip)->take($perPage)->get();
    }

    /**
     * Easy and a bit efficient to get the thumbnails.
     * Build in to input an array for more efficient quering, e.g. when
     * quering thumbnails for all posts in a category.
     *
     * @param  int/array $id
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function thumbnails($id)
    {
        // alias pm2 is wp_pm2 because auto prefix by Laravel QueryBuilder
        $thumbnail = PostMeta::join('postmeta as wp_pm2', 'pm2.post_id', '=', 'postmeta.meta_value')
            ->where('postmeta.meta_key', '_thumbnail_id')
            ->where('pm2.meta_key', '_wp_attached_file');

        // fields to return
        $fields = [ 'postmeta.post_id as id', 'pm2.meta_value as url' ];

        // check if $id is array or int
        if (is_array($id) && count($id) > 0) {
            return $thumbnail->whereIn('postmeta.post_id', $id)->get($fields);
        } else
            return $thumbnail->where('postmeta.post_id', (int) $id)->first($fields);
    }
}
