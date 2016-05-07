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
}
