<?php 

namespace Corcel;

class ThumbnailMeta extends PostMeta
{
    protected $with = ['attachment'];

    public function attachment()
    {
        return $this->belongsTo('Corcel\Attachment', 'meta_value');
    }

    public function __toString()
    {
        return $this->attachment->guid;
    }
}