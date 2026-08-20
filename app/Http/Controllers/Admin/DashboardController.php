<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;use App\Models\Course;use App\Models\Group;use App\Models\Material;use App\Models\Section;use App\Models\SeoPage;use App\Models\User;
class DashboardController extends Controller { public function index(){return view('admin.dashboard',['sections'=>Section::count(),'materials'=>Material::count(),'courses'=>Course::count(),'groups'=>Group::count(),'users'=>User::count(),'seoPages'=>SeoPage::count()]);} }
