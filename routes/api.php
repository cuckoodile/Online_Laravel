<?php
// TESTTT -Alex
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProductCommentController;
use App\Http\Controllers\ProductSpecificationController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\TransactionPaymentMethodController;
use App\Http\Controllers\TransactionStatusController;
use App\Http\Controllers\TransactionTypeController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CartController;
use Illuminate\Support\Facades\Route;

Route::post("login", [AuthController::class, "login"]);
Route::post("register", [UserController::class, "store"]);
Route::post("logout", [AuthController::class, "logout"])->middleware("auth:sanctum");
Route::get("user", [AuthController::class, "checkToken"])->middleware("auth:sanctum");

Route::group(["prefix" => "users", "middleware" => "auth:sanctum"], function () {
    Route::get("/", [UserController::class, "index"]);
    Route::get("/{id}", [UserController::class, "show"]);
    Route::delete("/{id}", [UserController::class, "destroy"]);
    Route::patch("/{id}", [UserController::class, "update"]);
});


Route::group(["prefix" => "categories"], function () {
    Route::get("/", [CategoryController::class, "index"]);
    Route::get("/{id}", [CategoryController::class, "show"]);
    Route::post("/", [CategoryController::class, "store"])->middleware("auth:sanctum");
    Route::delete("/{id}", [CategoryController::class, "destroy"])->middleware("auth:sanctum");
    Route::patch("/{id}", [CategoryController::class, "update"])->middleware("auth:sanctum");
});

Route::group(["prefix" => "products"], function () {
    Route::get("/", [ProductController::class, "index"]);
    Route::get("/{id}", [ProductController::class, "show"]);
    Route::post("/", [ProductController::class, "store"])->middleware("auth:sanctum");
    Route::delete("/{id}", [ProductController::class, "destroy"])->middleware("auth:sanctum");
    Route::patch("/{id}", [ProductController::class, "update"])->middleware("auth:sanctum");
});


Route::group(["prefix" => "comments"], function () {
    Route::get("/", [ProductCommentController::class, "index"]);
    Route::get("/{id}", [ProductCommentController::class, "show"]);
    Route::post("/", [ProductCommentController::class, "store"])->middleware("auth:sanctum");
    Route::delete("/{id}", [ProductCommentController::class, "destroy"])->middleware("auth:sanctum");
    Route::patch("/{id}", [ProductCommentController::class, "update"])->middleware("auth:sanctum");
});
Route::get("/reviewsCount", [ProductCommentController::class, "reviewsCount"]);
Route::get("/aveRate", [ProductCommentController::class, "aveRate"]);
Route::get("/rateCount", [ProductCommentController::class, "rateCount"]);

Route::group(["prefix" => "specifications"], function () {
    Route::get("/", [ProductSpecificationController::class, "index"]);
    Route::get("/{id}", [ProductSpecificationController::class, "show"]);
    Route::post("/", [ProductSpecificationController::class, "store"])->middleware("auth:sanctum");
    Route::delete("/{id}", [ProductSpecificationController::class, "destroy"])->middleware("auth:sanctum");
    Route::patch("/{id}", [ProductSpecificationController::class, "update"])->middleware("auth:sanctum");
});

Route::group(["prefix" => "transactions/type"], function () {
    Route::get("/", [TransactionTypeController::class, "index"]);
    Route::get("/{id}", [TransactionTypeController::class, "show"]);
    Route::post("/", [TransactionTypeController::class, "store"])->middleware("auth:sanctum");
    Route::delete("/{id}", [TransactionTypeController::class, "destroy"])->middleware("auth:sanctum");
    Route::patch("/{id}", [TransactionTypeController::class, "update"])->middleware("auth:sanctum");
});

Route::group(["prefix" => "transactions/status"], function () {
    Route::get("/", [TransactionStatusController::class, "index"]);
    Route::get("/{id}", [TransactionStatusController::class, "show"]);
    Route::post("/", [TransactionStatusController::class, "store"])->middleware("auth:sanctum");
    Route::delete("/{id}", [TransactionStatusController::class, "destroy"])->middleware("auth:sanctum");
    Route::patch("/{id}", [TransactionStatusController::class, "update"])->middleware("auth:sanctum");
});

Route::group(["prefix" => "transactions/payment"], function () {
    Route::get("/", [TransactionPaymentMethodController::class, "index"]);
    Route::get("/{id}", [TransactionPaymentMethodController::class, "show"]);
    Route::post("/", [TransactionPaymentMethodController::class, "store"])->middleware("auth:sanctum");
    Route::delete("/{id}", [TransactionPaymentMethodController::class, "destroy"])->middleware("auth:sanctum");
    Route::patch("/{id}", [TransactionPaymentMethodController::class, "update"])->middleware("auth:sanctum");
});

Route::group(["prefix" => "transactions", "middleware" => "auth:sanctum"], function () {
    Route::get("/", [TransactionController::class, "index"]);
    Route::get("/{id}", [TransactionController::class, "show"]);
    Route::post("/", [TransactionController::class, "store"]);
    Route::delete("/{id}", [TransactionController::class, "destroy"]);
    Route::patch("/{id}", [TransactionController::class, "update"]);
});

Route::group(["prefix" => "carts", "middleware" => "auth:sanctum"], function () {
    Route::get("/", [CartController::class, "index"]);      
    Route::get("/{id}", [CartController::class, "show"]);    
    Route::post("/", [CartController::class, "store"]);      
    Route::patch("/{id}", [CartController::class, "update"]); 
    Route::delete("/{id}", [CartController::class, "destroy"]); 
});