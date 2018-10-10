<?php
 namespace Tests\Unit;
 use Tests\TestCase;
 use Illuminate\Foundation\Testing\DatabaseMigrations;
 class UserTest extends TestCase
 {
 	use DatabaseMigrations;

 	public function test_user_can_fetch_their_most_recent_reply()
 	{
 		$user=create('App\User');
        $reply=create('App\Reply',['user_id'=>$user->id]);
        $this->assertEquals($reply->id,$user->lastReply->id);
 	}

 	public function test_user_can_determine_theri_avatar_path()
 	{
 		$user = create('App\User');
 		$this->assertEquals('avatars/default.jpg',$user->avatar());
 		$user->avatar_path='avatars/me.jpg';
 		$this->assertEquals('avatars/me.jpg',$user->avatar());
 	}

 }