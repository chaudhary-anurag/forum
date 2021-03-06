<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class BestReplyTest extends TestCase
{
	use DatabaseMigrations;

	public function test_thread_creator_may_mark_any_reply_as_the_best_reply()
	{
		$this->signIn();
		$thread=create('App\Thread',['user_id'=>auth()->id()]);
		$replies=create('App\Reply',['thread_id'=>$thread->id],2);
		$this->assertFalse($replies[1]->isBest());
		$this->postJson(route('best-replies.store',[$replies[1]->id]));
        $this->assertTrue($replies[1]->fresh()->isBest());
	}

	public function test_only_thread_creator_can_mark_a_reply_to_be_best()
	{
		$this->withExceptionHandling();
		$this->signIn();
		$thread=create('App\Thread',['user_id'=>auth()->id()]);
		$replies=create('App\Reply',['thread_id'=>$thread->id],2);
		$this->signIn(create('App\User'));
		$this->postJson(route('best-replies.store',[$replies[1]->id]))->assertStatus(403);
		$this->assertFalse($replies[1]->fresh()->isBest());
	}

	public function test_if_a_best_reply_is_deleted_then_thread_should reflect_that()
	{   
		$this->signIn();
		$replies=create('App\Reply',['user_id'=>auth()->id()]);
		$reply->thread->markBestReply($reply);
		$this->deleteJson(route('replies.destroy',$reply));
		$this->assertNull($reply->thread->fresh()->best_reply_id);
	}
}