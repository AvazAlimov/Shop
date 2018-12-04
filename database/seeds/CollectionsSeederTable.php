<?php

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CollectionsSeederTable extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->insert(1, "Collection 1", 3, 3);

    }

    private function insert($id, $default, $name, $photo)
    {
        DB::table("collections")->insert([
            "id" => $id,
            "default" => $default,
            "name" => $name,
            "photo" => $photo,
            "created_at" => Carbon::now()->format('Y-m-d H:i:s'),
            "updated_at" => Carbon::now()->format('Y-m-d H:i:s')
        ]);
    }
}
