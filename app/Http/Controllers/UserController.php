<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserRole;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Validator;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class UserController extends Controller
{
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
        try {
            $validator = Validator::make($request->all(), [
                'full_name' => 'required',
                'email'=>'required|email|unique:users',
                'pswd' => 'required',
            ]);

            if ($validator->fails()) {    
                return response()->json($validator->messages(), Response::HTTP_BAD_REQUEST);
            }

            if (filter_var($request->email, FILTER_VALIDATE_EMAIL) !== false) {                
                $user_data = User::where('email', '=', $request->email)->first();  
                if (empty($user_data)) {
                    $user_data =  new User();
                    $user_data->user_full_name = $request->full_name;
                    $user_data->password = Hash::make($request->pswd);                
                    $user_data->email = $request->email;
                    $user_data->save();
                    if ($user_data->id) {
                        $user_role_data = new UserRole();
                        $user_role_data->user_id = $user_data->id;
                        $user_role_data->role_id = 2;
                        $user_role_data->save();
                        echo json_encode(['msg' => true, 'data' => $user_data]);
                        // Auth::login($user_data);
                    }
                    else{
                        echo json_encode(['msg' => false, 'data' => 'User Not Register']);
                    }
                } else {
                    echo json_encode(['msg' => false, 'data' => 'Already Exist']);
                }              
            } 
            else {
                echo json_encode(['msg' => false, 'data' => 'Invalid Email']);
            }            
        } catch (\Exception $e) {
            
            echo json_encode(['msg' => false, 'data'=>$e->getMessage()]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function edit(User $user)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        //
    }

    public function admin_reg(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'full_name' => 'required',
                'email'=>'required|email|unique:users',
                'pswd' => 'required',
            ]);

            if ($validator->fails()) {    
                return response()->json($validator->messages(), Response::HTTP_BAD_REQUEST);
            }

            if (filter_var($request->email, FILTER_VALIDATE_EMAIL) !== false) {                
                $user_data = User::where('email', '=', $request->email)->first();  
                if (empty($user_data)) {
                    $user_data =  new User();
                    $user_data->user_full_name = $request->full_name;
                    $user_data->password = Hash::make($request->pswd);                
                    $user_data->email = $request->email;
                    $user_data->save();
                    if ($user_data->id) {
                        $user_role_data = new UserRole();
                        $user_role_data->user_id = $user_data->id;
                        $user_role_data->role_id = 1;
                        $user_role_data->save();
                        echo json_encode(['msg' => true, 'data' => $user_data]);
                        // Auth::login($user_data);
                    }
                    else{
                        echo json_encode(['msg' => false, 'data' => 'Admin User Not Register']);
                    }
                } else {
                    echo json_encode(['msg' => false, 'data' => 'Already Exist']);
                }              
            } 
            else {
                echo json_encode(['msg' => false, 'data' => 'Invalid Email']);
            }            
        } catch (\Exception $e) {
            
            echo json_encode(['msg' => false, 'data'=>$e->getMessage()]);
        }
    }

    public function login(Request $request){
        try {
            $validator = Validator::make($request->all(), [
                'email' => 'required',
                'password' => 'required',
            ]);

            if ($validator->fails()) {
                return response()->json($validator->messages(), Response::HTTP_BAD_REQUEST);
            }

            if (filter_var($request->email, FILTER_VALIDATE_EMAIL) !== false) {
                $credentials['email'] = $request->email;
                $credentials['password'] = $request->password;
                if (Auth::attempt($credentials)) {
                    $user= Auth::user();
                    $token = $user->createToken('token')->plainTextToken;
                    echo json_encode(['msg' => true, 'data' => $credentials, 'token'=>$token]);
                } else {
                    echo json_encode(['msg' => false,'data'=>'Invalid Credentails']);
                }
            } 
            else {
                echo json_encode(['msg' => false, 'data'=>'Invalid EMail']);
            }            
        } catch (\Exception $e) {
            
            echo json_encode(['msg' => false, 'data'=>$e->getMessage()]);
        }
    }

    public function logout(){        
        try {
            $user= Auth::user();
            $user->tokens()->delete();
            echo json_encode(['msg' => true, 'data'=>'logout successfully']);
        } catch (\Exception $e) {            
            echo json_encode(['msg' => false, 'data'=>$e->getMessage()]);
        }
    }
}
