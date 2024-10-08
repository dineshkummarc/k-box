<?php

namespace Tests\Feature\Identities;

use KBox\Capability;
use KBox\Identity;
use KBox\User;
use Tests\TestCase;

class UserIdentitiesControllerTest extends TestCase
{
    public function test_authentication_required_to_visit_connected_identities_page()
    {
        $response = $this->get(route('profile.identities.index'));

        $response->assertRedirect('/');
    }

    public function test_connected_identities_are_reported_for_user()
    {
        config(['identities.providers' => 'gitlab']);

        $user = User::factory()->create();

        $identity = Identity::factory()->registration()->create([
            'user_id' => $user->getKey(),
            'provider' => 'gitlab',
        ]);

        $response = $this->actingAs($user)
            ->get(route('profile.identities.index'));

        $response->assertStatus(200);

        $response->assertViewHasModels('identities', $identity);

        $response->assertViewHas('enabled', collect(['gitlab']));
        $response->assertViewHas('availableProviders', ['gitlab']);

        $response->assertSee(trans('identities.connected_at'));
        $response->assertSee(trans('identities.registration'));
    }

    public function test_identities_page_shows_only_my_identities()
    {
        config(['identities.providers' => 'gitlab,dropbox']);

        $user = tap(User::factory()->create(), function ($u) {
            $u->addCapabilities(Capability::$PARTNER);
            $u->markEmailAsVerified();
        });

        $identities = Identity::factory()->count(3)->create();

        $response = $this->actingAs($user)->get(route('profile.identities.index'));

        $response->assertStatus(200);

        $response->assertViewHas('identities');
        $this->assertTrue($response->viewData('identities')->isEmpty());

        $response->assertDontSee(trans('identities.connected_at'));
        $response->assertDontSee(trans('identities.registration'));
    }

    public function test_no_connected_identities_listed()
    {
        config(['identities.providers' => 'gitlab']);

        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->get(route('profile.identities.index'));

        $response->assertStatus(200);

        $response->assertViewHas('identities');
        $this->assertTrue($response->viewData('identities')->isEmpty());
        $response->assertViewHas('enabled', collect());
        $response->assertViewHas('availableProviders', ['gitlab']);

        $response->assertSee(trans('identities.nothing_connected'));
    }
    
    public function test_identity_connect_page_not_found_when_disabled_providers()
    {
        config(['identities.providers' => null]);

        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->get(route('profile.identities.index'));

        $response->assertNotFound();
    }

    public function test_unlink_requires_authentication()
    {
        config(['identities.providers' => 'gitlab']);

        $user = User::factory()->create();

        $identity = Identity::factory()->registration()->create([
            'user_id' => $user->getKey(),
            'provider' => 'gitlab',
        ]);

        $response = $this
            ->from(route('profile.identities.index'))
            ->delete(route('profile.identities.destroy', $identity->getKey()));

        $response->assertRedirect('/');
    }

    public function test_identity_can_be_unlinked()
    {
        config(['identities.providers' => 'gitlab']);

        $user = User::factory()->create();

        $identity = Identity::factory()->registration()->create([
            'user_id' => $user->getKey(),
            'provider' => 'gitlab',
        ]);

        $response = $this->actingAs($user)
            ->from(route('profile.identities.index'))
            ->delete(route('profile.identities.destroy', $identity->getKey()));

        $response->assertRedirect(route('profile.identities.index'));

        $response->assertSessionHas('flash_message', trans('identities.removed', ['provider' => $identity->provider]));

        $updatedIdentity = $identity->fresh();

        $this->assertNull($updatedIdentity);
    }

    public function test_identity_can_be_unlinked_by_owner_only()
    {
        config(['identities.providers' => 'gitlab']);

        $user = User::factory()->create();
        
        $otherUser = User::factory()->create();

        $identity = Identity::factory()->registration()->create([
            'user_id' => $user->getKey(),
            'provider' => 'gitlab',
        ]);

        $response = $this->actingAs($otherUser)
            ->from(route('profile.identities.index'))
            ->delete(route('profile.identities.destroy', $identity->getKey()));

        $response->assertForbidden();
    }

    public function test_identity_can_be_removed_via_ajax()
    {
        config(['identities.providers' => 'gitlab,dropbox']);

        $user = tap(User::factory()->partner()->create(), function ($u) {
            $u->markEmailAsVerified();
        });

        $identity = Identity::factory()->create([
            'user_id' => $user->getKey()
        ]);

        $response = $this->actingAs($user)->json('DELETE', route('profile.identities.destroy', ['identity' => $identity->getKey()]));

        $response->assertOk();
        $response->assertJson($identity->toArray());

        $this->assertNull($identity->fresh());
    }
}
