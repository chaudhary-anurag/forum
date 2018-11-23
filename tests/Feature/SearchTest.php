<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SearchTest extends TestCase
{
   use RefreshDatabase;

    public function test_user_can_search_threads()
    {
    	config(['scout.driver'=>'algolia']);
    	$search='foobar';
    	create('App\Thread',[],2);
    	create('App\Thread',['body'=>"A thread with the ($search) term."],2);
    	do { sleep(0.20);
             $results=$this->getJson("/threads/search?q={$search}")->json()['data'];
    	} while (empty($results));
    	$this->assertCount(2,$results);
        Thread::latest()->take(4)->unsearchable(); 
    }
}