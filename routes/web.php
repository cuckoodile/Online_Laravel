<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::group(["prefix" => "banner"], function () {
    Route::get("/", [BannerImageController::class, "index"]);
    Route::post("/", [BannerImageController::class, "store"]);
    Route::get("/{id}", [BannerImageController::class, "show"]);
});
