<?php

namespace Database\Seeders;

use App\Models\Section;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class PortalStructureSeeder extends Seeder
{
    public function run(): void
    {
        $roots = [
            'Юным дарованиям',
            'Студентам и абитуриентам',
            'Преподавателям и профессионалам',
            'Взрослым любителям музыки',
        ];

        $map = [];
        foreach ($roots as $i => $title) {
            $map[$title] = Section::updateOrCreate(
                ['slug' => Str::slug($title)],
                ['parent_id' => null, 'title' => $title, 'type' => 'audience', 'sort_order' => $i + 1, 'is_active' => true]
            );
        }

        $student = $map['Студентам и абитуриентам'];
        $specialties = [
            'Музыкальное искусство эстрады',
            'Инструментальное исполнительство',
            'Вокальное искусство',
            'Сольное и хоровое народное пение',
            'Хоровое дирижирование',
            'Теория музыки',
        ];
        foreach ($specialties as $i => $title) {
            Section::updateOrCreate(
                ['slug' => Str::slug($title)],
                ['parent_id' => $student->id, 'title' => $title, 'type' => 'specialty', 'sort_order' => $i + 1, 'is_active' => true]
            );
        }

        $pro = $map['Преподавателям и профессионалам'];
        foreach (['Повышение квалификации', 'Профессиональная переподготовка', 'Стажировка'] as $i => $title) {
            Section::updateOrCreate(
                ['slug' => Str::slug($title)],
                ['parent_id' => $pro->id, 'title' => $title, 'type' => 'program', 'sort_order' => $i + 1, 'is_active' => true]
            );
        }

        $young = $map['Юным дарованиям'];
        foreach (['Детская школа искусств', 'Эстетические классы'] as $i => $title) {
            Section::updateOrCreate(
                ['slug' => Str::slug($title)],
                ['parent_id' => $young->id, 'title' => $title, 'type' => 'program', 'sort_order' => $i + 1, 'is_active' => true]
            );
        }
    }
}
