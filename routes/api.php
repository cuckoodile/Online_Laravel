<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProductTransactionController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\TransactionPaymentMethodCrontroller;
use App\Http\Controllers\TransactionStatusController;
use App\Http\Controllers\TransactionTypeController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get("users", [UserController::class, "index"]);
Route::post("users", [UserController::class, "store"]);
Route::get("users/{id}", [UserController::class, "show"]);
Route::delete("users/{id}", [UserController::class, "destroy"]);
Route::patch("users/{id}", [UserController::class, "update"]);

Route::group(["prefix" => "categories"], function () {
    Route::get("/", [CategoryController::class, "index"]);
    Route::post("/", [CategoryController::class, "store"]);
    Route::get("/{id}", [CategoryController::class, "show"]);
    Route::delete("/{id}", [CategoryController::class, "destroy"]);
    Route::patch("/{id}", [CategoryController::class, "update"]);
});

Route::group(["prefix" => "products"], function () {
    Route::get("/", [ProductController::class, "index"]);
    Route::post("/", [ProductController::class, "store"]);
    Route::get("/{id}", [ProductController::class, "show"]);
    Route::delete("/{id}", [ProductController::class, "destroy"]);
    Route::patch("/{id}", [ProductController::class, "update"]);
});

Route::group(["prefix" => "transactions/type"], function () {
    Route::get("/", [TransactionTypeController::class, "index"]);
    Route::post("/", [TransactionTypeController::class, "store"]);
    Route::get("/{id}", [TransactionTypeController::class, "show"]);
    Route::delete("/{id}", [TransactionTypeController::class, "destroy"]);
    Route::patch("/{id}", [TransactionTypeController::class, "update"]);
});

Route::group(["prefix" => "transactions/status"], function () {
    Route::get("/", [TransactionStatusController::class, "index"]);
    Route::post("/", [TransactionStatusController::class, "store"]);
    Route::get("/{id}", [TransactionStatusController::class, "show"]);
    Route::delete("/{id}", [TransactionStatusController::class, "destroy"]);
    Route::patch("/{id}", [TransactionStatusController::class, "update"]);
});

Route::group(["prefix" => "transactions/payment"], function () {
    Route::get("/", [TransactionPaymentMethodCrontroller::class, "index"]);
    Route::post("/", [TransactionPaymentMethodCrontroller::class, "store"]);
    Route::get("/{id}", [TransactionPaymentMethodCrontroller::class, "show"]);
    Route::delete("/{id}", [TransactionPaymentMethodCrontroller::class, "destroy"]);
    Route::patch("/{id}", [TransactionPaymentMethodCrontroller::class, "update"]);
});

Route::group(["prefix" => "transactions"], function () {
    Route::get("/", [TransactionController::class, "index"]);
    Route::post("/", [TransactionController::class, "store"])->middleware("auth:sanctum");
    Route::get("/{id}", [TransactionController::class, "show"]);
    Route::delete("/{id}", [TransactionController::class, "destroy"]);
    Route::patch("/{id}", [TransactionController::class, "update"]);
});

Route::post("login", [AuthController::class, "login"]);
Route::post("logout", [AuthController::class, "logout"])->middleware("auth:sanctum");
Route::get("user", [AuthController::class, "checkToken"])->middleware("auth:sanctum");


// HALOOO