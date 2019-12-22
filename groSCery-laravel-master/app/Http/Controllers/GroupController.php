<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; 
use Validator;

use App\Group;

class GroupController extends Controller
{
    public $successStatus = 200;
    /**
    * @OA\Get(
    *   path="/api/groups/list",
    *   summary="List all the groups and their IDs",
    *   tags={"Groups"},
    *   security={"bearer"},
    *   @OA\Response(
    *       response=200,
    *       description="Groups have been retrieved",
    *       @OA\JsonContent(
    *           @OA\Property(
    *               property="success",
    *               type="object",
    *               @OA\Property(
    *                   property="id",
    *                   type="int"
    *               ),
    *               @OA\Property(
    *                   property="name",
    *                   type="string"
    *               )
    *           ),
    *           example={"success":{{"id":1,"name":"201"}}}
    *       )
    *   ),
    *   @OA\Response(
    *       response=401,
    *       ref="#/components/responses/401",
    *   )
    * )
    */ 
    public function list() 
    {
        return response()->json(['success' => Group::all()], $this->successStatus);
    }

    /**
    * @OA\Get(
    *   path="/api/groups/items",
    *   summary="Get a group's subscibed items",
    *   tags={"Groups"},
    *   security={"bearer"},
    *   @OA\Response(
    *       response=200,
    *       description="Group items have been retrieved",
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
        return response()->json(['success' => Auth::user()->group->items], $this->successStatus);
    }

    /**
    * @OA\Post(
    *   path="/api/groups/subscribe/{group_id}",
    *   summary="Subscribe a user to a group",
    *   tags={"Groups"},
    *   security={"bearer"},
    *   @OA\Response(
    *       response=200,
    *       description="User has been subscribed to group",
    *       @OA\JsonContent(
    *           @OA\Property(
    *               property="success",
    *               type="string"
    *           ),
    *           example={"success": "User has been subscribed to group 201"}
    *       )
    *   ),
    *   @OA\Response(
    *       response=400,
    *       ref="#/components/responses/400",
    *   ),
    *   @OA\Response(
    *       response=401,
    *       ref="#/components/responses/401",
    *   )
    * )
    */ 
    public function subscribe($group_id) 
    {
        $group = Group::find($group_id);
        if (!$group) {
            return response()->json(['error'=>'Group does not exist'], 400);
        }
        $group->subscribeUser();
        return response()->json(['success' => 'User has been subscribed to ' . Auth::user()->group->name], $this->successStatus);
    }

    /**
    * @OA\Post(
    *   path="/api/groups/unsubscribe",
    *   summary="Unsubscribe a user from a group",
    *   tags={"Groups"},
    *   security={"bearer"},
    *   @OA\Response(
    *       response=200,
    *       description="User has been unsubscribed from their group",
    *       @OA\JsonContent(
    *           @OA\Property(
    *               property="success",
    *               type="string"
    *           ),
    *           example={"success": "User has been unsubscribed to group 201"}
    *       )
    *   ),
    *   @OA\Response(
    *       response=400,
    *       ref="#/components/responses/400",
    *   ),
    *   @OA\Response(
    *       response=401,
    *       ref="#/components/responses/401",
    *   )
    * )
    */ 
    public function unsubscribe() 
    {
        $group = Auth::user()->group;
        if (!$group) {
            return response()->json(['error'=>'User has no Group'], 400);
        }
        Auth::user()->group = null;
        return response()->json(['success' => 'User has been subscribed to ' . $group->name], $this->successStatus);
    }

    /**
    * @OA\Get(
    *   path="/api/groups/subscribers",
    *   summary="Gets the list of users subscribed to users Group",
    *   tags={"Groups"},
    *   security={"bearer"},
    *   @OA\Response(
    *       response=200,
    *       description="Group subscribers returned",
    *       @OA\JsonContent(
    *           @OA\Property(
    *               property="success",
    *               type="string"
    *           ),
    *           example={"success": {{"id":2,"group_id":null,"name":"Kristof","pic_url":null,"email":"kristof@usc.edu","created_at":"2018-10-30 01:13:02"}}}
    *       )
    *   ),
    *   @OA\Response(
    *       response=400,
    *       ref="#/components/responses/400",
    *   ),
    *   @OA\Response(
    *       response=401,
    *       ref="#/components/responses/401",
    *   )
    * )
    */ 
    public function subscribers() {
        if (!Auth::user()->group) {
            return response()->json(['error'=>'Not in group'], 400);
        }
        return response()->json(['success'=>Auth::user()->group->users], 200);
    }

    /**
    * @OA\Post(
    *   path="/api/groups/create",
    *   summary="Create a new group and subscribe to it",
    *   tags={"Groups"},
    *   security={"bearer"},
    *   @OA\RequestBody(
    *       @OA\MediaType(
    *           mediaType="application/x-www-form-urlencoded",
    *           @OA\Schema(
    *               @OA\Property(
    *                   property="name",
    *                   type="string"
    *               )
    *             )
    *         )
    *   ),
    *   @OA\Response(
    *       response=200,
    *       description="Group created and user subscribed",
    *       @OA\JsonContent(
    *           @OA\Property(
    *               property="success",
    *               type="string"
    *           ),
    *           example={"success": { "id":"1","name":"201"}}
    *       )
    *   ),
    *   @OA\Response(
    *       response=400,
    *       ref="#/components/responses/400",
    *   ),
    *   @OA\Response(
    *       response=401,
    *       ref="#/components/responses/401",
    *   )
    * )
    */ 
    public function create(Request $request) 
    {
        $validator = Validator::make($request->all(), [ 
            'name' => 'required|string'
        ]);
        if ($validator->fails()) { 
            return response()->json(['error'=>$validator->errors()], 400);            
        }
        $input = $request->all();
        if (Group::where('name',$input['name'])->first()) {
            return response()->json(['error'=>'Group already exists.'], 409);
        }
        $group = Group::create($input);
        $group->subscribeUser();
        return response()->json(['success' => $group], $this->successStatus);
    }

}
