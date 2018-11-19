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
            $table->unsignedInteger("name");
            $table->string("default");
            $table->unsignedInteger("photo");
            $table->timestamps();

            $table->foreign("name")->references("id")->on("translation_bindings")->onDelete("cascade");
            $table->foreign("photo")->references("id")->on("photo_bindings")->onDelete("cascade");
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
