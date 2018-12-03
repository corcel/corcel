<?php

namespace Corcel\Model;

use Illuminate\Database\Eloquent\Builder;

/**
 * Class Page
 *
 * @package Corcel\Model
 * @author Junior Grossi <juniorgro@gmail.com>
 */
class Page extends Post
{
    /**
     * @var string
     */
    protected $postType = 'page';

    /**
     * @param Builder $query
     * @return mixed
     */
    public function scopeHome(Builder $query)
    {
        return $query
            ->where('ID', '=', Option::get('page_on_front'))
            ->limit(1);
    }
}
