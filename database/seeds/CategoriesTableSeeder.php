<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategoriesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->insert(1, "Category 1", 4, 4, null);
    }

    private function insert($id, $default, $name, $photo, $parent)
    {
        DB::table("categories")->insert([
            "id" => $id,
            "default" => $default,
            "name" => $name,
            "photo" => $photo,
            "parent" => $parent
        ]);
    }
}
