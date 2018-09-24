<?php

namespace app;
use App\Favourite;
use Illuminate\Database\Eloquent\Model;

trait Favouritable
{   
	protected static function bootFavouritable()
	{
		static::deleting(function($model){
           $model->favourites->each->delete();
		});
	}
	public function favourites()
    {
      return $this->morphMany(Favourite::Class,'favourited');
    }

    public function favourite()
    {
    	if(!$this->favourites()->where(['user_id'=>auth()->id()])->exists())
    	{
    		return $this->favourites()->create(['user_id'=>auth()->id()]);
    	}
    }

    public function unfavourite()
    {
        $attributes=['user_id'=>auth()->id()];
        $this->favourites()->where($attributes)->get()->each(function($favourite){
           $favourite->delete();
        });
    }

    public function isFavourited()
    {
    	return !! $this->favourites->where('user_id',auth()->id())->count();
    }

    public function getIsFavouritedAttribute()
    {
      return $this->isFavourited();
    }

    public function getFavouritesCountAttribute()
    {
        return $this->favourites->count();
    }
}