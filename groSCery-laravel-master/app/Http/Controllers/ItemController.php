<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator;
use App\User; 
use Illuminate\Support\Facades\Auth; 
use App\Item;

class ItemController extends Controller
{
    public $successStatus = 200;
    /**
    * @OA\Post(
    *   path="/api/items/create",
    *   summary="Create a new item and subscribe to it",
    *   tags={"Items"},
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
    *       description="Item created and user subscribed",
    *       @OA\JsonContent(
    *           @OA\Property(
    *               property="success",
    *               type="string"
    *           ),
    *           example={"success": {"id":"1","group_id":"1","name":"Banana","in_stock":"1",}}
    *       )
    *   ),
    *   @OA\Response(
    *       response=409,
    *       description="Item created and user subscribed",
    *       @OA\JsonContent(
    *           @OA\Property(
    *               property="success",
    *               type="string"
    *           ),
    *           example={"success": "Item already exists"}
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
            'name' => 'required|string', 
        ]);
        if ($validator->fails()) { 
            return response()->json(['error'=>$validator->errors()], 401);            
        }
        $input = $request->all();
        $item = Item::where('name',$input['name'])->where('group_id', Auth::user()->group_id)->first();
        if ($item) {
            return response()->json(['error'=>'Item already exists'], 409);
        }
        $input['group_id'] = Auth::user()->group_id;
        $input['in_stock'] = true;
        $item = Item::create($input);
        $item->subscribeUser();
        $item->save();
        return response()->json(['success' => $item], $this->successStatus); 
    }

    /**
    * @OA\Get(
    *   path="/api/items/subscribers/{item_id}",
    *   summary="Gets the list of users subscribed to a Item",
    *   tags={"Items"},
    *   security={"bearer"},
    *   @OA\Response(
    *       response=200,
    *       description="Item subscribers returned",
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
    public function subscribers($item_id) {
        $item = Item::find($item_id);
        if (!$item) {
            return response()->json(['error'=>'Item not found'], 404);
        }
        if ($item->group_id != Auth::user()->group_id){
            return response()->json(['error'=>'Unauthroized'], 401);
        }
        return response()->json(['success'=>$item->users], 200);
    }

    /**
    * @OA\Post(
    *   path="/api/items/subscribe/{item_id}",
    *   summary="Subscribe a user to an Item",
    *   tags={"Items"},
    *   security={"bearer"},
    *   @OA\Response(
    *       response=200,
    *       description="User subscribed to Item",
    *       @OA\JsonContent(
    *           @OA\Property(
    *               property="success",
    *               type="string"
    *           ),
    *           example={"success": {"id":"1","group_id":"1","name":"Banana","in_stock":"1",}}
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
    public function subscribe($item_id) {
        $item = Item::find($item_id);
        if (!$item) {
            return response()->json(['error'=>'Item not found'], 404);
        }
        if ($item->group_id != Auth::user()->group_id){
            return response()->json(['error'=>'Unauthroized'], 401);
        }
        $item->subscribeUser();
        return response()->json(['success'=>'User subscribed to Item'], 200);
    }

    /**
    * @OA\Post(
    *   path="/api/items/unsubscribe/{item_id}",
    *   summary="Unsubscribe a user to an Item",
    *   tags={"Items"},
    *   security={"bearer"},
    *   @OA\Response(
    *       response=200,
    *       description="User unsubscribed to Item",
    *       @OA\JsonContent(
    *           @OA\Property(
    *               property="success",
    *               type="string"
    *           ),
    *           example={"success": {"id":"1","group_id":"1","name":"Banana","in_stock":"1",}}
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
    public function unsubscribe($item_id) {
        $item = Item::find($item_id);
        if (!$item) {
            return response()->json(['error'=>'Item not found'], 404);
        }
        if ($item->group_id != Auth::user()->group_id){
            return response()->json(['error'=>'Unauthroized'], 401);
        }
        $item->unsubscribeUser();
        return response()->json(['success'=>'User subscribed to Item'], 200);
    }

    /**
    * @OA\Post(
    *   path="/api/items/in-stock/{item_id}",
    *   summary="Mark a item as in stock",
    *   tags={"Items"},
    *   security={"bearer"},
    *   @OA\Response(
    *       response=200,
    *       description="Item indicated as in stock",
    *       @OA\JsonContent(
    *           @OA\Property(
    *               property="success",
    *               type="string"
    *           ),
    *           example={"success": "Item marked in stock"}
    *       )
    *   ),
    *   @OA\Response(
    *       response=404,
    *       description="Item not found",
    *       @OA\JsonContent(
    *           @OA\Property(
    *               property="success",
    *               type="string"
    *           ),
    *           example={"success": "Item not found"}
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
    public function inStock($item_id) {
        $item = Item::findOrFail($item_id);
        if (!$item) {
            return response()->json(['error'=>'Item not found'], 404);
        }
        if ($item->group_id != Auth::user()->group_id){
            return response()->json(['error'=>'Unauthroized'], 401);
        }
        $item->in_stock = true;
        $item->save();
        return response()->json(['success'=>'Item marked in stock'], 200);
    }

    /**
    * @OA\Post(
    *   path="/api/items/out-of-stock/{item_id}",
    *   summary="Mark a item as out of stock",
    *   tags={"Items"},
    *   security={"bearer"},
    *   @OA\Response(
    *       response=200,
    *       description="Item indicated as out of stock",
    *       @OA\JsonContent(
    *           @OA\Property(
    *               property="success",
    *               type="string"
    *           ),
    *           example={"success": "Item marked out of stock"}
    *       )
    *   ),
    *   @OA\Response(
    *       response=404,
    *       description="Item not found",
    *       @OA\JsonContent(
    *           @OA\Property(
    *               property="success",
    *               type="string"
    *           ),
    *           example={"success": "Item not found"}
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
    public function outOfStock($item_id) {
        $item = Item::find($item_id);
        if (!$item) {
            return response()->json(['error'=>'Item not found'], 404);
        }
        if ($item->group_id != Auth::user()->group_id){
            return response()->json(['error'=>'Unauthroized'], 401);
        }
        $item->in_stock = false;
        $item->save();
        return response()->json(['success'=>'Item marked out of stock'], 200);
    }
}
