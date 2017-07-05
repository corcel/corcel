<?php

namespace Corcel;

use Illuminate\Database\Eloquent\Builder;

/**
 * Class PostBuilder
 *
 * @package Corcel
 * @author Junior Grossi <juniorgro@gmail.com>
 * TODO extract this to a trait creating scopes for Post class
 */
class PostBuilder extends Builder
{
    /**
     * Get only posts with a custom status.
     *
     * @param string $postStatus
     * @return $this
     */
    public function status($postStatus)
    {
        return $this->where('post_status', $postStatus);
    }

    /**
     * Get only published posts.
     *
     * @return $this
     */
    public function published()
    {
        return $this->status('publish');
    }

    /**
     * Get only posts from a custom post type.
     *
     * @param string $type
     * @return $this
     */
    public function type($type)
    {
        return $this->where('post_type', $type);
    }

    /**
     * Get only posts from an array of custom post types.
     *
     * @param array $types
     * @return $this
     */
    public function typeIn(array $types)
    {
        return $this->whereIn('post_type', $types);
    }

    /**
     * @param string $taxonomy
     * @param mixed $terms
     *
     * @return Builder|static
     */
    public function taxonomy($taxonomy, $terms)
    {
        return $this->whereHas('taxonomies', function ($query) use ($taxonomy, $terms) {
            $query->where('taxonomy', $taxonomy)
                ->whereHas('term', function ($query) use ($terms) {
                    $query->whereIn('slug', is_array($terms) ? $terms : [$terms]);
                });
        });
    }

    /**
     * Get only posts with a specific slug.
     *
     * @param string $slug
     * @return $this
     */
    public function slug($slug)
    {
        return $this->where('post_name', $slug);
    }

    /**
     * Paginate the results.
     *
     * @param int $perPage
     * @param int $currentPage
     * @return \Illuminate\Support\Collection
     * TODO why not using Laravel default one?
     */
    public function paged($perPage = 10, $currentPage = 1)
    {
        $skip = $currentPage * $perPage - $perPage;

        return $this->skip($skip)->take($perPage)->get();
    }
}
