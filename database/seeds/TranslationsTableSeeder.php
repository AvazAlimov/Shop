<?php

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TranslationsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //Brand names
        $binding = 1;
        $this->insertBinding($binding);
        $this->insert(1, "uz", "Brand uz", $binding);
        $this->insert(2, "ru", "Brand ru", $binding);
        $this->insert(3, "en", "Brand en", $binding);
        $this->insert(4, "it", "Brand it", $binding);
    }

    private function insertBinding($id)
    {
        DB::table("translation_bindings")->insert([
            "id" => $id,
            "created_at" => Carbon::now()->format('Y-m-d H:i:s'),
            "updated_at" => Carbon::now()->format('Y-m-d H:i:s')
        ]);
    }

    private function insert($id, $code, $value, $binding)
    {
        DB::table("translations")->insert([
            "id" => $id,
            "code" => $code,
            "value" => $value,
            "binding" => $binding,
            "created_at" => Carbon::now()->format('Y-m-d H:i:s'),
            "updated_at" => Carbon::now()->format('Y-m-d H:i:s')
        ]);
    }
}
