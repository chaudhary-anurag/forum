<?php

namespace Tests\Feature;

use App\Inspections\Spam;
use Tests\TestCase;

class SpamTest extends TestCase
{
	public function test_checks_for_invalid_keywords()
	{
		$spam = new Spam;
		/*$this->assertFalse($spam->detect('Innocent reply here'));*/
		$this->expectException('Exception');
		$spam->detect('yahoo customer support');
	}

	public function test_checks_for_any_key_being_held_down()
	{
		$spam = new Spam;
        
		$this->expectException('Exception');
		$spam->detect('Hello world aaaaaaaa');
	}
}