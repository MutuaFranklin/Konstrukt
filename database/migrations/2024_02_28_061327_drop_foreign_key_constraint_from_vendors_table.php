<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('vendors', function (Blueprint $table) {
            $table->dropForeign('vendors_category_id_foreign');
        });
    }

    public function down()
    {
        Schema::table('vendors', function (Blueprint $table) {
            // Recreate the foreign key constraint if necessary
            $table->foreignId('category_id')->constrained()->onDelete('cascade');
        });
    }
};
