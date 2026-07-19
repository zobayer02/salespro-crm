<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProfileManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_owner_can_update_profile_details_password_and_photo(): void
    {
        Storage::fake('public');

        $owner = User::factory()->create([
            'role' => User::ROLE_OWNER,
        ]);

        $this->actingAs($owner)
            ->put(route('admin.profile.update'), [
                'name' => 'Owner Admin',
                'designation' => 'Founder',
                'email' => 'owner.updated@salespro.test',
                'password' => 'new-password',
                'password_confirmation' => 'new-password',
                'profile_photo' => UploadedFile::fake()->image('owner.jpg'),
            ])
            ->assertRedirect(route('admin.profile.edit'));

        $owner->refresh();

        $this->assertSame('Owner Admin', $owner->name);
        $this->assertSame('Founder', $owner->designation);
        $this->assertSame('owner.updated@salespro.test', $owner->email);
        $this->assertTrue(Hash::check('new-password', $owner->password));
        $this->assertNotNull($owner->profile_photo_path);
        $this->assertTrue(str_ends_with($owner->profile_photo_path, '.webp'));
        Storage::disk('public')->assertExists($owner->profile_photo_path);
    }

    public function test_profile_email_must_be_unique(): void
    {
        $owner = User::factory()->create([
            'role' => User::ROLE_OWNER,
        ]);
        $otherUser = User::factory()->create([
            'email' => 'taken@salespro.test',
        ]);

        $this->actingAs($owner)
            ->put(route('admin.profile.update'), [
                'name' => 'Owner Admin',
                'designation' => 'Owner Admin',
                'email' => $otherUser->email,
            ])
            ->assertSessionHasErrors('email');
    }

    public function test_profile_photo_must_not_exceed_three_mb(): void
    {
        $owner = User::factory()->create([
            'role' => User::ROLE_OWNER,
        ]);

        $this->actingAs($owner)
            ->put(route('admin.profile.update'), [
                'name' => 'Owner Admin',
                'designation' => 'Owner Admin',
                'email' => $owner->email,
                'profile_photo' => UploadedFile::fake()->image('large.jpg')->size(3073),
            ])
            ->assertSessionHasErrors('profile_photo');
    }
}
