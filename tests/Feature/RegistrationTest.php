<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Mail\PleaseConfirmYourEmail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class RegistrationTest extends TestCase
{
	use DatabaseMigrations;

	public function test_confirmation_mail_is_sent_upon_registration()
	{
		Mail::fake();
		$this->post(route('register'),[
			'name'=>'joe',
			'email'=>'test@test.com',
			'password'=>'foobar',
			'password_confirmation'=>'foobar'
		]);
        Mail::assertQueued(PleaseConfirmYourEmail::class);
	}

	public function test_user_can_fully_confirm_their_email_address()
	{
		Mail::fake();
		$this->post(route('register'),[
			'name'=>'joe',
			'email'=>'test@test.com',
			'password'=>'foobar',
			'password_confirmation'=>'foobar'
		]);

		$user=User::whereName('joe')->first();
		$this->assertFalse($user->confirmed);
		$this->assertNotNull($user->confirmation_token);
		$this->get(route('register.confirm',['token'=> $user->confirmation_token]))
		    ->assertRedirect(route('threads'));
	    tap($user->fresh(), function ($user){
                $this->assertTrue($user->confirmed);
		        $this->assertNull($user->confirmation_token);
		    });	
	}

	public function test_confirming_invalid_token()
	{
	   $this->get(route('register.confirm',['token'=> 'invalid']))
	        ->assertRedirect(route('threads'))
	        ->assertSessionHas('flash','Unknown token.');
	}
}