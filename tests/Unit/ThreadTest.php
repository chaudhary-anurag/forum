<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Notification;

class ThreadTest extends TestCase
{
	use DatabaseMigrations;

    protected $thread;

    public function setUp()
    {
        parent::setUp();
        $this->thread=factory('App\Thread')->create();
    }

    public function test_thread_has_a_path()
    {
        $thread=create('App\Thread');
        $this->assertEquals("/threads/{$thread->channel->slug}/{$thread->slug}",$thread->path());
    }
    
    public function test_thread_has_creator()
    {
        $this->assertInstanceOf('App\User',$this->thread->creator);
    }

    public function test_thread_has_replies()
    {
        $this->assertInstanceOf('Illuminate\Database\Eloquent\Collection',$this->thread->replies);
    }

    
    public function test_thread_can_add_reply()
    {
        $this->thread->addReply([
            'body' => 'Foobar',
            'user_id' => 1
        ]);
        $this->assertCount(1,$this->thread->replies);
    }

    public function test_thread_notified_registered_subscribers_when_a_reply_is_left()
    {
        Notification::fake();
        $this->signIn()->thread->subscribe()->addReply([
            'body'=>'Foobar',
            'user_id'=>999
        ]);

        Notification::assertSentTo(auth()->user(),ThreadWasUpdated::Class);
    }

    public function test_thread_belongs_to_a_channel()
    {
        $thread = create('App\Thread');

        $this->assertInstanceOf('App\Channel',$thread->channel);
    }

    public function test_thread_can_be_subscribed_to()
    {
        $thread=create('App\Thread');
        $thread->subscribe(1);
        $this->assertEquals(1,$thread->subscriptions()->where('user_id',1)->count());
    }

    public function test_thread_can_be_unsubscribed_to()
    {
        $thread=create('App\Thread');
        $thread->subscribe($userId=1);
        $thread->unsubscribe($userId=1);
        $this->assertCount(0,$thread->subscriptions);
    }

    public function test_thread_knows_if_authenticated_user_got_subscribed_to_it()
    {
        $thread=create('App\Thread');
        $this->signIn();
        $this->assertFalse($thread->isSubscribedTo);
        $thread->subscribe();
        $this->assertTrue($thread->isSubscribedTo);
    }

    public function test_thread_can_check_if_authenticated_user_has_read_all_replies()
    {
        $this->signIn();
        $thread=create('App\Thread');
        tap(auth()->user(),function($user) use ($thread){
           $this->assertTrue($thread->hasUpdatesFor($user));
           $user->read($thread);
        $this->assertFalse($thread->hasUpdatesFalse($user)); 
        });     
    }
}
