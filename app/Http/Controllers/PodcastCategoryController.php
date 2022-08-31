<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator;
use Illuminate\Http\Response;
use App\Models\User;
use App\Models\PodcastCategory;


class PodcastCategoryController extends Controller
{
    public function index(){
        try {
            $podcast_data = PodcastCategory::all();
            echo json_encode(['msg' => true, 'data' => $podcast_data]);
        } catch (\Exception $e) {            
            echo json_encode(['msg' => false, 'data'=>$e->getMessage()]);
        }
    }

    public function save_category(Request $request){
        try {
            $validator = Validator::make($request->all(), [
                'title' => 'required',
            ]);

            if ($validator->fails()) {    
                return response()->json($validator->messages(), Response::HTTP_BAD_REQUEST);
            }

            $podcast_category= new PodcastCategory();
            $podcast_category->title = $request->title;
            $podcast_category->save();

            if ($podcast_category->id) {                
                echo json_encode(['msg' => true, 'data' => $podcast_category]);                              
            } 
            else {
                echo json_encode(['msg' => false, 'data' => 'podcast_category not saved']);
            }            
        } catch (\Exception $e) {            
            echo json_encode(['msg' => false, 'data'=>$e->getMessage()]);
        }
    }
}
