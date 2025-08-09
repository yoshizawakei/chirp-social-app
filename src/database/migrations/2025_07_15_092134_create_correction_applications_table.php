<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCorrectionApplicationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('correction_applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId("user_id")->constrained()->onDelete("cascade");
            $table->foreignId("attendance_id")->constrained()->onDelete("cascade");
            $table->time("clock_in_time_before")->nullable();
            $table->time("clock_out_time_before")->nullable();
            $table->json("rests_before")->nullable();
            $table->text("notes_before")->nullable();
            $table->time("clock_in_time_after")->nullable();
            $table->time("clock_out_time_after")->nullable();
            $table->json("rests_after")->nullable();
            $table->text("notes_after")->nullable();
            $table->tinyInteger("is_approved")->nullable()->default(null);
            $table->timestamp("approved_at")->nullable();
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
        Schema::dropIfExists('correction_applications');
    }
}
