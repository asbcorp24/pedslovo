<?php
namespace App\Providers;
use App\Models\SeoPage;use Illuminate\Support\Facades\Schema;use Illuminate\Support\Facades\View;use Illuminate\Support\ServiceProvider;
class AppServiceProvider extends ServiceProvider {
 public function register(): void {}
 public function boot(): void { View::composer('*',function($view){$seo=null;try{if(Schema::hasTable('seo_pages')){$path='/'.ltrim(request()->path(),'/');if($path==='//')$path='/';$seo=SeoPage::where('is_active',true)->where('path',$path)->first()?:SeoPage::where('is_active',true)->where('path','*')->first();}}catch(\Throwable $e){}$view->with('seo',$seo);}); }
}
