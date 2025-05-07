<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Hash;

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
        $inputs = $request->all();
    
        // Sanitize name fields if they exist
        if (isset($inputs["first_name"])) {
            $inputs["first_name"] = $this->SanitizedName($inputs["first_name"]);
        }
    
        if (isset($inputs["last_name"])) {
            $inputs["last_name"] = $this->SanitizedName($inputs["last_name"]);
        }
    
        if (isset($inputs["username"])) {
            $inputs["username"] = $this->SanitizedName($inputs["username"]);
        }
    
        // Validation
        $validator = validator()->make($inputs, [
            "profile_image" => "required|nullable|string|max:2048", // Validate as a string for URLs or local paths
            "first_name" => "required|min:4|max:255|string|regex:/^[A-Za-z\s]+$/i",
            "last_name" => "required|min:4|max:255|string|regex:/^[A-Za-z\s]+$/i",
            "username" => "required|unique:users,username|min:4|regex:/^[^\p{C}]+$/u|max:32",
            "email" => "required|unique:users|email|max:255",
            "contact_number" => "phone:PH|required|unique:profiles|min:10|max:15",
            "is_admin" => "required|boolean",
            "password" => "required|min:8|max:255",
        ]);
    
        if ($validator->fails()) {
            return $this->BadRequest($validator);
        }

        $validated = $validator->validated();
        $validated['password'] = Hash::make($validated['password']);

    
        // Handle the profile_image input
        $profileImage = $validated["profile_image"] ?? null;
        if ($request->hasFile('profile_image')) {
            $uploadedImage = $request->file('profile_image');
    
            $fileValidator = validator()->make(['file' => $uploadedImage], [
                'file' => 'image|mimes:jpeg,png,jpg,gif|max:2048'
            ]);
    
            if ($fileValidator->fails()) {
                return $this->BadRequest($fileValidator);
            }
    
            $fileName = time() . '_' . $uploadedImage->getClientOriginalName();
            $uploadedImage->move(public_path('images'), $fileName);
            $profileImage = '/images/' . $fileName;
        }
    
        // Save the user and their profile
        $user = User::create(array_merge(
            $validated,
            ["profile_image" => $profileImage]
        ));
    
        $user->profile()->create(array_merge(
            $validator->validated(),
            ["profile_image" => $profileImage]
        ));

        // Assign the user role based on is_admin
        // $roleName = $inputs['is_admin'] ? 'admin' : 'user';
        // $role = Role::firstOrCreate(['name' => $roleName, 'guard_name' => 'api']);
        // $user->assignRole($role);

        // $roleName = $inputs['is_admin'] ? 'admin' : 'user';

            // Create or get the role
        // $role = Role::firstOrCreate(['name' => $roleName, 'guard_name' => 'api']);

            // Create or get the permission
        // $permissionName = $roleName === 'admin' ? 'Manage All Works' : 'Manage Own Post';
        // $permission = Permission::firstOrCreate(['name' => $permissionName, 'guard_name' => 'api']);

            // Assign permission to role
        // $role->givePermissionTo($permission);

        $roleName = $inputs['is_admin'] ? 'admin' : 'user';
        $role = Role::firstOrCreate(['name' => $roleName, 'guard_name' => 'api']);
        $permissionName = $roleName === 'admin' ? 'Manage All Works' : 'Manage Own Post';
        $permission = Permission::firstOrCreate(['name' => $permissionName, 'guard_name' => 'api']);

        // this is only the connection between the role and the permission
        $role->givePermissionTo($permission); // Assign permission to role
        $user->assignRole($role); // Assign role to user
        $user->givePermissionTo($permission); // Assign permission to user

        // $roleUser = Role::firstOrCreate(["name" => "user", "guard_name" => "api"]);
        // $rolePermissionUser = Permission::firstOrCreate(["name" => "Manage Own Post", "guard_name" => "api"]);
        // $roleUser->givePermissionTo($rolePermissionUser);
        
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
        // Find the user
        $user = User::find($id);
    
        if (empty($user)) {
            return $this->NotFound("User Not Found!");
        }
    
        $inputs = $request->all();
    
        // Sanitize the name fields if they exist
        if (isset($inputs["first_name"])) {
            $inputs["first_name"] = $this->SanitizedName($inputs["first_name"]);
        }
    
        if (isset($inputs["last_name"])) {
            $inputs["last_name"] = $this->SanitizedName($inputs["last_name"]);
        }
    
        if (isset($inputs["username"])) {
            $inputs["username"] = $this->SanitizedName($inputs["username"]);
        }
    
        // Validation rules
        $validator = validator()->make($inputs, [
            "profile_image" => "sometimes|nullable|string|max:2048", // Accepts URLs or strings for paths
            "first_name" => "sometimes|min:4|max:255|string|regex:/^[A-Za-z\s]+$/i",
            "last_name" => "sometimes|min:4|max:255|string|regex:/^[A-Za-z\s]+$/i",
            "username" => "sometimes|unique:users,username,$id|min:4|regex:/^[A-Za-z0-9,.'\-\s]+$/|max:32",
            "email" => "sometimes|unique:users,email,$id|email|max:255",
            "contact_number" => "phone:PH|sometimes|unique:profiles|min:10|max:15",
            "password" => "sometimes|min:8|max:255",
            "is_admin" => "sometimes|boolean",
        ]);
    
        if ($validator->fails()) {
            return response()->json([
                "ok" => false,
                "errors" => $validator->errors(),
                "message" => "Validation Failed!"
            ], 400);
        }
    
        // Handle profile_image
        $profileImage = $inputs["profile_image"] ?? null;
        if ($request->hasFile('profile_image')) {
            $uploadedImage = $request->file('profile_image');
    
            // Validate the uploaded file
            $fileValidator = validator()->make(['file' => $uploadedImage], [
                'file' => 'image|mimes:jpeg,png,jpg,gif|max:2048'
            ]);
    
            if ($fileValidator->fails()) {
                return response()->json([
                    "ok" => false,
                    "errors" => $fileValidator->errors(),
                    "message" => "File validation failed!"
                ], 400);
            }
    
            // Move the file to public/images directory
            $fileName = time() . '_' . $uploadedImage->getClientOriginalName();
            $uploadedImage->move(public_path('images'), $fileName);
    
            // Set the profile_image to the local path
            $profileImage = '/images/' . $fileName;
        }
    
        // Update the user profile
        if (!$user->profile) {
            $user->profile()->create([
                "profile_image" => $profileImage,
            ]);
        } else {
            $user->profile->update(array_merge(
                $validator->validated(),
                ["profile_image" => $profileImage]
            ));
        }
    
        // Update the user record
        $user->update(array_merge(
            $validator->validated(),
            ["profile_image" => $profileImage]
        ));

        // Assign the user role
        $role = Role::where('name',$inputs['role'])->first()->name;

        $user = User::find($inputs['id']);

        $user->syncRoles($role);

        $user->update($inputs);
    
        return $this->Ok($user, "User {$user->name}'s information has been updated successfully!");
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


