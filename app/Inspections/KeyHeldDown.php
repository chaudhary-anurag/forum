<?php 

namespace App\Inspections;
use Exception;

class KeyHeldDown
{
	public function detect($body)
	{
       if(preg_match('/(.)\\1{4,1}/',$body)){
   	  	throw new Exception('Your reply contains spam.');
   	  	
   	  }
	}
}