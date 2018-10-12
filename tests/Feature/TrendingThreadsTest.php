<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Trending;
use Illuminate\Support\Facades\Redis;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class TrendingThreadsTest extends TestCase
{
	use DatabaseMigrations;

    protected function setUp()
    {
       parent::setUp();
       $this->trending=new Trending();
       $this->trending->reset();
    }

	public function test_increments_a_threads_score_each_time_it_is_read(){
		$this->assertEmpty($this->trending->get());
        $thread=create('App\Thread');
        $this->call('GET',$thread->path());
        $this->assertCount(1,$trending=$this->trending->get());
        $this->assertEquals($thread->title,$trending[0]->title);
	}
}