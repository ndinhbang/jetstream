<?php

use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Ndinhbang\Jetstream\Features;
use Ndinhbang\Jetstream\Mail\TeamInvitation;

test('team members can be invited to team', function () {
    Mail::fake();

    $this->actingAs($user = User::factory()->withPersonalTeam()->create());

    $response = $this->post('/teams/'.$user->currentTeam->id.'/members', [
        'email' => 'test@example.com',
        'role' => 'admin',
    ]);

    Mail::assertSent(TeamInvitation::class);

    expect($user->currentTeam->fresh()->teamInvitations)->toHaveCount(1);
})->skip(function () {
    return ! Features::sendsTeamInvitations();
}, 'Team invitations not enabled.');

test('team member invitations can be cancelled', function () {
    Mail::fake();

    $this->actingAs($user = User::factory()->withPersonalTeam()->create());

    $invitation = $user->currentTeam->teamInvitations()->create([
        'email' => 'test@example.com',
        'role' => 'admin',
    ]);

    $response = $this->delete('/team-invitations/'.$invitation->id);

    expect($user->currentTeam->fresh()->teamInvitations)->toHaveCount(0);
})->skip(function () {
    return ! Features::sendsTeamInvitations();
}, 'Team invitations not enabled.');
