<?php
namespace Database\Seeders;
use App\Models\Section;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
class DatabaseSeeder extends Seeder {
 public function run(){
  User::firstOrCreate(['email'=>'admin@pedslovo.local'],['name'=>'Администратор','password'=>Hash::make('ChangeMe123!'),'role'=>'admin']);
  $roots=['Юным дарованиям','Студентам и абитуриентам','Преподавателям и профессионалам','Взрослым любителям музыки'];$map=[];
  foreach($roots as $i=>$title){$map[$title]=Section::firstOrCreate(['slug'=>Str::slug($title)],['title'=>$title,'type'=>'audience','sort_order'=>$i+1]);}
  $student=$map['Студентам и абитуриентам'];$specialties=['Музыкальное искусство эстрады','Инструментальное исполнительство','Вокальное искусство','Сольное и хоровое народное пение','Хоровое дирижирование','Теория музыки'];
  foreach($specialties as $i=>$title){Section::firstOrCreate(['slug'=>Str::slug($title)],['parent_id'=>$student->id,'title'=>$title,'type'=>'specialty','sort_order'=>$i+1]);}
  $pro=$map['Преподавателям и профессионалам'];foreach(['Повышение квалификации','Профессиональная переподготовка','Стажировка'] as $i=>$title){Section::firstOrCreate(['slug'=>Str::slug($title)],['parent_id'=>$pro->id,'title'=>$title,'type'=>'program','sort_order'=>$i+1]);}
  $young=$map['Юным дарованиям'];foreach(['Детская школа искусств','Эстетические классы'] as $i=>$title){Section::firstOrCreate(['slug'=>Str::slug($title)],['parent_id'=>$young->id,'title'=>$title,'type'=>'program','sort_order'=>$i+1]);}
 }
}
