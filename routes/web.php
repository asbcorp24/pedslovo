<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\SectionController;
use App\Http\Controllers\MaterialController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\SectionController as AdminSectionController;
use App\Http\Controllers\Admin\MaterialController as AdminMaterialController;
Route::get('/',[HomeController::class,'index'])->name('home');
Route::get('/section/{section:slug}',[SectionController::class,'show'])->name('section.show');
Route::get('/material/{material:slug}',[MaterialController::class,'show'])->name('material.show');
Route::middleware('guest')->group(function(){ Route::get('/login',[AuthController::class,'form'])->name('login'); Route::post('/login',[AuthController::class,'login'])->name('login.post'); });
Route::post('/logout',[AuthController::class,'logout'])->middleware('auth')->name('logout');
Route::prefix('admin')->name('admin.')->middleware(['auth','admin'])->group(function(){ Route::get('/',[DashboardController::class,'index'])->name('dashboard'); Route::resource('sections',AdminSectionController::class)->except('show'); Route::resource('materials',AdminMaterialController::class)->except('show'); });
