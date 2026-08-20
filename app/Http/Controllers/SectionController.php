<?php
namespace App\Http\Controllers;
use App\Models\Section;
class SectionController extends Controller { public function show(Section $section){ abort_unless($section->is_active,404); $section->load(['children'=>fn($q)=>$q->where('is_active',true),'materials'=>fn($q)=>$q->published()->latest('published_at')]); return view('section',compact('section')); } }
