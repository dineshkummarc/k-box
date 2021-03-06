<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use KBox\Capability;
use KBox\File;
use KBox\Jobs\CalculateUserUsedQuota;
use KBox\User;
use KBox\UserQuota;

class CalculateUserUsedQuotaJobTest extends TestCase
{
    use DatabaseTransactions;

    public function test_user_used_quota_is_calculated()
    {
        $user = tap(factory(User::class)->create(), function ($u) {
            $u->addCapabilities(Capability::$PARTNER);
        });

        $files = factory(File::class, 5)->create([
            'user_id' => $user->id,
            'size' => 4
        ]);

        (new CalculateUserUsedQuota($user))->handle();

        $quota = UserQuota::forUser($user)->first();

        $this->assertNotNull($quota);
        $this->assertEquals(20, $quota->used);
        $this->assertFalse($quota->is_full);
        $this->assertFalse($quota->is_above_threshold);
    }
}
