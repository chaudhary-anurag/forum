<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class MentionUsersTest extends TestCase
{
	use DatabaseMigrations;
    function test_mentioned_users_in_the_reply_are_notified()
    {
        $john=create('App\User',['name'=>'joe']);
        $this->signIn($john);
        $jane=create('App\User',['name'=>'jae']);
        $thread=create('App\Thread');
        $reply=make('App\Reply',[
        	'body'=>'@jae look at this.'
        ]);
        $this->json('post',$thread->path().'/replies',$reply->toArray());
        $this->assertCount(1,$jane->notifications);
    }
}
