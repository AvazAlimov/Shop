<?php

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LanguagesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->insert("uz");
        $this->insert("ru");
        $this->insert("en");
        $this->insert("it");
    }

    private function insert($code)
    {
        DB::table("languages")->insert([
            "code" => $code,
            "created_at" => Carbon::now()->format('Y-m-d H:i:s'),
            "updated_at" => Carbon::now()->format('Y-m-d H:i:s')
        ]);
    }
}
