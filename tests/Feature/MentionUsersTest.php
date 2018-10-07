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

    function test_fetch_all_mentioned_users_starting_with_the_given_characters()
    {
        create('App\User',['name'=>'johndoe']);
        create('App\User',['name'=>'johndoe2']);
        create('App\User',['name'=>'janedoe']);
        $results=$this->json('GET','/api/users',['name'=>'john']);
        $this->assertCount(2,$results->json());
    }
}
