<?php

namespace Corcel\Model;

/**
 * Class CustomLink
 *
 * @package Corcel\Model
 * @author Junior Grossi <juniorgro@gmail.com>
 */
class CustomLink extends Post
{
    /**
     * @var string
     */
    protected $table = 'posts';

    /**
     * @var string
     */
    protected $primaryKey = 'ID';

    /**
     * @param string $key
     * @return mixed
     */
    public function __get($key)
    {
        if ($key === 'url') {
            return $this->meta->_menu_item_url;
        }

        if ($key === 'link_text') {
            return $this->post_title;
        }

        return parent::__get($key);
    }
}
