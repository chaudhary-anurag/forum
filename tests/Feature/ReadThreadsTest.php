<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class ReadThreadsTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    use DatabaseMigrations;

    public function setUp()
    {
        parent::setUp();
        $this->thread=factory('App\Thread')->create();
    }

    public function test_user_browses_all_threads()
    {
        $response=$this->get('/threads');
        $response->assertSee($this->thread->title);
    }

    public function test_user_reads_single_thread()
    { 
        
        $response=$this->get('/threads/'.$this->thread->channel->slug.'/'.$this->thread->id);
        $response->assertSee($this->thread->title);
    }

    public function test_user_can_filter_threads_according_to_channnel()
    {
       $channel=create('App\Channel');
       $threadInChannel=create('App\Thread',['channel_id'=>$channel->id]);
       $threadNotInChannel=create('App\Thread');
       $this->get('/threads/'.$channel->slug)
            ->assertSee($threadInChannel->title)
            ->assertDontSee($threadNotInChannel->title);
    }

    public function test_user_can_filter_threads_by_any_username()
    {
       $this->signIn(create('App\User',['name'=>'John']));
       $threadByJohn=create('App\Thread',['user_id'=>auth()->id()]);
       $otherThread=create('App\Thread');
       $this->get('threads?by=John')
            ->assertSee($threadByJohn->title)
            ->assertDontSee($otherThread->title);
    }

    public function test_user_can_filter_threads_by_popularity()
    {
      $threadWithTwoReplies=create('App\Thread');
      create('App\Reply',['thread_id'=>$threadWithTwoReplies->id],2);
      $threadWithThreeReplies=create('App\Thread');
      create('App\Reply',['thread_id'=>$threadWithThreeReplies->id],3);
      $threadWithNoReplies=$this->thread;
      $response=$this->getJson('threads?popular=1')->json();

      $this->assertEquals([3,2,0],array_column($response['data'],'replies_count'));
    }

    public function test_user_can_filter_threads_by_the_unanswered_threads()
    {
       $thread=create('App\Thread');
       create('App\Reply',['thread_id'=>$thread->id]);
       $response=$this->getJson('threads?unanswered=1')->json();
       $this->assertCount(1,$response['data']);
    }

    public function test_user_can_request_all_replies_for_given_thread(){
      $thread=create('App\Thread');
      create('App\Reply',['thread_id'=>$thread->id],2);
      $response=$this->getJson($thread->path().'/replies')->json();
      $this->assertCount(1,$response['data']);
      $this->assertEquals(2,$response['total']);
    }

    public function test_record_a_new_visit_each_time_thread_is_read()
    {
       $thread=create('App\Thread');
       $this->assertSame(0,$thread->visits);
       $this->call('GET',$thread->path());
       $this->assertEquals(1,$thread->fresh()->visits);
    }
}
