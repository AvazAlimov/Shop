<?php /** @noinspection PhpUndefinedMethodInspection */

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCollectionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('collections', function (Blueprint $table) {
            $table->increments('id');
            $table->string("default");
            $table->unsignedInteger("name");
            $table->unsignedInteger("photo")->nullable();
            $table->timestamps();

            $table->foreign("name")->references("id")->on("translation_bindings")->onDelete("cascade");
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
        Schema::dropIfExists('collections');
    }
}
