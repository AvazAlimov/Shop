<?php /** @noinspection PhpUndefinedMethodInspection */

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->increments('id');
            $table->string("default");
            $table->unsignedInteger("name")->nullable();
            $table->unsignedInteger("photo")->nullable();
            $table->unsignedInteger("brand")->nullable();
            $table->unsignedInteger("season")->nullable();
            $table->unsignedInteger("category")->nullable();
            $table->unsignedInteger("collection")->nullable();
            $table->unsignedInteger("description")->nullable();
            $table->timestamps();

            $table->foreign("name")->references("id")->on("translation_bindings")->onDelete("set null");
            $table->foreign("brand")->references("id")->on("brands")->onDelete("set null");
            $table->foreign("collection")->references("id")->on("collections")->onDelete("set null");
            $table->foreign("season")->references("id")->on("seasons")->onDelete("set null");
            $table->foreign("category")->references("id")->on("categories")->onDelete("set null");
            $table->foreign("description")->references("id")->on("translation_bindings")->onDelete("set null");
            $table->foreign("photo")->references("id")->on("photo_bindings")->onDelete("set null");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('products');
    }
}
