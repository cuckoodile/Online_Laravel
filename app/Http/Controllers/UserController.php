<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Hash;
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

        // Handle both file upload and base64 BEFORE validation
        // If the profile image is a file, process it as an uploaded file
        // If it's a base64 string, decode and save it as a file
        $profileImage = null;
        if ($request->hasFile('profile_image')) {
            $inputs['profile_image'] = $request->file('profile_image');
        } elseif (isset($inputs['profile_image']) && is_string($inputs['profile_image'])) {
            $image = $this->createBase64Image($inputs['profile_image']);
            if (!$image) {
                // Return an error if the base64 image is invalid
                return response()->json(['error' => 'Invalid base64 image'], 400);
            }
            $inputs['profile_image'] = $image; 
            $profileImage = $image; // Store for later use
        }

        // Validation rules
        // Ensure all required fields are present and valid
        // Avoid using overly strict rules that may block valid inputs
        // NOTE: The file upload should be a real file or else it will
        $validator = validator()->make($inputs, [
            "profile_image" => [
                'sometimes',
                function ($attribute, $value, $fail) {
                    // Allow: (1) Uploaded file, (2) Processed filename, or (3) null
                    if (
                        !$this->isValidFile($value) && 
                        !is_string($value) && 
                        !is_null($value)
                    ) {
                        $fail('Invalid profile image format.'); 
                    }
                },
            ],
            "first_name" => "required|min:4|max:255|string|regex:/^[A-Za-z\s]+$/i",
            "last_name" => "required|min:4|max:255|string|regex:/^[A-Za-z\s]+$/i",
            "username" => "required|unique:users,username|min:4|regex:/^[^\p{C}]+$/u|max:32",
            "email" => "required|unique:users|email|max:255",
            "contact_number" => "phone:PH|required|unique:profiles|min:10|max:15",
            "is_admin" => "required|boolean",
            "password" => "required|min:8|max:255",
        ]);

        if ($validator->fails()) {
            // Return validation errors if any
            return $this->BadRequest($validator);
        }

        $validated = $validator->validated();
        $validated['password'] = Hash::make($validated['password']); // Hash the password before saving

        // Handle file upload if not already processed (Base64 case handled earlier)
        if ($request->hasFile('profile_image') && !$profileImage) {
            $uploadedImage = $request->file('profile_image');
            $fileName = time() . '_' . $uploadedImage->getClientOriginalName();
            $uploadedImage->move(public_path('images'), $fileName);
            $profileImage = $fileName;
        }

        // Save the user and profile
        // Ensure the user and profile are created together
        $user = User::create(array_merge(
            $validated,
            ["profile_image" => $profileImage]
        ));
        $user->profile()->create(array_merge(
            $validated,
            ["profile_image" => $profileImage]
        ));

        // Role and permission handling
        // Assign roles and permissions based on the is_admin field
        $roleName = $inputs['is_admin'] ? 'admin' : 'user';
        $role = Role::firstOrCreate(['name' => $roleName, 'guard_name' => 'api']);
        $permissionName = $roleName === 'admin' ? 'Manage All Works' : 'Manage Own Post';
        $permission = Permission::firstOrCreate(['name' => $permissionName, 'guard_name' => 'api']);

        $role->givePermissionTo($permission); // Link permission to role
        $user->assignRole($role); // Assign role to user
        $user->givePermissionTo($permission); // Assign permission to user

        // Return success response
        return $this->Created($user, "User created successfully!");
    }
    
    // Helper Methods (add these to your controller)
    protected function createBase64Image($base64)
    {
        if (!preg_match('/^data:image\/(\w+);base64,/', $base64, $type)) {
            return false;
        }
    
        $image = substr($base64, strpos($base64, ',') + 1);
        $image = str_replace(' ', '+', $image);
        $decodedImage = base64_decode($image);
    
        if (!$decodedImage) {
            return false;
        }
    
        $extension = $type[1];
        $filename = 'img_' . time() . '_' . Str::random(8) . '.' . $extension;
        file_put_contents(public_path('images/' . $filename), $decodedImage);
    
        return $filename;
    }
    
    protected function isValidFile($value)
    {
        if ($value instanceof UploadedFile) {
            return $value->isValid();
        }
        return false;
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
        // Find user with profile
        $user = User::with('profile')->find($id);
        if (!$user) {
            return $this->NotFound("User not found!");
        }
    
        $inputs = $request->all();
    
        // Sanitize inputs
        $inputs = $this->sanitizeUserInputs($inputs);
    
        // Handle image uploads/Base64 before validation
        $profileImage = $user->profile_image;  // Preserve current image by default
        if ($request->hasFile('profile_image')) {
            $inputs['profile_image'] = $request->file('profile_image');
        } elseif (isset($inputs['profile_image']) && is_string($inputs['profile_image'])) {
            $image = $this->updateBase64Image($inputs['profile_image']);
            if (!$image) {
                return response()->json(['error' => 'Invalid base64 image'], 400);
            }
            $inputs['profile_image'] = $image;
        }
    
        // Validation rules
        $validator = validator()->make($inputs, [
            "profile_image" => [
                'sometimes',
                function ($attribute, $value, $fail) {
                    // Accepts: UploadedFile, processed filename string, or null
                    if (
                        !($value instanceof UploadedFile) &&
                        !is_string($value) &&
                        !is_null($value)
                    ) {
                        $fail('Invalid profile image format.');
                    }
                },
            ],
            "first_name" => "sometimes|min:4|max:255|string|regex:/^[A-Za-z\s]+$/i",
            "last_name" => "sometimes|min:4|max:255|string|regex:/^[A-Za-z\s]+$/i",
            "username" => "sometimes|unique:users,username,".$user->id."|min:4|regex:/^[^\p{C}]+$/u|max:32",
            "email" => "sometimes|unique:users,email,".$user->id."|email|max:255",
            "contact_number" => "sometimes|phone:PH|unique:profiles,contact_number,".$user->profile?->id."|min:10|max:15",
            "is_admin" => "sometimes|boolean",
            "password" => "sometimes|min:8|max:255",
        ]);
    
        if ($validator->fails()) {
            return $this->BadRequest($validator);
        }
    
        // Process image if not already done (for file uploads)
        if ($request->hasFile('profile_image') && is_uploaded_file($inputs['profile_image']->path())) {
            $fileName = 'user_' . time() . '_' . Str::random(8) . '.' . $inputs['profile_image']->extension();
            $inputs['profile_image']->move(public_path('images'), $fileName);
            
            // if New file saved successfully, it will delete the old file
            if ($user->profile_image && file_exists(public_path('images/' . $user->profile_image))) {
                @unlink(public_path('images/' . $user->profile_image));
            }
            
            $profileImage = $fileName;
        }
    
        // Prepare update data
        $updateData = $validator->validated();
        if (isset($updateData['password'])) {
            $updateData['password'] = Hash::make($updateData['password']);
        }
        $updateData['profile_image'] = $profileImage;
    
        // Update user
        $user->update($updateData);
    
        // Update profile
        if ($user->profile) {
            $user->profile->update([
                'profile_image' => $profileImage,
                'contact_number' => $inputs['contact_number'] ?? $user->profile->contact_number
            ]);
        } else {
            $user->profile()->create([
                'profile_image' => $profileImage,
                'contact_number' => $inputs['contact_number'] ?? null
            ]);
        }
    
        // Update role if changed
        if (isset($inputs['is_admin'])) {
            $roleName = $inputs['is_admin'] ? 'admin' : 'user';
            $user->syncRoles($roleName);
        }
    
        return $this->Ok($user, "User updated successfully");
    }
    
    // Helper Methods
    protected function sanitizeUserInputs(array $inputs): array
    {
        $sanitizable = ['first_name', 'last_name', 'username'];
        foreach ($sanitizable as $field) {
            if (isset($inputs[$field])) {
                $inputs[$field] = $this->SanitizedName($inputs[$field]);
            }
        }
        return $inputs;
    }
    
    protected function updateBase64Image($base64)
    {
        if (!preg_match('/^data:image\/(\w+);base64,/', $base64, $type)) {
            return false;
        }
    
        $image = substr($base64, strpos($base64, ',') + 1);
        $image = str_replace(' ', '+', $image);
        $decodedImage = base64_decode($image);
    
        if (!$decodedImage) {
            return false;
        }
    
        $extension = $type[1];
        $filename = 'user_' . time() . '_' . Str::random(8) . '.' . $extension;
        file_put_contents(public_path('images/' . $filename), $decodedImage);
    
        return $filename;
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


