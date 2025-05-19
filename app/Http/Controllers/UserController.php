<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Http\UploadedFile;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Retrieve all users and load their profiles
        $users = User::with(['profile', 'address', 'transactions'])->get();

        return $this->Ok($users);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $inputs = $request->all();
    
        // Sanitize name fields if they exist
        // Ensure names are properly formatted and free of unwanted characters
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
            "profile_image" => "sometimes|image|mimes:jpeg,png,jpg,gif,webp,jfif",
            "first_name" => "required|min:4|max:255|string|regex:/^[A-Za-z\s]+$/i",
            "last_name" => "required|min:4|max:255|string|regex:/^[A-Za-z\s]+$/i",
            "username" => "required|unique:users,username|min:4|regex:/^.+$/|max:32", // allow all characters
            "email" => "required|unique:users|email|max:255",
            "contact_number" => "required|unique:profiles|min:10|regex:/^[0-9]+$/|max:15", // no need for phone:PH - just create a rule to regex
            "is_admin" => "required|boolean", 
            "password" => "required|min:8|max:255",
        ]);

        if ($validator->fails()) {
            // return $this->BadRequest($validator);
            return response()->json([
                'ok' => false,
                'message' => 'Validation failed!',
                'errors' => $validator->errors()
            ], 422);
            
        }

        // Handle profile image upload
        $profileImage = null;
        if ($request->hasFile('profile_image')) {
            $file = $request->file('profile_image');
            $extension = $file->getClientOriginalExtension();
            $fileName = time() . '_' . uniqid() . '.' . $extension;
            $file->move(public_path('images'), $fileName);
            $profileImage = $fileName;
        }


        $validated = $validator->validated();
        $validated['password'] = Hash::make($validated['password']);

        // Save the user and profile
        $user = User::create($validated);

        // Save the profile (include profile_image here)
        $user->profile()->create(array_merge(
            $validated,
            ["profile_image" => $profileImage]
        ));

        // Role and permission handling
        $roleName = $inputs['is_admin'] ? 'admin' : 'user';
        $role = Role::firstOrCreate(['name' => $roleName, 'guard_name' => 'api']);
        $permissionName = $roleName === 'admin' ? 'Manage All Works' : 'Manage Own Post';
        $permission = Permission::firstOrCreate(['name' => $permissionName, 'guard_name' => 'api']);

        $role->givePermissionTo($permission);
        $user->assignRole($role);
        $user->givePermissionTo($permission);

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

        $user->profile;
        $user->address;
        $user->transactions;

        return $this->Ok($user);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);
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

        // Validation rules
        $validator = validator()->make($inputs, [
            "profile_image" => "sometimes|image|mimes:jpeg,png,jpg,gif,webp,jfif",
            "first_name" => "sometimes|min:4|max:255|string|regex:/^[A-Za-z\s]+$/i",
            "last_name" => "sometimes|min:4|max:255|string|regex:/^[A-Za-z\s]+$/i",
            "username" => "sometimes|min:4|regex:/^.+$/|max:32|unique:users,username," . $user->id,
            "email" => "sometimes|email|max:255|unique:users,email," . $user->id,
            "contact_number" => "sometimes|min:10|regex:/^[0-9]+$/|max:15|unique:profiles,contact_number," . $user->profile->id,
            "is_admin" => "sometimes|boolean",
            "password" => "nullable|min:8|max:255",
        ]);

        if ($validator->fails()) {
            return response()->json([
                'ok' => false,
                'message' => 'Validation failed!',
                'errors' => $validator->errors()
            ], 422);
        }

        // Handle profile image upload
        $profileImage = $user->profile->profile_image;
        if ($request->hasFile('profile_image')) {
            $file = $request->file('profile_image');
            $extension = $file->getClientOriginalExtension();
            $fileName = time() . '_' . uniqid() . '.' . $extension;
            $file->move(public_path('images'), $fileName);
            $profileImage = $fileName;
        }

        $validated = $validator->validated();

        // Handle password hashing
        if (isset($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        // Update user and profile separately
    // Separate user and profile fields
    $userFields = array_intersect_key($validated, array_flip([
        'username', 'email', 'password', 'is_admin'
    ]));

    $profileFields = array_intersect_key($validated, array_flip([
        'contact_number', 'profile_image', 'first_name', 'last_name'
    ]));

    // Perform updates
    $user->update($userFields);
    $user->profile()->update($profileFields);

        // Role and permission handling
        $roleName = isset($validated['is_admin']) && $validated['is_admin'] ? 'admin' : 'user';
        $role = Role::firstOrCreate(['name' => $roleName, 'guard_name' => 'api']);
        $permissionName = $roleName === 'admin' ? 'Manage All Works' : 'Manage Own Post';
        $permission = Permission::firstOrCreate(['name' => $permissionName, 'guard_name' => 'api']);

        $user->syncRoles([$role]);
        $user->syncPermissions([$permission]);

        return response()->json([
            'ok' => true,
            'message' => 'User updated successfully!',
            'data' => $user
        ]);
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


