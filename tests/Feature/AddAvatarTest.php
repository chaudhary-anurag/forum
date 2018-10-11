<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class AddAvatarTest extends TestCase
{
	use DatabaseMigrations;

	public function test_only_members_can_add_avatar()
	{
		$this->withExceptionHandling();
		$this->json('POST','api/users/1/avatar')
		     ->assertStatus(401);
	}

	public function test_valid_avatar_must_be_provided()
	{
		$this->withExceptionHandling()->signIn();
		$this->json('POST','api/users/'.auth()->id().'/avatar',[
			'avatar' => 'not-an-image'
		])->assertStatus(422);
	}

	public function test_user_may_add_an_avatar_to_their_profile()
	{
		$this->signIn();
		Storage::fake('public');
        $this->json('POST','api/users/'.auth()->id().'/avatar',[
			'avatar' =>$file = UploadedFile::fake()->image('avatar.jpg')
		]);
		$this->assertEquals(asset('avatars/'.$file->hashName()),auth()->user()->avatar_path);
        Storage::disk('public')->assertExists('avatars/'.$file->hashName());
	}
}