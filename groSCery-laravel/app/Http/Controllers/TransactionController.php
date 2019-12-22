<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator;
use Illuminate\Support\Facades\Auth;
use App\User;
use App\Item;
use App\Transaction;
use App\Mail\PurchaseMade;
use Mail;

class TransactionController extends Controller
{
    public $successStatus = 200;

    /**
    * @OA\Post(
    *   path="/api/transactions/purchase",
    *   summary="Purchase an item and notify group",
    *   tags={"Transactions"},
    *   security={"bearer"},
    *   @OA\RequestBody(
    *       @OA\MediaType(
    *           mediaType="application/x-www-form-urlencoded",
    *           @OA\Schema(
    *               @OA\Property(
    *                   property="item_id",
    *                   type="int"
    *               ),
    *               @OA\Property(
    *                   property="price",
    *                   type="numeric"
    *               ),
    *             )
    *         )
    *   ),
    *   @OA\Response(
    *       response=200,
    *       description="Item purchased successfully",
    *       @OA\JsonContent(
    *           @OA\Property(
    *               property="success",
    *               type="string"
    *           ),
    *           example={"success":{"user_id":1,"group_id":1,"item_id":1,"price":"5.99","created_at":"2018-11-24 06:30:54","id":27}}
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
    public function purchase(Request $request) {
        $validator = Validator::make($request->all(), [ 
            'item_id' => 'required|int', 
            'price' => 'required|numeric',
        ]);
        if ($validator->fails()) { 
            return response()->json(['error'=>$validator->errors()], 401);            
        }
        $input = $request->all();
        $item = Item::find($input['item_id']);
        if (!$item) {
            return response()->json(['error'=>'Item not found'], 404);
        }
        if ($item->group_id != Auth::user()->group_id){
            return response()->json(['error'=>'Unauthroized'], 401);
        }
        
        $item->in_stock = true;
        $item->save();

        $tran = new Transaction;
        $tran->user_id = Auth::user()->id;
        $tran->group_id = Auth::user()->group_id;
        $tran->item_id = $item->id;
        $tran->price = $input['price'];
        $tran->save();

        $itemUsers = $item->users;
        foreach ($itemUsers as $user) {
            if ($user->id != Auth::user()->id) {
                Mail::to($user->email)->queue(new PurchaseMade($tran));
            }
        }
        
        return response()->json(['success' => $tran], $this->successStatus); 
    }
}
