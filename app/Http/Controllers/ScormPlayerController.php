<?php
namespace App\Http\Controllers;
use App\Models\ScormAttempt;
use App\Models\ScormPackage;
use Illuminate\Http\Request;
class ScormPlayerController extends Controller {
 public function launch(ScormPackage $scorm){ abort_unless($scorm->is_active,404); $attempt=ScormAttempt::create(['scorm_package_id'=>$scorm->id,'user_id'=>auth()->id(),'status'=>'incomplete','started_at'=>now(),'cmi_data'=>[]]); return view('scorm.player',compact('scorm','attempt')); }
 public function state(ScormAttempt $attempt){ $this->authorizeAttempt($attempt); return response()->json(['ok'=>true,'data'=>$attempt->cmi_data?:[],'status'=>$attempt->status,'score_raw'=>$attempt->score_raw,'lesson_location'=>$attempt->lesson_location,'suspend_data'=>$attempt->suspend_data]); }
 public function commit(Request $request,ScormAttempt $attempt){
  $this->authorizeAttempt($attempt); $data=$request->validate(['data'=>'required|array']); $cmi=$attempt->cmi_data?:[];
  foreach($data['data'] as $key=>$value){ $cmi[(string)$key]=is_scalar($value)||is_null($value)?(string)$value:$value; }
  $status=$cmi['cmi.core.lesson_status']??$cmi['cmi.completion_status']??$attempt->status;
  $score=$cmi['cmi.core.score.raw']??$cmi['cmi.score.raw']??$attempt->score_raw;
  $attempt->update(['cmi_data'=>$cmi,'status'=>$status?:$attempt->status,'score_raw'=>is_numeric($score)?$score:$attempt->score_raw,'score_min'=>is_numeric($cmi['cmi.core.score.min']??null)?$cmi['cmi.core.score.min']:$attempt->score_min,'score_max'=>is_numeric($cmi['cmi.core.score.max']??null)?$cmi['cmi.core.score.max']:$attempt->score_max,'lesson_location'=>$cmi['cmi.core.lesson_location']??$cmi['cmi.location']??$attempt->lesson_location,'suspend_data'=>$cmi['cmi.suspend_data']??$attempt->suspend_data,'session_time'=>$cmi['cmi.core.session_time']??$cmi['cmi.session_time']??$attempt->session_time,'completed_at'=>in_array($status,['completed','passed','failed'])?now():$attempt->completed_at]);
  return response()->json(['ok'=>true]);
 }
 private function authorizeAttempt(ScormAttempt $attempt){ abort_unless(auth()->check() && ($attempt->user_id===auth()->id() || auth()->user()->isAdmin()),403); }
}
