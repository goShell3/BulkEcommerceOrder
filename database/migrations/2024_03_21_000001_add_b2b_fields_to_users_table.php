<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
                
            if (!Schema::hasColumn('users', 'phone')) {
                    $table->string('phone')->nullable()->after('email');
                } else {
                    $table->string('phone')->nullable()->after('email')->change();
                }
        
            $table->string('phone')->nullable()->change()->after('email');
            $table->string('company_name')->nullable()->after('phone');
            $table->string('tax_number')->nullable()->after('company_name');
            $table->string('company_registration_number')->nullable()->after('tax_number');
            $table->string('vat_number')->nullable()->after('company_registration_number');
            $table->string('business_type')->nullable()->after('vat_number');
            $table->string('industry')->nullable()->after('business_type');
            $table->decimal('credit_limit', 10, 2)->default(0)->after('industry');
            $table->decimal('available_credit', 10, 2)->default(0)->after('credit_limit');
            $table->boolean('is_verified')->default(false)->after('available_credit');
            $table->timestamp('verified_at')->nullable()->after('is_verified');
            $table->text('verification_notes')->nullable()->after('verified_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'phone',
                'company_name',
                'tax_number',
                'company_registration_number',
                'vat_number',
                'business_type',
                'industry',
                'credit_limit',
                'available_credit',
                'is_verified',
                'verified_at',
                'verification_notes'
            ]);
        });
    }
}; 