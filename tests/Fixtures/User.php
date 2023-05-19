<?php

namespace Ndinhbang\Jetstream\Tests\Fixtures;

use App\Models\User as BaseUser;
use Ndinhbang\Jetstream\HasProfilePhoto;
use Ndinhbang\Jetstream\HasTeams;
use Laravel\Sanctum\HasApiTokens;

class User extends BaseUser
{
    use HasApiTokens, HasTeams, HasProfilePhoto;

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];
}
