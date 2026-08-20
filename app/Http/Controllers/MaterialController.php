<?php
namespace App\Http\Controllers;
use App\Models\Material;
class MaterialController extends Controller { public function show(Material $material){ abort_unless($material->status==='published',404); $material->load('sections'); return view('material',compact('material')); } }
