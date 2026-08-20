<?php
use Illuminate\Support\Facades\Route;
use App\Models\Section;
use App\Models\Material;
Route::get('/sections', fn()=>Section::where('is_active',true)->orderBy('sort_order')->get());
Route::get('/materials', fn()=>Material::published()->latest('published_at')->paginate(20));
