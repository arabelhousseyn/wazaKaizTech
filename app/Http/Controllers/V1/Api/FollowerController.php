<?php

namespace App\Http\Controllers\V1\Api;

use App\Models\follower;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Auth;
use App\Models\notification;
use App\Traits\SendNotification;
class FollowerController extends Controller
{
    use SendNotification;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
    public function store(Request $request)
    {
        // add friend 

        $validator = Validator::make($request->all(), [
            'user_id' => 'required'
        ]);

        if($validator->fails())
        {
            response()->json(['success' => false,200]);
        }

        if($validator->validated())
        {
            $checkUser = User::where('id',$request->user_id)->first();
        if($checkUser)
        {
            if(Auth::user()->id == $request->user_id)
            {
                return response()->json(['success' => false], 200);
            }

            $check = follower::where([['user_id','=',Auth::user()->id],['follow_id','=',$request->user_id]])->first();
            if($check)
            {
                $delete = follower::where('id',$check->id)->delete();
                notification::where([['user_id','=',Auth::user()->id],['morphable_id','=',$request->user_id]])->delete();
                if($delete)
                {
                    return response()->json(['success' => true,'following' => 0], 200);
                }
                return response()->json(['success' => false], 200);
            }else{
                $following = follower::create([
                    'user_id' => Auth::user()->id,
                    'follow_id' => $request->user_id,
                    'is_friend' => 0
                ]);

                if($following)
                {
                    $notification = notification::create([
                        'user_id' => Auth::user()->id,
                        'morphable_id' => $request->user_id,
                        'type' => 4,
                        'is_read' => 0
                    ]);

                    return response()->json(['success' => true,'following' => 1,'notification_id' => $notification->id], 200);
                }
            }
          return response()->json(['success' => false], 200);
        }
        return response()->json(['success' => false], 200);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\follower  $follower
     * @return \Illuminate\Http\Response
     */
    public function show(follower $follower)
    {

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\follower  $follower
     * @return \Illuminate\Http\Response
     */
    public function edit(follower $follower)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\follower  $follower
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, follower $follower)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\follower  $follower
     * @return \Illuminate\Http\Response
     */
    public function destroy(follower $follower)
    {
        //
    }

    public function AcceptFriend($user_id = null)
    {
        // accept friend
        $check = follower::where([['user_id','=',$user_id],['follow_id','=',Auth::user()->id]])->first();
        if($check)
        {
           $follow =  follower::where([['user_id','=',$user_id],['follow_id','=',Auth::user()->id]])->update([
                'is_friend' => 1
            ]);
            $notification = notification::where([['user_id','=',$user_id],['morphable_id','=',Auth::user()->id]])->first();
            return response()->json(['success' => true,'notification_id' => $notification->id], 200);
        }

        return response()->json(['success' => false], 200);
    }
}
