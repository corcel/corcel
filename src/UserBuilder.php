<?php

/**
 * Corcel\UserBuilder
 *
 * @author Mickael Burguet <www.rundef.com>
 */

namespace Corcel;

use Illuminate\Database\Eloquent\Builder;

class UserBuilder extends Builder
{
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

    /**
     * Add nested meta exists conditions to the query.
     *
     * @param string $metaKey
     * @param string $metaValue
     * @return UserBuilder|static
     */
    public function hasMeta($metaKey, $metaValue)
    {
        return $this->whereHas('meta', function ($query) use ($metaKey, $metaValue) {
            $query->where('meta_key', $metaKey)
                ->where('meta_value', $metaValue)
            ;
        });
    }
}
