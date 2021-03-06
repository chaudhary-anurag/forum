<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Activity;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class CreateThreadsTest extends TestCase
{
	use DatabaseMigrations;

    public function setUp()
    {
        parent::setUp();
        app()->singleton(Recaptcha::class,function(){
          return \Mockery::mock(Recaptcha::class,function($m){
               $m->shouldReceive('passes')->andReturn(true);
          });
        });
    }

    public function test_user_can_create_new_threads()
    {
        $response=$this->publishThread(['title'=>'Some title','body'=>'Body']);
        $this->get($response->headers->get('Location'))
             ->assertSee('Some Title')
             ->assertSee('Body');
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

    public function test_thread_requires_recaptcha_verification()
    {
        unset(app()[Recaptcha::class]);
        $this->publishThread(['g-recaptcha-response'=>'test'])
             ->assertSessionHasErrors('g-recaptcha-response');
    }

    public function test_thread_requires_valid_channel()
    {
    	factory('App\Channel',2)->create();
    	$this->publishThread(['channel_id'=>null])
    	     ->assertSessionHasErrors('channel_id');
    	$this->publishThread(['channel_id'=> 9999])
    	     ->assertSessionHasErrors('channel_id');

    }

    public function test_thread_requires_unique_slug()
    {
       $this->signIn();
       $thread=create('App\Thread',['title'=>'Foo Title');
      $this->assertEquals($thread->fresh()->slug,'foo-title');
       $thread=$this->postJson(route('threads'),$thread->toArray() + ['g-recaptcha-response'=>'token'])->json();
       $this->assertEquals("foo-title-{$thread['id']}",$thread['slug']);
    }

    public function test_thread_with_a_title_that_ends_in_a_number_should_generate_proper_slug()
    {
       $this->signIn();
       $thread=create('App\Thread',['title'=>'Foo Title 24']);
       $thread=$this->postJson(route('threads'),$threads->toArray() + ['g-recaptcha-response'=>'token'])->json();
       $this->assertEquals("foo-title-24-{$thread['id']}",$thread['slug']);
    }

    public function publishThread($overrides=[])
    {
    	$this->withExceptionHandling()->signIn();
    	$thread = make('App\Thread',$overrides);
    	return $this->post(route('threads'),$thread->toArray()+ ['g-recaptcha-response'=>'token']);
    }

    public function test_guest_cant_create_threads()
    {
        $this->withExceptionHandling()     
    	     ->post(route('threads'))
             ->assertRedirect(route('login'));

         $this->withExceptionHandling()     
             ->get('/threads/create')
             ->assertRedirect(route('login'));
    }

    public function test_new_users_must_first_confirm_their_email_Address_before_creating_threads()
    {
        $user=factory('App\User')->states('unconfirmed')->create();
        $this->signIn($user);
        $thread = make('App\Thread');
        $this->post(route('threads'),$thread->toArray())
             ->assertRedirect(route('threads'))
             ->assertSessionHas('flash','yOu must first confirm your email address.');
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

