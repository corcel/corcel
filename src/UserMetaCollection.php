<?php

/**
 * Corcel\UserMetaCollection.
 *
 * @author Mickael Burguet <www.rundef.com>
 */

namespace Corcel;

use Illuminate\Database\Eloquent\Collection;

class UserMetaCollection extends Collection
{
    protected $changedKeys = [];

    /**
     * Search for the desired key and return only the row that represent it.
     *
     * @param string $key
     *
     * @return string
     */
    public function __get($key)
    {
        foreach ($this->items as $item) {
            if ($item->meta_key == $key) {
                return $item->meta_value;
            }
        }
    }

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
