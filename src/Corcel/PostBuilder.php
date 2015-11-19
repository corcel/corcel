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
     * Get only posts from a custom post type
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
        if(strstr($slug, "/"))
        {
            return $this->slugSubPage($slug);
        }

        return $this->where('post_name', $slug)->where('post_parent','=',0);
    }
    /**
     * Get only posts with a specific slug where the slug contains a page hierarchy ( page/subpage )
     * 
     * @param string slug
     * @return \Corcel\PostBuilder
     */
    public function slugSubPage($slug)
    {
        $hierarchy = array();
        $lastId = 'init';
        $slugs = explode('/', $slug);
        $matches = Post::whereIn('post_name', $slugs )
            ->get(array("ID","post_name","post_parent")
        );

        foreach($slugs as $slug) 
        {
            $hierarchy[$slug] = 'nomatch';
            foreach($matches as $match)
            {
                if($match->post_name == $slug)
                {
                    if($lastId == 'init')
                    {
                        if($match->post_parent == 0)
                        {
                            $lastId = $match->ID;
                            $hierarchy[$slug] = $match->ID;    
                        }
                        
                    }
                    if($match->post_parent <> 0)
                    {
                        if($match->post_parent == $lastId) {
                            $hierarchy[$slug] = $match->ID;
                            $lastId = $match->ID;
                        }
                    }
                }                
            }
        }

        $post = end($hierarchy);

        //no match found make sure the query returns nothing
        if($post == "nomatch") {
            return $this->where('ID', '=',-1);
        }

        //found a match
        return $this->where("ID",'=',$post);
    }

    /**
     * Paginate the results
     * 
     * @param int $perPage
     * @param int $currentPage
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function paged($perPage = 10, $currentPage = 1)
    {
        $skip = $currentPage * $perPage - $perPage;
        return $this->skip($skip)->take($perPage)->get();
    }
}
