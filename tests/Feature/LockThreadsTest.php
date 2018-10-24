<?php
namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class LockThreadsTest extends TestCase
{
	use DatabaseMigrations;

    public function test_non_admin_may_not_lock_threads()
    {
       $this->withExceptionHandling();
       $this->signIn();
       $thread=create('App\thread',['user_id'=>auth()->id()]);
       $this->post(route('locked-threads.store',$thread))->assertStatus(403);
       $this->assertFalse(!! $thread->fresh()->locked);
    }

    public function test_admin_can_lock_threads()
    {
       $this->signIn(factory('App\User')->states('administrator')->create());
       $thread=create('App\thread',['user_id'=>auth()->id()]);
        $this->post(route('locked-threads.store',$thread));
       $this->assertTrue($thread->fresh()->locked); 
    }

     public function test_admin_can_unlock_threads()
    {
       $this->signIn(factory('App\User')->states('administrator')->create());
       $thread=create('App\thread',['user_id'=>auth()->id(),'locked'=>false]);
        $this->delete()(route('locked-threads.destroy',$thread));
       $this->assertFalse($thread->fresh()->locked,'Failed asserting that the thread was unlocked.'); 
    }

	public function test_once_locked_thread_may_not_receive_new_replies()
	{
		$this->signIn();
		$thread=create('App\Thread',['locked'=>true]);
		$this->post($thread->path().'/replies',[
			'body'=>'Foobar',
			'user_id'=>create('App\User')->id
		])->assertStatus(422);
	}
}