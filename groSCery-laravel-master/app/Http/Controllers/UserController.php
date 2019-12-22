<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request; 
use App\User; 
use Illuminate\Support\Facades\Auth; 
use Validator;

/**
 * @OA\Info(title="201 Project API", version="1.0")
 * 
 * @OA\SecurityScheme(
 *      type="http",
 *      securityScheme="bearer",
 *      bearerFormat="JWT"
 * ),
 * 
 * @OA\Response(
 *  response=401,
 *  description="Request is not authorized",
 *  @OA\JsonContent(
 *      @OA\Property(
 *          property="error",
 *          type="string",
 *      ),
 *      example={"error": "Unauthorized"}
 *  )
 * ),
 * @OA\Response(
 *  response=400,
 *  description="Bad request",
 *  @OA\JsonContent(
 *      @OA\Property(
 *          property="error",
 *          type="string",
 *      ),
 *      example={"error": "Bad request"}
 *  )
 * )
 * 
 */
class UserController extends Controller 
{
    public $successStatus = 200;
    
    /**
    * @OA\Post(
    *   path="/api/login",
    *   summary="Login a user",
    *   tags={"Login, Logout, and Registration"},
    *   @OA\RequestBody(
    *       @OA\MediaType(
    *           mediaType="application/x-www-form-urlencoded",
    *           @OA\Schema(
    *               @OA\Property(
    *                   property="email",
    *                   type="email"
    *               ),
    *               @OA\Property(
    *                   property="password",
    *                   type="password"
    *               )
    *             )
    *         )
    *   ),
    *   @OA\Response(
    *       response=200,
    *       description="Login successful",
    *       @OA\JsonContent(
    *           @OA\Property(
    *               property="success",
    *               type="object",
    *               @OA\Property(
    *                   property="token",
    *                   type="string"
    *               ),
    *           ),
    *           example={"success": {"token" : "1KJGK234..." }}
    *       )
    *   ),
    *   @OA\Response(
    *       response=401,
    *       ref="#/components/responses/401",
    *   )
    * )
    */ 
    public function login(){ 
        if(Auth::attempt(['email' => request('email'), 'password' => request('password')])){ 
            $user = Auth::user(); 
            $success['token'] =  $user->createToken('MyApp')->accessToken; 
            return response()->json(['success' => $success], $this->successStatus); 
        } 
        else{ 
            return response()->json(['error'=>'Unauthorized'], 401); 
        } 
    }

    
    /**
    * @OA\Post(
    *   path="/api/logout",
    *   summary="Logout a user",
    *   tags={"Login, Logout, and Registration"},
    *   security={"bearer"},
    *   @OA\Response(
    *       response=200,
    *       description="Logout successful",
    *       @OA\JsonContent(
    *           @OA\Property(
    *               property="success",
    *               type="string",
    *           ),
    *           example={"success": "Token has been revoked"}
    *       )
    *   ),
    *   @OA\Response(
    *       response=401,
    *       ref="#/components/responses/401",
    *   )
    * )
    */ 
    public function logout(){
        if (Auth::check()) {
            Auth::user()->token()->revoke();
            return response()->json(['success' => 'Token has been revoked'], $this->successStatus);
        } else { 
            return response()->json(['error'=>'Unauthroized'], 401); 
        } 
    }

