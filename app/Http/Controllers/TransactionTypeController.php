<?php

namespace App\Http\Controllers;

use App\Models\TransactionType;
use Illuminate\Http\Request;


/**
 * @OA\Get(
 *     path="/api/transactions/type",
 *     summary="Get all transaction types",
 *     tags={"TransactionType"},
 *     @OA\Response(
 *         response=200,
 *         description="List of transaction types",
 *         @OA\JsonContent(
 *             @OA\Property(property="ok", type="boolean"),
 *             @OA\Property(property="message", type="string"),
 *             @OA\Property(
 *                 property="data",
 *                 type="array",
 *                 @OA\Items(ref="#/components/schemas/TransactionType")
 *             )
 *         )
 *     )
 * )
 *
 * @OA\Get(
 *     path="/api/transactions/type/{id}",
 *     summary="Get a specific transaction type by ID",
 *     tags={"TransactionType"},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Transaction type found",
 *         @OA\JsonContent(ref="#/components/schemas/TransactionType")
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Transaction Type not found"
 *     )
 * )
 *
 * @OA\Post(
 *     path="/api/transactions/type",
 *     summary="Reference an existing transaction type by name",
 *     tags={"TransactionType"},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"name"},
 *             @OA\Property(property="name", type="string", example="delivery")
 *         )
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="Transaction Type has been referenced successfully"
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Validation error"
 *     )
 * )
 *
 * @OA\Patch(
 *     path="/api/transactions/type/{id}",
 *     summary="Update a transaction type",
 *     tags={"TransactionType"},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"name"},
 *             @OA\Property(property="name", type="string", example="pickup")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Transaction Type has been updated successfully"
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Validation error"
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Transaction Type not found"
 *     )
 * )
 *
 * @OA\Delete(
 *     path="/api/transactions/type/{id}",
 *     summary="Delete a transaction type",
 *     tags={"TransactionType"},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Transaction Type has been deleted"
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Transaction Type not found"
 *     )
 * )
 *
 * @OA\Schema(
 *     schema="TransactionType",
 *     @OA\Property(property="id", type="integer"),
 *     @OA\Property(property="name", type="string"),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time")
 * )
 */
class TransactionTypeController extends Controller
{
    /**
     * Display a listing of all transaction types.
    */
    public function index() 
    {
        $types = TransactionType::all();
        
        return $this->Ok($types);
    } 

     /**
     * Display the specified transaction types.
    */
    public function show(string $id)
    {
        $types = TransactionType::find($id);
        
        if (empty($types)) {
            return $this->NotFound("Transaction Type not found");
        }

        $types->transactions;

        return $this->Ok($types);
    }


    /**
     * Store a newly created transaction types.
     */
    public function store(Request $request)
    {
        $request->validate([
            "name" => "required|string|exists:transaction_types,name"
        ]);
    
        // Retrieve the existing status instead of creating a new one
        $status = TransactionType::where('name', $request->name)->first();
    
        return $this->Created($status, "Transaction Type has been referenced successfully");
    }   

    /**
     * Update the specified transaction type.
     */
    public function update(Request $request, string $id)
    {
        $inputs = $request->all();
        $inputs["name"] = $this->SanitizedName($inputs["name"] ?? "");

       
        $type = TransactionType::find($id);

        // Validate the name exists in the transaction_types table
        $validator = validator()->make($inputs, [
            "name" => "required|string|exists:transaction_types,name"
        ]);

        if ($validator->fails()) {
            return $this->BadRequest($validator->errors());
        }

        // Update the transaction type
        $type->update($validator->validated());

        return $this->Ok($type, "Transaction Type has been updated successfully.");
    }

    

    public function destroy(string $id)
    {
        $types = TransactionType::find($id);

        if (empty($types)) {
            return $this->NotFound("Transaction Type not found");
        }

        $types->delete();

        return $this->Ok(null, "Transaction Type has been deleted");
    }
}
