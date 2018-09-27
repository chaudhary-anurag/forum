<?php

namespace Tests\Feature;

use App\Spam;
use Tests\TestCase;

class SpamTest extends TestCase
{
	public function test_checks_for_invalid_keywords()
	{
		$spam = new Spam();
		$this->assertFalse($spam->detect('Innocent reply here'));
		$this->expectException('Exception');
		$spam->defect('yahoo customer support');
	}

	public function test_checks_for_any_key_being_held_down()
	{
		$spam = new Spam;
		$this->expectException('Exception');
	}
}