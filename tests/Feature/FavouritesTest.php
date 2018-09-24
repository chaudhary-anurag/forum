<?php
namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class FavouritesTest extends TestCase
{
	use DatabaseMigrations;

	public function test_guest_cant_favourite_anything()
	{
		$this->withExceptionHandling()
           ->post('replies/1/favourites')
           ->assertRedirect('/login');
	}

	public function test_authenticated_user_can_favourite_any_reply()
	{
		$this->signIn();
		$reply =  create('App\Reply');
		$this->post('replies/'.$reply->id.'/favourites');
		$this->assertCount(1,$reply->favourites);
	}

	public function test_authenticated_user_can_unfavourite_any_reply()
	{
		$this->signIn();
		$reply=create('App\Reply');
		$reply->favourite();
		$this->delete('replies/'.$reply->id.'/favourites');
		$this->assertCount(0,$reply->favourites);
	}

	public function test_authenticated_user_can_favourite_reply_once()
	{
		$this->signIn();
		$reply =  create('App\Reply');
		$this->post('replies/'.$reply->id.'/favourites');
	    $this->post('replies/'.$reply->id.'/favourites');
		$this->assertCount(1,$reply->favourites);
	}
}