<?php

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PhotosTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->insertBinding(1);
        $this->insert("file_1", 1);
        $this->insertBinding(2);
        $this->insert("file_2", 2);
        $this->insertBinding(3);
        $this->insert("file_3", 3);
    }

    private function insertBinding($id)
    {
        DB::table("photo_bindings")->insert([
            "id" => $id,
            "created_at" => Carbon::now()->format('Y-m-d H:i:s'),
            "updated_at" => Carbon::now()->format('Y-m-d H:i:s')
        ]);
    }

    private function insert($filename, $binding)
    {
        DB::table("photos")->insert([
            "filename" => $filename,
            "binding" => $binding,
            "created_at" => Carbon::now()->format('Y-m-d H:i:s'),
            "updated_at" => Carbon::now()->format('Y-m-d H:i:s')
        ]);
    }
}
