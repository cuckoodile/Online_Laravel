<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
{
    // Retrieve all users and load their profiles
    $users = User::with('profile')->get();

    return $this->Ok($users);
}


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //for create
        
        $inputs = $request->all();

         // Sanitize only first_name, last_name, and username if they exist in the request
        if (isset($inputs["first_name"])) {
            $inputs["first_name"] = $this->SanitizedName($inputs["first_name"] ?? "");
        }

        if (isset($inputs["last_name"])) {
            $inputs["last_name"] = $this->SanitizedName($inputs["last_name"] ?? "");
        }

        if (isset($inputs["username"])) {
            $inputs["username"] = $this->SanitizedName($inputs["username"] ?? "");
        }

        //validator
        $validator = validator()->make( $inputs , [
            "first_name" => "required|min:4|max:255|string|regex:/^[A-Za-z\s]+$/i",
            "last_name" => "required|min:4|max:255|string|regex:/^[A-Za-z\s]+$/i",
            "username" => "required|unique:users|min:4|regex:/^[^\p{C}]+$/u|max:32",
            "email" => "required|unique:users|email|max:255",
            "contact_number" => "phone:PH|required|unique:profiles|min:10|max:15",
            "password" => "required|min:8|max:255",
            "region" => "required|string",
            "province" => "required|string",
            "district" => "required|string",
            "city_or_municipality" => "required|string",
            "barangay" => "required|string",
            "subdivision_or_village" => "required|string",
            "street_number" => "required|string",
            "street_name" => "required|string",
            "unit_number" => "required|string|regex:/^[0-9,.'\-\s]+$/",
            "zip_code" => "required|string|min:4|regex:/^[0-9]+$/",
            
        ], [
            "phone" => "The :attribute must be a valid phone number",
        ]);

        if($validator->fails()) {
            return $this->BadRequest($validator);
        }
        
        $user = User::create($validator->validated());

        $user->profile()->create($validator->validated());
        $user->profile;

        $user->address()->create($validator->validated());
        $user->address;

        return $this->Created($user, "User created successfully!");

    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $user = User::find($id);

        if(empty($user)){
            return $this->NotFound("User Not Found!");
        }

        return $this->Ok($user);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $user = User::find($id);

        if(empty($user)){
            return $this->NotFound("User Not Found!");
        }

        $inputs = $request->all();

         // Sanitize only first_name, last_name, and username if they exist in the request
        if (isset($inputs["first_name"])) {
            $inputs["first_name"] = $this->SanitizedName($inputs["first_name"] ?? "");
        }

        if (isset($inputs["last_name"])) {
            $inputs["last_name"] = $this->SanitizedName($inputs["last_name"] ?? "");
        }

        if (isset($inputs["username"])) {
            $inputs["username"] = $this->SanitizedName($inputs["username"] ?? "");
        }

        //validator
        $validator = validator()->make( $inputs , [
            "first_name" => "sometimes|min:4|max:255|string|regex:/^[A-Za-z\s]+$/i",
            "last_name" => "sometimes|min:4|max:255|string|regex:/^[A-Za-z\s]+$/i",
            "username" => "sometimes|unique:users,username,$id|min:4|regex:/^[A-Za-z0-9,.'\-\s]+$/|max:32",
            "email" => "sometimes|unique:users,email,$id|email|max:255",
            "contact_number" => "phone:PH|sometimes|unique:profiles|min:10|max:15",
            "password" => "sometimes|min:8|max:255",
            "region" => "sometimes|string",
            "province" => "sometimes|string",
            "district" => "sometimes|string",
            "city_or_municipality"=> "sometimes|string",
            "barangay" => "sometimes|string",
            "sudivision_or_village" => "sometimes|string",
            "street_number" => "sometimes|string",
            "street_name" => "sometimes|string",
            "unit_number" => "sometimes|string",
            "zip_code" => "sometimes|string",

        ]);
        
        if ($validator->fails()) {
            return $this->BadRequest($validator);
        }

        $user->update($validator->validated());
        $user->profile->update($validator->validated());
        $user->address->update($validator->validated());

        if(empty($user)){
            return $this->NotFound("User Not Found!");
        }
        return $this->Ok($user, "User $user->name information has been updated successfully!"); 
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $user = User::find($id);
        // $user = Profile::find($id);

        if(empty($user)){
            return $this->NotFound("User Not Found!");
        }

        //Deleting the associated profile also then proceeding to users
        if ($user->profile) {
            $user->profile->delete();
        }
        if ($user->address) {
            $user->address->delete();
        }
        $user->delete();
        return $this->Ok($user, "This user has been deleted");
    }
}
