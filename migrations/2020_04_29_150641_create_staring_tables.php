<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStaringTables extends Migration
{
    public function up()
    {
        Schema::create('stars', function(Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('starable_id');
            $table->string('starable_type', 35);
            $table->unsignedBigInteger('user_id');
            $table->enum('value', [1, 2, 3, 4, 5]);
            $table->timestamp('created_at');
        });

        Schema::create('star_stats', function(Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedBigInteger('starable_id');
            $table->string('starable_type', 35);

            $table->decimal('avg_value', 3)->default(0);
            $table->unsignedInteger('star_count')->default(1);

            $table->unsignedInteger('five_star_count')->default(0);
            $table->unsignedInteger('four_star_count')->default(0);
            $table->unsignedInteger('three_star_count')->default(0);
            $table->unsignedInteger('two_star_count')->default(0);
            $table->unsignedInteger('one_star_count')->default(0);
        });
    }

    public function down()
    {
        Schema::drop('star_stats');
        Schema::drop('stars');
    }
}
