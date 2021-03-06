<?php

namespace App\Http\Controllers\V1\Api;

use Illuminate\Http\Request;
use App\Models\{
    innovation,
    innovationDomain,
    innovationImage,
    innovationLike,
    User,
    notification
};
use Carbon\Carbon;
use ImageOptimizer;
use Auth;
use Illuminate\Support\Facades\Validator;
use App\Traits\upload;
class innovationController extends Controller
{
    use upload;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = innovation::with('likesList')
        ->whereDate('created_at', '>=', Carbon::now()->subDays(7)->setTime(0, 0, 0)->toDateTimeString())->paginate(7);
        return response()->json($data, 200);
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
        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'description' => 'required',
            'innovation_domain_id' => 'required',
            'type' => 'required'
        ]);

        if($validator->fails())
        {
            return response()->json(['success' => false], 200);
        }

        if($validator->validated())
        {
            $checkInnovationDomain = innovationDomain::find($request->innovation_domain_id);

            if(!$checkInnovationDomain)
            {
                return response()->json(['success' => false], 200);
            }

            $check = false;
            $innovation = null;
            $pathAudio = null;
            $pathImageCompany = '';
                 if(strlen($request->audio) != 0)
                 {
                    $pathAudio = $request->audio;
                 }

                 if(strlen($request->imageCompany) != 0)
                 {
                    $pathImageCompany = $this->ImageUpload($request->imageCompany,'ImageCompany');
                 }

                    $innovation = innovation::create([
                        'title' => $request->title,
                        'description' => $request->description,
                        'audio' => $pathAudio,
                        'is_financed' => 0,
                        'financementAmount' => 0,
                        'pathBusinessPlan' => '',
                        'user_id' => Auth::user()->id,
                        'likes' => 0,
                        'type' => $request->type,
                        'imageCompany' =>(strlen($pathImageCompany) != 0) ? env('DISPLAY_PATH') .'ImageCompany/'.$pathImageCompany : '',
                        'innovation_domain_id' => $request->innovation_domain_id,
                        'status' => 0
                    ]);

                   $images = explode(';ibaa;',$request->images);
                   foreach ($images as $image) {
                    $pathImage = $this->ImageUpload($image,'innovationImages');
                           $check = innovationImage::create([
                               'path' => env('DISPLAY_PATH') .'innovationImages/'. $pathImage,
                               'innovation_id' => $innovation->id,
                           ]);
                   }
                   if($check)
                   {
                       return response()->json(['success' => true,'id' => $innovation->id], 200);
                   }
                   return response()->json(['success' => false], 200);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
         $data = innovation::with('user','images','likesList')->find($id);
         if($data)
         {
            $user = User::where('id',$data->user_id)->first();
            $likeList = array();
            $dislikeList = array();
            $temp = $data->likesList;
            if(strlen($data->imageCompany) != 0)
            {
                $data->user->picture = $data->imageCompany;
                $data['is_company'] = 1;
            }else{
                $data->user->picture = $data->user->picture;
                $data['is_company'] = 0;
            }
            $data['is_kaiztech_team'] = $user->is_kaiztech_team;
            foreach ($temp as $value) {
                if($value->type == -1)
           {
               array_push($dislikeList,$value->user_id);
           }

           if($value->type == 1)
           {
            array_push($likeList,$value->user_id);
           }
            }
            $data['dislikeList'] = $dislikeList;
            $data['likeList'] = $likeList;

            return response()->json(['success' => true,'data' => $data], 200);
         }
         return response()->json(['success' => false], 200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'description' => 'required',
        ]);

        if($validator->fails())
        {
            return response()->json(['success' => false], 200);
        }

        if($validator->validated())
        {
            $GroupPost = innovation::where('id',$id)->update([
                'description' => $request->description
            ]);
        if($GroupPost)
        {
            return response()->json(['success' => true], 200);
        }
        return response()->json(['success' => false], 200);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $GroupPost = innovation::where('id',$id)->delete();
        if($GroupPost)
        {
            return response()->json(['success' => true], 200);
        }
        return response()->json(['success' => false], 200);
    }

    public function getInnovationByDomain($id)
    {
        if($id == 0)
        {
            $data = innovation::with('likesList')->selective($id)->paginate(20);
        foreach ($data as $value) {
            $value['createdAt'] = Carbon::parse($value->created_at)->locale('fr_FR')->subMinutes(2)->diffForHumans();
            $tempImages = array();
            $likeList = array();
            $dislikeList = array();
            $temp = $value->likesList;
            foreach ($temp as $vl) {
                if($vl->type == -1)
           {
               array_push($dislikeList,$vl->user_id);
           }

           if($vl->type == 1)
           {
            array_push($likeList,$vl->user_id);
           }
            }
            $value['dislikes'] = count($dislikeList);
            $value['likes'] = count($likeList);

            $userInnovation = innovation::with('images')->where('id',$value->id)->first();
            $user = User::where('id',$value->user_id)->first();
            if(count($userInnovation->images) > 5)
            {
                for ($i=0; $i <5 ; $i++) {
                    array_push($tempImages,$userInnovation->images[$i]); 
                }
                $value['images'] = $tempImages;
            }else{
                $value['images'] = $userInnovation->images;
            }
            $value['countImages'] = count($userInnovation->images);
            $value['user'] = $user->fullName;
            if(strlen($value->imageCompany) != 0)
            {
                $value['pictureUser'] = $value->imageCompany;
            }else{
                $value['pictureUser'] = $user->picture;
            }
            $value['is_kaiztech_team'] = $user->is_kaiztech_team;
        }
    
        return response()->json($data, 200);
        }

        $data = innovation::with('likesList')->selective($id)->paginate(20);

        foreach ($data as $value) {
            $value['createdAt'] = Carbon::parse($value->created_at)->locale('fr_FR')->subMinutes(2)->diffForHumans();
            $tempImages = array();
            $likeCount = 0;
            $dislikeCount = 0;
            $temp = $value->likesList;
            foreach ($temp as $vl) {
                if($vl->type == -1)
           {
              $dislikeCount++; 
           }

           if($vl->type == 1)
           {
              $likeCount++; 
           }
            }
            $value['dislikes'] = $dislikeCount;
            $value['likes'] = $likeCount;
            $userInnovation = innovation::with('images')->where('id',$value->id)->first();
            $user = User::where('id',$value->user_id)->first();
            if(count($userInnovation->images) > 5)
            {
                for ($i=0; $i <5 ; $i++) {
                    array_push($tempImages,$userInnovation->images[$i]); 
                }
                $value['images'] = $tempImages;
            }else{
                $value['images'] = $userInnovation->images;
            }
            $value['countImages'] = count($userInnovation->images);
            $value['user'] = $user->fullName;
            if(strlen($value->imageCompany) != 0)
            {
                $value['pictureUser'] = $value->imageCompany;
            }else{
                $value['pictureUser'] = $user->picture;
            }
            $value['is_kaiztech_team'] = $user->is_kaiztech_team;
        }

        return response()->json($data, 200);
    }



    public function funding(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'pathBusinessPlan' => 'required',
            'id' => 'required',
            'financementAmount' => 'required',
        ]);
        if($validator->fails())
        {
            return response()->json(['success' => false], 200);
        }

        if($validator->validated())
        {
            $pathPdf = '';
        if(strlen($request->pathBusinessPlan) != 0)
                 {
                   // $pdf = gzdecode(base64_decode($request->pathBusinessPlan));
                   $pathPdf = $this->ImageUpload($request->pathBusinessPlan,'bussinesPlan');
                 }
                $update = innovation::where('id',$request->id)->update([
                     'is_financed' => 1,
                     'financementAmount' => $request->financementAmount,
                     'pathBusinessPlan' => env('DISPLAY_PATH') .'bussinesPlan/'. $pathPdf
                 ]);

                 if(!$update)
                 {
                     return response()->json(['success' => false], 200);
                 }
                 
                 return response()->json(['success' => true], 200);
        }
    }

    public function handleActionInnovation(Request $request)
    {
        $check = innovationLike::where([['user_id','=',Auth::user()->id],['innovation_id','=',$request->innovation_id]])->first();

        if($check)
        {
            if($request->type == $check->type)
            {
                innovationLike::where([['user_id','=',Auth::user()->id],['innovation_id','=',$request->innovation_id]])->delete();  
            }else{
                $check->update([
                    'type' => $request->type
                ]);    
            }
        $data = $this->likeListByInnovation($request->innovation_id);

        return response()->json($data, 200);
        }

        $like = innovationLike::create([
            'user_id' => Auth::user()->id,
            'innovation_id' => $request->innovation_id,
            'type' => $request->type
        ]);

        // $notification = notification::create([
        //     'user_id' => Auth::user()->id,
        //     'morphable_id' => $request->innovation_id,
        //     'type' => 0,
        //     'is_read' => 0,
        //     //'affiliate' => 1,
        // ]);

        $group_post = innovation::findOrFail($request->innovation_id);
        $likes = $group_post->likes + 1;
        $group_post->update([
            'likes' => $likes
        ]); 
        $data = $this->likeListByInnovation($request->innovation_id);

        return response()->json($data, 200);
    }

    public function likeListByInnovation($id)
    {
        $data = innovation::with('user','likesList')->find($id);
        $final = array();
        $temp = $data->likesList;
        $likeList = array();
        $dislikeList = array();
        foreach ($temp as $value) {
           if($value->type == -1)
           {
               array_push($dislikeList,$value->user_id);
           }

           if($value->type == 1)
           {
            array_push($likeList,$value->user_id);
           }
        }

        $final['dislikeList'] = $dislikeList;
        $final['likeList'] = $likeList;

         return response()->json($final, 200);
    }
  
}
