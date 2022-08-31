<?php

namespace App\Http\Controllers;
use Validator;
use Illuminate\Http\Response;
use App\Models\User;
use App\Models\Podcast;
use App\Models\UserPodcast;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class PodcastController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            $podcast_data = Podcast::join('podcast_categories', 'podcast_categories.id', '=', 'podcasts.category_id')
                            ->select('podcasts.*', 'podcast_categories.title as category')
                            ->get();
            echo json_encode(['msg' => true, 'data' => $podcast_data]);
        } catch (\Exception $e) {            
            echo json_encode(['msg' => false, 'data'=>$e->getMessage()]);
        }
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
        try{
            $validator = Validator::make($request->all(), [
                'category_id' => 'required',
                'title' => 'required',
                'audio' =>'required|file|mimes:audio/mpeg,mpga,mp3,wav,aac'
                
            ]);

            if ($validator->fails()) {    
                return response()->json($validator->messages(), Response::HTTP_BAD_REQUEST);
            }
            $uniqueid = uniqid();
            $extension=$request->file('audio')->getClientOriginalExtension();
            $filename=Carbon::now()->format('Ymd').'_'.$uniqueid.'.'.$extension;
            $path = $request->file('audio')->storeAs('public/upload/audio',$filename);
            $podcast_data = new Podcast();
            $podcast_data->category_id = $request->category_id;
            $podcast_data->title = $request->title;
            $podcast_data->description = $request->description;
            $podcast_data->audio_file_path = $path;
            $podcast_data->save();
            if ($podcast_data->id) {
                echo json_encode(['msg' => true, 'data' => $podcast_data]);
            }
            else {
                echo json_encode(['msg' => false, 'data'=>'audio data not saved']);
            }
        } catch (\Exception $e) {            
            echo json_encode(['msg' => false, 'data'=>$e->getMessage()]);
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
        try {
            $podcast_data = Podcast::find($id);
            if ($podcast_data) {
                echo json_encode(['msg' => true, 'data' => $podcast_data]);
            }
            else {
                echo json_encode(['msg' => true, 'data' => 'No data found']);
            }
        } catch (\Exception $e) { 
            echo json_encode(['msg' => false, 'data'=>$e->getMessage()]);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
               
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
        try {
            $validator = Validator::make($request->all(), [                
                'audio' =>'file|mimes:audio/mpeg,mpga,mp3,wav,aac'                
            ]);

            if ($validator->fails()) {    
                return response()->json($validator->messages(), Response::HTTP_BAD_REQUEST);
            }
            $podcast_data = Podcast::find($id);
            if ($request->hasFile('audio')) {
                Storage::delete($podcast_data->audio_file_path); // delete old file
                $uniqueid = uniqid();
                $extension=$request->file('audio')->getClientOriginalExtension();
                $filename=Carbon::now()->format('Ymd').'_'.$uniqueid.'.'.$extension;
                $path = $request->file('audio')->storeAs('public/upload/audio',$filename);
                $podcast_data->audio_file_path = $path;
            }
            $podcast_data->category_id = $request->category_id;
            $podcast_data->title = $request->title;
            $podcast_data->description = $request->description;            
            $podcast_data->save();
            if ($podcast_data->id) {
                echo json_encode(['msg' => true, 'data' => $podcast_data]);
            }
            else {
                echo json_encode(['msg' => true, 'data' => 'No data found']);
            }
        } catch (\Exception $e) { 
            echo json_encode(['msg' => false, 'data'=>$e->getMessage()]);
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
        try {
            $podcast_data = Podcast::find($id);
            Storage::delete($podcast_data->audio_file_path);
            if ($podcast_data->delete()) {
                echo json_encode(['msg' => true, 'data' => 'Podcast deleted successfully']);
            }
            else {
                echo json_encode(['msg' => true, 'data' => 'No data found']);
            }
        } catch (\Exception $e) { 
            echo json_encode(['msg' => false, 'data'=>$e->getMessage()]);
        }
    }

    public function start_play($id)
    {
        try {
            $podcast_data = Podcast::find($id);
            if ($podcast_data) {
                $UserPodcast_data = UserPodcast::where('podcast_id',$id)
                ->where('user_id',Auth::user()->id)->first();
                if (!$UserPodcast_data) {
                    $UserPodcast_data = new UserPodcast();
                    $UserPodcast_data->user_id = Auth::user()->id;
                    $UserPodcast_data->podcast_id = $id;
                }
                $UserPodcast_data->status = 'Running';
                $UserPodcast_data->latest_play= date('Y-m-d H:i:s');
                $UserPodcast_data->save();                
                echo json_encode(['msg' => true, 'data' => $UserPodcast_data]);
            }
            else {
                echo json_encode(['msg' => true, 'data' => 'No data found']);
            }
        } catch (\Exception $e) { 
            echo json_encode(['msg' => false, 'data'=>$e->getMessage()]);
        }
    }

    public function stop_play($id)
    {
        try {
            $podcast_data = Podcast::find($id);
            if ($podcast_data) {
                $UserPodcast_data = UserPodcast::where('podcast_id',$id)
                ->where('user_id',Auth::user()->id)->first();
                $start_time = strtotime($UserPodcast_data->latest_play);
                $end_time = strtotime(date('Y-m-d H:i:s'));
                $duration = $end_time-$start_time;
                $UserPodcast_data->played_time = date('g:i:s',($duration));
                $UserPodcast_data->status = 'Stopped';
                $UserPodcast_data->save();                
                echo json_encode(['msg' => true, 'data' => $UserPodcast_data]);
            }
            else {
                echo json_encode(['msg' => true, 'data' => 'No data found']);
            }
        } catch (\Exception $e) { 
            echo json_encode(['msg' => false, 'data'=>$e->getMessage()]);
        }
    }

    public function get_status($id)
    {
        try {
            $podcast_data = Podcast::join('user_podcasts', 'user_podcasts.podcast_id', '=', 'podcasts.id')
            ->select('podcasts.*', 'user_podcasts.status')
            ->where('podcasts.id',$id)
            ->get();;
            if ($podcast_data) {
                echo json_encode(['msg' => true, 'data' => $podcast_data,'played_count' => count($podcast_data)]);
            }
            else {
                echo json_encode(['msg' => true, 'data' => 'No data found']);
            }
        } catch (\Exception $e) { 
            echo json_encode(['msg' => false, 'data'=>$e->getMessage()]);
        }
    }
}
