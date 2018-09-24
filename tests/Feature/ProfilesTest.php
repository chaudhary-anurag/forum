<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class ProfileTest extends TestCase
{
	use DatabaseMigrations;

	public function test_user_has_a_profile()
	{
		$user=create('App\User');
		$this->get("/profiles/{$user->name}")
		     ->assertSee($user->name);
	}

 	public function test_profile_displays_all_threads_created_by_associated_user()
	{
		$this->signIn();
		$thread=create('App\Thread',['user_id'=>auth()->id()]);
		$this->get("/profiles/".auth()->user()->name)
		     ->assertSee($thread->title)
		     ->assertSee($thread->body);
	}
	
}