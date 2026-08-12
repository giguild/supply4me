<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->string('slug')->after('name')->nullable();
        });

        // Generate slugs for existing products
        $products = DB::table('products')->select('id', 'name')->get();
        foreach ($products as $product) {
            $slug = \Illuminate\Support\Str::slug($product->name);
            // Ensure uniqueness within the same company
            $existing = DB::table('products')->where('slug', $slug)->count();
            if ($existing > 0) {
                $slug = $slug . '-' . strtolower(\Illuminate\Support\Str::random(5));
            }
            DB::table('products')->where('id', $product->id)->update(['slug' => $slug]);
        }

        Schema::table('products', function (Blueprint $table) {
            $table->unique(['slug']);
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('slug');
        });
    }
};
