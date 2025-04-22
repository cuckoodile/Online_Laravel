<?php


namespace App\Http\Controllers;

abstract class Controller
{
    // This is a base controller that will be extended by other controllers
    // This part is for the response methods for errors and success
    protected function BadRequest($validator, string $message = "Bad Request!") 
    {
        return response()->json([
            "ok" => false,
            "error" => $validator,
            "message" => $message,
        ], 400);
    }

    protected function NotFound(string $message = "Not Found!") 
    {
        return response()->json([
            "ok" => false,
            "message" => $message,
        ], 404);
    }

    protected function Forbidden(string $message = "Forbidden!") 
    {
        return response()->json([
            "ok" => false,
            "message" => $message,
        ], 403);
    }

    protected function Unauthorized(string $message = "Unauthorized!") 
    {
        return response()->json([
            "ok" => false,
            "message" => $message,
        ], 401);
    }
    
    protected function Ok($data, string $message = "Ok!", $others = []) 
    {
        return response()->json([
            "ok" => true,
            "data" => $data,
            "message" => $message,
            "others" => $others,
        ]);
    }

    protected function Created($data, string $message = "Created!", $others = []) 
    {
        return response()->json([
            "ok" => true,
            "data" => $data,
            "message" => $message,
            "others" => $others,
        ], 201);
    }

    protected function SanitizedName($name) {
       $name = trim($name);

       $name = ucwords($name);

        do {
       $name = str_replace(" ", " ",$name);
        }while (str_contains($name, "  "));

        return $name;
    }
    
// alex .

}
