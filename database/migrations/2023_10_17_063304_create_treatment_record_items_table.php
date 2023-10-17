<?php

use App\Models\TreatmentRecord;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTreatmentRecordItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('treatment_record_items', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->foreignIdFor(TreatmentRecord::class);
            $table->string('tooth')->nullable();
            $table->string('finding')->nullable();
            $table->string('treatment')->nullable();
            $table->string('status')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('treatment_record_items');
    }
}
