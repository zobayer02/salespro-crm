<?php

namespace Tests\Feature;

use App\Mail\PasswordResetOtpMail;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class PasswordResetOtpTest extends TestCase
{
    use RefreshDatabase;

    public function test_owner_can_request_password_reset_code(): void
    {
        Mail::fake();

        User::factory()->create([
            'email' => 'owner@salespro.test',
            'role' => User::ROLE_OWNER,
        ]);

        $response = $this->post(route('password.email'), [
            'email' => 'owner@salespro.test',
        ]);

        $response
            ->assertRedirect(route('password.otp'))
            ->assertSessionHas('password_reset_email', 'owner@salespro.test');

        Mail::assertSent(PasswordResetOtpMail::class, function (PasswordResetOtpMail $mail): bool {
            return preg_match('/^\d{6}$/', $mail->code) === 1;
        });

        $this->assertDatabaseHas('password_reset_tokens', [
            'email' => 'owner@salespro.test',
        ]);
    }

    public function test_owner_can_verify_code_and_reset_password(): void
    {
        Mail::fake();

        $user = User::factory()->create([
            'email' => 'owner@salespro.test',
            'password' => 'old-password',
            'role' => User::ROLE_OWNER,
        ]);

        $code = null;

        $this->post(route('password.email'), [
            'email' => $user->email,
        ]);

        Mail::assertSent(PasswordResetOtpMail::class, function (PasswordResetOtpMail $mail) use (&$code): bool {
            $code = $mail->code;

            return true;
        });

        $this->post(route('password.otp.verify'), [
            'email' => $user->email,
            'otp' => $code,
        ])
            ->assertRedirect(route('password.reset.form'))
            ->assertSessionHas('password_reset_verified', true);

        $this->get(route('password.reset.form'))
            ->assertOk()
            ->assertSee('Set your new password');

        $response = $this->post(route('password.update'), [
            'password' => 'new-secure-password',
            'password_confirmation' => 'new-secure-password',
        ]);

        $response->assertRedirect(route('password.reset.success'));

        $this->assertTrue(Hash::check('new-secure-password', $user->fresh()->password));
        $this->assertDatabaseMissing('password_reset_tokens', [
            'email' => $user->email,
        ]);
    }

    public function test_invalid_code_does_not_reset_password(): void
    {
        $user = User::factory()->create([
            'email' => 'owner@salespro.test',
            'password' => 'old-password',
            'role' => User::ROLE_OWNER,
        ]);

        DB::table('password_reset_tokens')->insert([
            'email' => $user->email,
            'token' => Hash::make('123456'),
            'created_at' => now(),
        ]);

        $response = $this->post(route('password.otp.verify'), [
            'email' => $user->email,
            'otp' => '654321',
        ]);

        $response->assertSessionHasErrors('otp');
        $this->assertTrue(Hash::check('old-password', $user->fresh()->password));
    }

    public function test_expired_code_does_not_reset_password(): void
    {
        $user = User::factory()->create([
            'email' => 'owner@salespro.test',
            'password' => 'old-password',
            'role' => User::ROLE_OWNER,
        ]);

        DB::table('password_reset_tokens')->insert([
            'email' => $user->email,
            'token' => Hash::make('123456'),
            'created_at' => now()->subMinutes(11),
        ]);

        $response = $this->post(route('password.otp.verify'), [
            'email' => $user->email,
            'otp' => '123456',
        ]);

        $response->assertSessionHasErrors('otp');
        $this->assertTrue(Hash::check('old-password', $user->fresh()->password));
    }

    public function test_reset_form_requires_verified_code(): void
    {
        $this->get(route('password.reset.form'))
            ->assertRedirect(route('password.request'));
    }
}
