<?php

use Faker\Generator as Faker;

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| This directory should contain each of the model factory definitions for
| your application. Factories provide a convenient way to generate new
| model instances for testing / seeding your application's database.
|
*/

$factory->define(App\User::class, function (Faker $faker) {
    return [
        'name' => $faker->name,
        'email' => $faker->unique()->safeEmail,
        'password' => '$2y$10$TKh8H1.PfQx37YgCzwiKb.KjNyWgaHb9cbcoQgdIVFlYg7B77UdFm', // secret
        'remember_token' => str_random(10),
        'confirmed'=>true
    ];
});

$factory->state(App\User::class,'unconfirmed',function () {
       return [
          'confirmed' => false
       ];
});

$factory->define(App\Thread::class, function (Faker $faker) {
    return [
    	'user_id'=> function(){
    		return factory('App\User')->create()->id;
    	},
        'channel_id'=> function(){
            return factory('App\Channel')->create()->id;
        },
        'title'=>$faker->sentence,
        'body'=>$faker->paragraph,
        'visits'=>0
    ];
});

$factory->define(App\Channel::class,function(Faker $faker){
    $name=$faker->word;
    return [
        'name' => $name,
        'slug' => $name
    ];
});

$factory->define(App\Reply::class, function (Faker $faker) {
    return [
        //
       'thread_id'=>function(){
        	return factory('App\Thread')->create()->id;
        	},
        'user_id'=>function(){
        	return factory('App\User')->create()->id;
        },
        
         'body'=>$faker->paragraph
    ];
});

$factory->define(\Illuminate\Notifications\DatabaseNotification::class, function (Faker $faker) {
    return [
       'id' =>'\Ramsey\Uuid\Uuid::uuid4()->toString()',
       'type' => 'App\Notifications\ThreadWasUpdated',
       'notifiable_id' => function(){
          return auth()->id()?:factory('App\User')->create()->id;
       },
       'notifiable_type' =>'App\User',
       'data' =>['foo'=>'bar']
    ];
});
