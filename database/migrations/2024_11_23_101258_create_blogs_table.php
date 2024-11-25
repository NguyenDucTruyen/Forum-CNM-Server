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
        Schema::create('blogs', function (Blueprint $table) {
            $table->id();
            //trường khóa ngoại
            $table->unsignedBigInteger('user_id');
            //trường khóa ngoại
            $table->unsignedBigInteger('category_id');
            //thiết lập khóa ngoại
            $table->foreign('category_id')->references('id')->on('categories')->onDelete('cascade');
            //thiết lập khóa ngoại
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            //onDelete Khi xóa category, tự động xóa products

            $table->string('title')->nullable();
            $table->text('content')->nullable();
            $table->string('blogImage')->nullable();
            $table->string('statusBlog')->default('pending');
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
        Schema::dropIfExists('blogs');
    }
};
