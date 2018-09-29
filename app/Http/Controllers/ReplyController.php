<?php

namespace App\Http\Controllers;

use App\Thread;
use App\Inspections\Spam;
use App\Reply;
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
    public function store($channelId,Thread $thread)
    {
        $this->validateReply();
        $reply=$thread->addReply([
            'body' => request('body'),
            'user_id' => auth()->id()
        ]);
        if(request()->expectsJson()){
           return $reply->load('owner');
        }
        return back()->with('flash','Your reply has been rendered');
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
        $this->validateReply();
        $reply->update(request(['body']));
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

    public function validateReply()
    {
        $this->validate(request(),['body'=>'required']);
        resolve(Spam::class)->detect(request('body'));
    }
}
