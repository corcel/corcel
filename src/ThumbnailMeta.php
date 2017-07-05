<?php

namespace Corcel;

use Illuminate\Support\Arr;

/**
 * Class ThumbnailMeta
 *
 * @package Corcel
 * @author Junior Grossi <juniorgro@gmail.com>
 */
class ThumbnailMeta extends PostMeta
{
    const SIZE_THUMBNAIL = 'thumbnail';
    const SIZE_MEDIUM = 'medium';
    const SIZE_LARGE = 'large';
    const SIZE_FULL = 'full';

    /**
     * @var array
     */
    protected $with = ['attachment'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function attachment()
    {
        return $this->belongsTo(Attachment::class, 'meta_value');
    }

    /**
     * @param $size
     * @return array
     * @throws \Exception
     *
     * @todo Fix this method
     * @todo Rewrite this _wp_attachment_metadata key
     * @todo dirname() here? Fix this
     */
    public function size($size)
    {
        if ($size == self::SIZE_FULL) {
            return $this->attachment->url;
        }

        $meta = unserialize($this->attachment->meta->_wp_attachment_metadata);
        $sizes = Arr::get($meta, 'sizes');

        if (!isset($sizes[$size])) {
            throw new \Exception('Invalid size: ' . $size);
        }

        $data = Arr::get($sizes, $size);

        return array_merge($data, [
            'url' => dirname($this->attachment->url).'/'.$data['file'],
        ]);
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->attachment->guid;
    }
}
