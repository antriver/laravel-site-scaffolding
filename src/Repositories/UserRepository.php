<?php

namespace Antriver\LaravelSiteUtils\Repositories;

use Antriver\LaravelSiteUtils\Models\User;
use Carbon\Carbon;
use DB;
use Tmd\LaravelRepositories\Base\AbstractCachedRepository;

class UserRepository extends AbstractCachedRepository
{
    /**
     * Return the fully qualified class name of the Models this repository returns.
     *
     * @return string
     */
    public function getModelClass()
    {
        return User::class;
    }

    public function getMaxId()
    {
        $row = DB::selectOne('SELECT MAX(`id`) AS `id` FROM `users`');

        return $row->id;
    }

    /**
     * Returns the number of users that have signed up today.
     */
    public function countNewToday(): int
    {
        $row = DB::selectOne(
            'SELECT COUNT(*) AS `count` FROM `user_profiles` `p` WHERE `p`.`createdAt` >= ?',
            [
                (new Carbon('midnight today'))->toDateTimeString(),
            ]
        );

        return $row->count;
    }
}
