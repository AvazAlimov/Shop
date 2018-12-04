<?php

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SeasonsSeederTable extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->insert(1, "Season 1", 2, 2);
    }

    private function insert($id, $default, $name, $photo)
    {
        DB::table("seasons")->insert([
            "id" => $id,
            "default" => $default,
            "name" => $name,
            "photo" => $photo,
            "created_at" => Carbon::now()->format('Y-m-d H:i:s'),
            "updated_at" => Carbon::now()->format('Y-m-d H:i:s')
        ]);
    }
}
