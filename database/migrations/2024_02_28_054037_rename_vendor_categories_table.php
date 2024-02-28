<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::rename('vendor_categories', 'product_categories');
    }

    public function down()
    {
        Schema::rename('product_categories', 'vendor_categories');
    }
};
