<?php

namespace Corcel\Laravel\Observers;

use Carbon\Carbon;
use Corcel\Model\User;

/**
 * Class UserObserver
 *
 * @package Corcel\Observers
 * @author Junior Grossi <juniorgro@gmail.com>
 */
class UserObserver
{
    /**
     * @param User $user
     */
    public function creating(User $user): void
    {
        $user->fill([
            'user_nicename' => $user->user_nicename ?: $user->user_login,
            'user_registered' => Carbon::now(),
            'display_name' => $user->display_name ?: $user->user_login,
        ]);
    }
}
