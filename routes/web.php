<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\SectionController;
use App\Http\Controllers\MaterialController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\LearningController;
use App\Http\Controllers\CertificateController;
use App\Http\Controllers\CabinetController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ScormPlayerController;
use App\Http\Controllers\HelpController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\SectionController as AdminSectionController;
use App\Http\Controllers\Admin\MaterialController as AdminMaterialController;
use App\Http\Controllers\Admin\ScormController as AdminScormController;
use App\Http\Controllers\Admin\CourseController as AdminCourseController;
use App\Http\Controllers\Admin\LessonController as AdminLessonController;
use App\Http\Controllers\Admin\LessonRequirementController;
use App\Http\Controllers\Admin\JournalController as AdminJournalController;
use App\Http\Controllers\Admin\GroupController as AdminGroupController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Admin\SeoController as AdminSeoController;
use App\Http\Controllers\Admin\SortController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\MediaController;
use App\Http\Controllers\Admin\ScormResultsController;

Route::get('/locale/{locale}',function($locale){
    abort_unless(in_array($locale,['ru','cv','mhr','tt'],true),404);
    session(['locale'=>$locale]);
    return back();
})->name('locale.switch');

Route::get('/',[HomeController::class,'index'])->name('home');
Route::get('/section/{section:slug}',[SectionController::class,'show'])->name('section.show');
Route::get('/material/{material:slug}',[MaterialController::class,'show'])->name('material.show');
Route::get('/course/{course:slug}',[CourseController::class,'show'])->name('courses.show');

Route::middleware('guest')->group(function(){
    Route::get('/login',[AuthController::class,'form'])->name('login');
    Route::post('/login',[AuthController::class,'login'])->name('login.post');
});
Route::post('/logout',[AuthController::class,'logout'])->middleware('auth')->name('logout');

Route::middleware('auth')->group(function(){
    Route::get('/cabinet',[CabinetController::class,'index'])->name('cabinet');
    Route::get('/help',[HelpController::class,'index'])->name('help');
    Route::get('/teacher/journal/{course}',[CabinetController::class,'teacherJournal'])->middleware('role:teacher')->name('teacher.journal');
    Route::post('/course/{course:slug}/enroll',[CourseController::class,'enroll'])->name('courses.enroll');
    Route::get('/my-courses',[LearningController::class,'myCourses'])->name('learning.my-courses');
    Route::get('/lesson/{lesson}',[LearningController::class,'lesson'])->name('learning.lesson');
    Route::post('/lesson/{lesson}/complete',[LearningController::class,'complete'])->name('learning.lesson.complete');
    Route::post('/lesson/{lesson}/resource/{type}/{resourceId}/complete',[LearningController::class,'completeResource'])->name('learning.resource.complete');
    Route::get('/certificates',[CertificateController::class,'index'])->name('certificates.index');
    Route::get('/certificates/{certificate}',[CertificateController::class,'show'])->name('certificates.show');
    Route::get('/scorm/{scorm}/launch',[ScormPlayerController::class,'launch'])->name('scorm.launch');
    Route::get('/scorm/attempt/{attempt}/state',[ScormPlayerController::class,'state'])->name('scorm.state');
    Route::post('/scorm/attempt/{attempt}/commit',[ScormPlayerController::class,'commit'])->name('scorm.commit');
});

Route::prefix('admin')->name('admin.')->middleware(['auth','admin'])->group(function(){
    Route::get('/',[DashboardController::class,'index'])->name('dashboard');
    Route::resource('sections',AdminSectionController::class)->except('show');
    Route::resource('materials',AdminMaterialController::class)->except('show');
    Route::resource('courses',AdminCourseController::class)->except('show');
    Route::resource('courses.lessons',AdminLessonController::class)->except('show');
    Route::delete('/courses/{course}/lessons/{lesson}/files/{file}',[AdminLessonController::class,'destroyFile'])->name('courses.lessons.files.destroy');
    Route::get('/courses/{course}/lessons/{lesson}/requirements',[LessonRequirementController::class,'edit'])->name('courses.lessons.requirements.edit');
    Route::put('/courses/{course}/lessons/{lesson}/requirements',[LessonRequirementController::class,'update'])->name('courses.lessons.requirements.update');
    Route::get('/scorm',[AdminScormController::class,'index'])->name('scorm.index');
    Route::post('/scorm',[AdminScormController::class,'store'])->name('scorm.store');
    Route::delete('/scorm/{scorm}',[AdminScormController::class,'destroy'])->name('scorm.destroy');
    Route::resource('seo',AdminSeoController::class)->except('show');
    Route::get('/media',[MediaController::class,'index'])->name('media.index');
    Route::post('/media',[MediaController::class,'store'])->name('media.store');
    Route::delete('/media/{media}',[MediaController::class,'destroy'])->name('media.destroy');
    Route::post('/sort/{type}',[SortController::class,'update'])->name('sort.update');

    Route::middleware('role:admin')->group(function(){
        Route::resource('groups',AdminGroupController::class)->except('show');
        Route::post('/groups/{group}/assign-courses',[AdminGroupController::class,'assignCourses'])->name('groups.assign-courses');
        Route::get('/users/credentials',[AdminUserController::class,'credentials'])->name('users.credentials');
        Route::resource('users',AdminUserController::class)->except('show');
        Route::post('/users/import',[AdminUserController::class,'import'])->name('users.import');
        Route::post('/users/bulk-group',[AdminUserController::class,'bulkGroup'])->name('users.bulk-group');
        Route::get('/journal',[AdminJournalController::class,'index'])->name('journal.index');
        Route::get('/journal/{course}',[AdminJournalController::class,'show'])->name('journal.show');
        Route::get('/scorm-results',[ScormResultsController::class,'index'])->name('scorm-results.index');
        Route::get('/scorm-results/{user}',[ScormResultsController::class,'show'])->name('scorm-results.show');
        Route::get('/settings',[SettingsController::class,'edit'])->name('settings.edit');
        Route::put('/settings',[SettingsController::class,'update'])->name('settings.update');
    });
});

Route::get('/sitemap.xml',fn()=>app(\App\Http\Controllers\SeoController::class)->sitemap())->name('sitemap');
Route::get('/robots.txt',fn()=>app(\App\Http\Controllers\SeoController::class)->robots())->name('robots');
