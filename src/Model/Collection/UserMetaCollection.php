<?php

namespace Corcel\Model\Collection;

use Corcel\Model\Meta\UserMeta;
use Corcel\Traits\MetaCollection;
use Illuminate\Database\Eloquent\Collection;

/**
 * Class UserMetaCollection
 *
 * @package Corcel\Model\Collection
 * @author Mickael Burguet <www.rundef.com>
 * @author Junior Grossi <juniorgro@gmail.com>
 */
class UserMetaCollection extends Collection
{
    use MetaCollection;

    /**
     * @var array
     */
    protected $changedKeys = [];

    /**
     * @param $key
     * @param $value
     * TODO remove this, is it necessary?
     */
    public function __set($key, $value)
    {
        $this->changedKeys[] = $key;

        foreach ($this->items as $item) {
            if ($item->meta_key == $key) {
                $item->meta_value = $value;

                return;
            }
        }

        $item = new UserMeta([
            'meta_key' => $key,
            'meta_value' => $value,
        ]);

        $this->push($item);
    }

    /**
     * @param $userId
     * TODO is this necessary? Remove this
     */
    public function save($userId)
    {
        $this->each(function ($item) use ($userId) {
            if (in_array($item->meta_key, $this->changedKeys)) {
                $item->user_id = $userId;
                $item->save();
            }
        });
    }
}
