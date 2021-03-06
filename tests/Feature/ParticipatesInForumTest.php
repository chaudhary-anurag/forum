<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Auth\AuthenticationException;

class ParticipatesInForumTest extends TestCase
{
	use DatabaseMigrations;

	public function test_unauthenticated_users_may_not_Add_replies()
	{   
		$this->withExceptionHandling()
             ->post('/threads/some_channel/1/replies',[])
             ->assertRedirect('/login');
	}

    public function test_authenticated_user_may_Participate_in_threads()
    {
        $this->be($user=factory('App\User')->create());
        $thread = factory('App\Thread')->create();

        $reply=factory('App\Reply')->make();
        $this->post($thread->path().'/replies',$reply->toArray());
        $this->assertDatabaseHas('replies',['body'=>$reply->body]);
        $this->assertEquals(1,$thread->fresh()->replies_count);
    }

    public function test_reply_requires_body()
    {
    	$this->withExceptionHandling()->signIn();
    	$thread = factory('App\Thread')->create();

        $reply=factory('App\Reply')->make(['body'=>null ]);
    	 $this->post($thread->path().'/replies',$reply->toArray())
    	      ->assertSessionHasErrors('body');
    }

    public function test_unauthorized_users_cannot_delete_replies()
    {
        $this->withExceptionHandling();
        $reply=create('App\Reply');
        $this->delete("/replies/{$reply->id}")
        ->assertRedirect('login');
        $this->signIn()
             ->delete("/replies/{$reply->id}")
             ->assertStatus(403);
    }

    public function test_authorized_users_can_delete_replies()
    {
      $this->signIn();
      $reply=create('App\Reply',['user_id'=>auth()->id()]);
      $this->delete("/replies/{$reply->id}")
           ->assertStatus(302);
      $this->assertDatabaseMissing('replies',['id'=>$reply->id]);
      $this->assertEquals(0,$reply->threads->fresh()->replies_count);
    }

    public function test_authorized_users_can_update_replies()
    {
        $this->signIn();
      $reply=create('App\Reply',['user_id'=>auth()->id()]);
      $updatedReply='Your body has been updated';
      $this->patch("/replies/{$reply->id}",['body'=>$updatedReply]);
      $this->assertDatabaseHas('replies',['id'=>$reply->id, 'body'=>$updatedReply]);
    }
    public function test_unauthorized_users_cannot_update_replies()
    {
        $this->withExceptionHandling();
        $reply=create('App\Reply');
        $this->patch("/replies/{$reply->id}")
        ->assertRedirect('login');
        $this->signIn()
             ->patch("/replies/{$reply->id}")
             ->assertStatus(403);
    }

    public function test_replies_that_contain_spam_may_not_be_created()
    {
       $this->withExceptionHandling();
       $this->signIn();
       $thread=create('App\Thread');
       $reply=make('App\Reply',[
        'body'=> 'Yahoo Customer Support'
       ]);
       $this->json('post',$thread->path().'/replies',$reply->toArray())
            ->assertStatus(422);
    }

    public function test_users_may_only_reply_maximum_of_once_per_minute()
    {
       $this->withExceptionHandling();
       $this->signIn();
       $thread=create('App\Thread');
       $reply=make('App\Reply',[
        'body'=> 'My casual reply.'
       ]);
       $this->post($thread->path().'/replies',$reply->toArray())
            ->assertStatus(200);
       $this->post($thread->path().'/replies',$reply->toArray())
            ->assertStatus(422);
    }
}
