<?php

namespace Ndinhbang\Jetstream\Http\Controllers\Inertia;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Gate;
use Ndinhbang\Jetstream\Actions\ValidateTeamDeletion;
use Ndinhbang\Jetstream\Contracts\CreatesTeams;
use Ndinhbang\Jetstream\Contracts\DeletesTeams;
use Ndinhbang\Jetstream\Contracts\UpdatesTeamNames;
use Ndinhbang\Jetstream\Jetstream;
use Ndinhbang\Jetstream\RedirectsActions;

class TeamController extends Controller
{
    use RedirectsActions;

    /**
     * Show the team management screen.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $teamId
     * @return \Inertia\Response
     */
    public function show(Request $request, $teamId)
    {
        $team = Jetstream::newTeamModel()->findOrFail($teamId);

        Gate::authorize('view', $team);

        return Jetstream::inertia()->render($request, 'Teams/Show', [
            'team' => $team->load('owner', 'users', 'teamInvitations'),
            'availableRoles' => array_values(Jetstream::$roles),
            'availablePermissions' => Jetstream::$permissions,
            'defaultPermissions' => Jetstream::$defaultPermissions,
            'permissions' => [
                'canAddTeamMembers' => Gate::check('addTeamMember', $team),
                'canDeleteTeam' => Gate::check('delete', $team),
                'canRemoveTeamMembers' => Gate::check('removeTeamMember', $team),
                'canUpdateTeam' => Gate::check('update', $team),
                'canUpdateTeamMembers' => Gate::check('updateTeamMember', $team),
            ],
        ]);
    }

    /**
     * Show the team creation screen.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Inertia\Response
     */
    public function create(Request $request)
    {
        Gate::authorize('create', Jetstream::newTeamModel());

        return Jetstream::inertia()->render($request, 'Teams/Create');
    }

    /**
     * Create a new team.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $creator = app(CreatesTeams::class);

        $creator->create($request->user(), $request->all());

        return $this->redirectPath($creator);
    }

    /**
     * Update the given team's name.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $teamId
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $teamId)
    {
        $team = Jetstream::newTeamModel()->findOrFail($teamId);

        app(UpdatesTeamNames::class)->update($request->user(), $team, $request->all());

        return back(303);
    }

    /**
     * Delete the given team.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $teamId
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Request $request, $teamId)
    {
        $team = Jetstream::newTeamModel()->findOrFail($teamId);

        app(ValidateTeamDeletion::class)->validate($request->user(), $team);

        $deleter = app(DeletesTeams::class);

        $deleter->delete($team);

        return $this->redirectPath($deleter);
    }
}
