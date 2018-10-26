<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UpdateThreadsTest extends TestCase
{
	use RefreshDatabase;

	public function setUp()
	{
       parent::setUp();
       $this->withExceptionHandling();
       $this->signIn();
	}

	public function test_thread_requires_title_and_body_to_be_updated()
    {
        create('App\Thread',['user_id'=>auth()->id()]);
        $this->patch($thread->path(),[
            'title'=>'changed',
        ])->assertSessionHasErrors('body');
         $this->patch($thread->path(),[
            'body'=>'changed',
        ])->assertSessionHasErrors('title');
    }

    public function test_unauthorized_user_cant_update_threads()
    {
        create('App\Thread',['user_id'=>create('App\User')->id]);
        $this->patch($thread->path(),[])->assertStatus(403);
    }

    public function test_thread_can_be_updated_by_the_creator()
    {
        create('App\Thread',['user_id'=>auth()->id()]);
        $this->patch($thread->path(),[
            'title'=>'changed',
            'body'=>'Changed'
        ]);
        tap($thread->fresh(),function($thread){
               $this->assertEquals('changed',$thread->title);
               $this->assertEquals('Changed',$thread->body);
        });
    }
}