    /**
    * @OA\Post(
    *   path="/api/register",
    *   summary="Register a new user",
    *   tags={"Login, Logout, and Registration"},
    *   @OA\RequestBody(
    *       @OA\MediaType(
    *           mediaType="application/x-www-form-urlencoded",
    *           @OA\Schema(
    *               @OA\Property(
    *                   property="name",
    *                   type="string"
    *               ),
    *               @OA\Property(
    *                   property="email",
    *                   type="email"
    *               ),
    *               @OA\Property(
    *                   property="password",
    *                   type="password"
    *               ),
    *               @OA\Property(
    *                   property="c_password",
    *                   type="password"
    *               )
    *             )
    *         )
    *   ),
    *   @OA\Response(
    *       response=200,
    *       description="Registration successful",
    *       @OA\JsonContent(
    *           @OA\Property(
    *               property="success",
    *               type="object",
    *               @OA\Property(
    *                   property="token",
    *                   type="string"
    *               ),
    *           ),
    *           example={"success": {"token" : "1KJGK234..." }}
    *       )
    *   ),
    *   @OA\Response(
    *       response=400,
    *       ref="#/components/responses/400",
    *   ),
    *   @OA\Response(
    *       response=409,
    *       description="User could not be created due to a conflict. User already exists.",
    *       @OA\JsonContent(
    *           @OA\Property(
    *               property="error",
    *               type="string",
    *           ),
    *           example={"error": "User already exists"}
    *       )
    *   )
    * )
    */  
    public function register(Request $request) 
    { 
        $validator = Validator::make($request->all(), [ 
            'name' => 'required', 
            'email' => 'required|email', 
            'password' => 'required', 
            'c_password' => 'required|same:password', 
        ]);
        if ($validator->fails()) { 
            return response()->json(['error'=>$validator->errors()], 400);            
        }
        $input = $request->all();
        if (User::where('email',$input['email'])->first()) {
            return response()->json(['error'=>'User already exists'], 409);
        }
        $input['password'] = bcrypt($input['password']); 
        $user = User::create($input);
        $success['token'] =  $user->createToken('MyApp')->accessToken; 
        $success['name'] =  $user->name;
        return response()->json(['success'=>$success], $this->successStatus); 
    }
    
    /**
    * @OA\Get(
    *   path="/api/users/details",
    *   summary="Get a users basic information",
    *   tags={"Users"},
    *   security={"bearer"},
    *   @OA\Response(
    *       response=200,
    *       description="User details have been retrieved",
    *       @OA\JsonContent(
    *           @OA\Property(
    *               property="success",
    *               type="object",
    *               @OA\Property(
    *                   property="id",
    *                   type="int"
    *               ),
    *               @OA\Property(
    *                   property="group_id",
    *                   type="int"
    *               ),
    *               @OA\Property(
    *                   property="name",
    *                   type="string"
    *               ),
    *               @OA\Property(
    *                   property="pic_url",
    *                   type="string"
    *               ),
    *               @OA\Property(
    *                   property="email",
    *                   type="string"
    *               ),
    *               @OA\Property(
    *                   property="created_at",
    *                   type="string"
    *               ),
    *           ),
    *           example={"success":{"id":2,"group_id":null,"name":"Kristof","pic_url":null,"email":"kristof@usc.edu","created_at":"2018-10-30 01:13:02"}}
    *       )
    *   ),
    *   @OA\Response(
    *       response=401,
    *       ref="#/components/responses/401",
    *   )
    * )
    */ 
    public function details() 
    { 
        $user = Auth::user();
        if ($user) {
            return response()->json(['success' => $user], $this->successStatus);
        } else {
            return response()->json(['error'=>'Unauthorised'], 401);
        }
    }

    /**
    * @OA\Get(
    *   path="/api/users/items",
    *   summary="Get a user's subscibed items",
    *   tags={"Users"},
    *   security={"bearer"},
    *   @OA\Response(
    *       response=200,
    *       description="User items have been retrieved",
    *       @OA\JsonContent(
    *           @OA\Property(
    *               property="success",
    *               type="object",
    *               @OA\Property(
    *                   property="id",
    *                   type="int"
    *               ),
    *               @OA\Property(
    *                   property="group_id",
    *                   type="int"
    *               ),
    *               @OA\Property(
    *                   property="name",
    *                   type="string"
    *               ),
    *               @OA\Property(
    *                   property="pic_url",
    *                   type="string"
    *               )
    *           ),
    *           example={"success":{{"id":1,"group_id":1,"name":"Banana","in_stock":1,"pic_url":null}}}
    *       )
    *   ),
    *   @OA\Response(
    *       response=401,
    *       ref="#/components/responses/401",
    *   )
    * )
    */ 
    public function items() 
    {
        return response()->json(['success' => Auth::user()->items()->get()], $this->successStatus);
    }



}
