<?php
namespace App\Http\Controllers;
use App\Models\Certificate;
use Illuminate\Http\Request;
class CertificateController extends Controller {
 public function index(Request $r){$certificates=Certificate::with('course')->where('user_id',$r->user()->id)->latest('issued_at')->get();return view('learning.certificates',compact('certificates'));}
 public function show(Request $r,Certificate $certificate){abort_unless($certificate->user_id===$r->user()->id||$r->user()->isAdmin(),403);$certificate->load(['course','user','template']);return view('learning.certificate',compact('certificate'));}
 public function verify(string $token){$certificate=Certificate::with(['course','user','template'])->where('verification_token',$token)->firstOrFail();return view('learning.certificate-verify',compact('certificate'));}
}
