<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('replies', function (Blueprint $table) {
            $table->id();

            //trường khóa ngoại
            $table->unsignedBigInteger('user_id');
            //trường khóa ngoại
            $table->unsignedBigInteger('comment_id');
            //thiết lập khóa ngoại
            $table->foreign('comment_id')->references('id')->on('comments')->onDelete('cascade');
            //thiết lập khóa ngoại
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            //onDelete Khi xóa category, tự động xóa products

            $table->text('content');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('replies');
    }
};
