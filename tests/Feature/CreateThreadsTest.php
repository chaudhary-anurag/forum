<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Activity;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class CreateThreadsTest extends TestCase
{
	use DatabaseMigrations;

    public function test_authenticated_user_can_create_new_threads()
    {
        $this->signIn();
        $thread = make('App\Thread');
        $response=$this->post('/threads',$thread->toArray());
        $this->get($response->headers->get('Location'))
             ->assertSee($thread->title)
             ->assertSee($thread->body);
    }

    public function test_thread_requires_title()
    {
    	$this->publishThread(['title'=>null])
    	     ->assertSessionHasErrors('title');

    }

     public function test_thread_requires_body()
    {
    	$this->publishThread(['body'=>null])
    	     ->assertSessionHasErrors('body');

    }

    public function test_thread_requires_valid_channel()
    {
    	factory('App\Channel',2)->create();
    	$this->publishThread(['channel_id'=>null])
    	     ->assertSessionHasErrors('channel_id');
    	$this->publishThread(['channel_id'=> 9999])
    	     ->assertSessionHasErrors('channel_id');

    }


    public function publishThread($overrides=[])
    {
    	$this->withExceptionHandling()->signIn();
    	$thread = make('App\Thread',$overrides);
    	return $this->post('/threads',$thread->toArray());
    }

    public function test_guest_cant_create_threads()
    {
        /*   $this->expectException('Illuminate\Auth\AuthenticationException');
    	$thread=make('App\Thread');
    	$this->post('/threads',$thread->toArray()); */ 

        $this->withExceptionHandling()     
    	     ->post('/threads')
             ->assertRedirect('/login');

         $this->withExceptionHandling()     
             ->get('/threads/create')
             ->assertRedirect('/login');

    }

    public function test_authorized_user_can_delete_threads()
    {
        $this->signIn();
        $thread=create('App\Thread',['user_id'=>auth()->id()]);
        $reply=create('App\Reply',['thread_id'=>$thread->id]);
        $response=$this->json('DELETE',$thread->path());
             $response->assertStatus(204);
        $this->assertDatabaseMissing('threads',['id'=>$thread->id]);
        $this->assertDatabaseMissing('replies',['id'=>$reply->id]);
        $this->assertEquals(0,Activity::count());
    }

    public function test_unauthorized_users_cant_delete_threads()
    {
        $this->withExceptionHandling();
        $thread=create('App\Thread');
        $this->delete($thread->path())
             ->assertRedirect('/login');
        $this->signIn();
        $this->delete($thread->path())
             ->assertStatus(403);

    }
}

