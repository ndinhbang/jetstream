<?php

namespace Ndinhbang\Jetstream\Actions;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;
use Ndinhbang\Jetstream\Events\TeamMemberUpdated;
use Ndinhbang\Jetstream\Jetstream;
use Ndinhbang\Jetstream\Rules\Role;

class UpdateTeamMemberRole
{
    /**
     * Update the role for the given team member.
     *
     * @param  mixed  $user
     * @param  mixed  $team
     * @param  int  $teamMemberId
     * @param  string  $role
     * @return void
     */
    public function update($user, $team, $teamMemberId, string $role)
    {
        Gate::forUser($user)->authorize('updateTeamMember', $team);

        Validator::make([
            'role' => $role,
        ], [
            'role' => ['required', 'string', new Role],
        ])->validate();

        $team->users()->updateExistingPivot($teamMemberId, [
            'role' => $role,
        ]);

        TeamMemberUpdated::dispatch($team->fresh(), Jetstream::findUserByIdOrFail($teamMemberId));
    }
}
