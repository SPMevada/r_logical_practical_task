<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\FrontentController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/admin',[UserController::class,'index'])->name('user.index');
Route::get('/register',[UserController::class,'register'])->name('user.register');
Route::post('/store',[UserController::class,'store'])->name('user.store');
Route::post('/login',[UserController::class,'login'])->name('user.login');

Route::middleware('is_admin')->group(function() {
    Route::post('/logout',[UserController::class,'logout'])->name('user.logout');
    Route::get('/dashboard',[UserController::class,'dashboard'])->name('admin.dashboard');

    // Category route
    Route::post('/category/store',[CategoryController::class,'store'])->name('category.store');
    Route::get('/category/edit',[CategoryController::class,'edit'])->name('category.edit');
    Route::post('/category/update',[CategoryController::class,'update'])->name('category.update');
    Route::post('/category/status',[CategoryController::class,'status'])->name('category.status');
    Route::get('/category/index',[CategoryController::class,'index'])->name('category.index');
    Route::post('/category/delete',[CategoryController::class,'delete'])->name('category.delete');

    // Product route
    Route::get('/product/index',[ProductController::class,'index'])->name('product.index');
    Route::post('/product/store',[ProductController::class,'store'])->name('product.store');
    Route::get('/product/edit',[ProductController::class,'edit'])->name('product.edit');
    Route::post('/product/update',[ProductController::class,'update'])->name('product.update');
    Route::post('/product/delete',[ProductController::class,'delete'])->name('product.delete'); 
});

// Route::middleware('is_front_user')->group(function() {
    Route::get('/',[FrontentController::class,'frontent'])->name('frontent.index');
    Route::get('/frontentregister',[FrontentController::class,'register'])->name('frontent.register');
    Route::post('/frontenstore',[FrontentController::class,'store'])->name('frontent.store');
    Route::post('/frontenlogin',[FrontentController::class,'login'])->name('frontent.login');
    Route::get('/frontendashboard',[FrontentController::class,'dashboard'])->name('frontent.dashboard');
    Route::post('/frontendlogout',[FrontentController::class,'logout'])->name('frontent.logout');
    Route::post('/frontentaddtocart',[FrontentController::class,'addToCart'])->name('frontent.addtocart');
    Route::get('/frontentcartdashboard',[FrontentController::class,'cartItem'])->name('frontent.carditem');
// });


