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
}
