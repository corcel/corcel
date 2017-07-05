<?php

namespace Corcel;

use Illuminate\Database\Eloquent\Builder;

/**
 * Class UserBuilder
 *
 * @package Corcel
 * @author Mickael Burguet <www.rundef.com>
 * @author Junior Grossi <juniorgro@gmail.com>
 */
class UserBuilder extends Builder
{
    /**
     * Paginate the results.
     *
     * @param int $perPage
     * @param int $currentPage
     *
     * @return \Illuminate\Database\Eloquent\Collection
     * TODO why not using default Laravel page?
     */
    public function paged($perPage = 10, $currentPage = 1)
    {
        $skip = $currentPage * $perPage - $perPage;

        return $this->skip($skip)->take($perPage)->get();
    }
}
