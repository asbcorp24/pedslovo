<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\Section;
use App\Models\Material;
use App\Models\User;
class DashboardController extends Controller { public function index(){ return view('admin.dashboard',['sections'=>Section::count(),'materials'=>Material::count(),'users'=>User::count()]); } }
