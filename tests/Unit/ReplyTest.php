<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class ReplyTest extends TestCase
{
	use DatabaseMigrations;
	public function test_it_has_owner()
	{
		$reply=factory('App\Reply')->create();
		$this->assertInstanceOf('App\User',$reply->owner);
	}

	public function test_it_knows_if_it_was_just_published()
	{
		$reply=create('App\Reply');
		$this->assertTrue($reply->wasJustPublished());
		$reply->created_at=Carbon::now()->subMonth();
		$this->assertFalse($reply->wasJustPublished());
	}

	public function test_it_can_detect_all_mentioed_users_in_the_body()
	{
		$reply=create('App\Reply',[
			'body'=>'@jae wants to talk to @joe.'
		]);
        $this->assertEquals(['jae','joe'],$reply->mentionedUsers());
	}

	public function test_wraps_mentioned_usernames_in_the_body_within_Anchor_tags()
	{
        $reply=new \App\Reply([
			'body'=>'Hello @jae.'
		]);
       $this->assertEquals(
          'Hello <a href="/profiles/jae">@jae</a>',$reply->body
       ); 
	}
}
