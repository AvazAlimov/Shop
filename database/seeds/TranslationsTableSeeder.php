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
        $this->insert("uz", "Brand uz", $binding);
        $this->insert("ru", "Brand ru", $binding);
        $this->insert("en", "Brand en", $binding);
        $this->insert("it", "Brand it", $binding);

        $binding = 2;
        $this->insertBinding($binding);
        $this->insert("uz", "Season 1 uz", $binding);
        $this->insert("ru", "Season 1 ru", $binding);
        $this->insert("en", "Season 1 en", $binding);
        $this->insert("it", "Season 1 it", $binding);

        $binding = 3;
        $this->insertBinding($binding);
        $this->insert("uz", "Collection 1 uz", $binding);
        $this->insert("ru", "Collection 1 ru", $binding);
        $this->insert("en", "Collection 1 en", $binding);
        $this->insert("it", "Collection 1 it", $binding);
    }

    private function insertBinding($id)
    {
        DB::table("translation_bindings")->insert([
            "id" => $id,
            "created_at" => Carbon::now()->format('Y-m-d H:i:s'),
            "updated_at" => Carbon::now()->format('Y-m-d H:i:s')
        ]);
    }

    private function insert($code, $value, $binding)
    {
        DB::table("translations")->insert([
            "code" => $code,
            "value" => $value,
            "binding" => $binding,
            "created_at" => Carbon::now()->format('Y-m-d H:i:s'),
            "updated_at" => Carbon::now()->format('Y-m-d H:i:s')
        ]);
    }
}
