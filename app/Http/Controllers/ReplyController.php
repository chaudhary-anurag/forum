<?php

namespace App\Http\Controllers;

use App\Thread;
use App\User;
use App\Inspections\Spam;
use App\Reply;
use App\Notifications\YouWereMentioned;
use App\Http\Requests\CreatePostRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\Request;

class ReplyController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth',['except'=>'index']);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($channelId,Thread $thread)
    {
       return $thread->replies()->paginate(20);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store($channelId,Thread $thread, CreatePostRequest $form)
    {
        $reply=$thread->addReply([
            'body'=>request('body'),
            'user_id'=>auth()->id()
        ]);
        preg_match_all('/\@([^\s\.]+)/',$reply->body,$matches);
        $names=$matches[1];

        foreach($names as $name)
        {
            $user=User::whereName($name)->first();
            if($user){
                $user->notify(new YouWereMentioned($reply));
            }
        }
        return $reply->load('owner');

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Reply  $reply
     * @return \Illuminate\Http\Response
     */
    public function show(Reply $reply)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Reply  $reply
     * @return \Illuminate\Http\Response
     */
    public function edit(Reply $reply)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Reply  $reply
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Reply $reply)
    {
        $this->authorize('update',$reply);
        try {
              $this->validate(request(),['body'=>'required|spamfree']);
              $reply->update(request(['body']));
          }
        catch (\Exception $e){
         return response('Sorry, your reply could not be saved at this time', 422);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Reply  $reply
     * @return \Illuminate\Http\Response
     */
    public function destroy(Reply $reply)
    {
       $this->authorize('update',$reply);
       $reply->delete();
      if(request()->expectsJson()){
        return response(['status'=>'Reply deleted']);
       } 
       return back();
    }
}
