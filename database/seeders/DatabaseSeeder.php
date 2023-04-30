<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {

        $this->call([
            CountrySeeder::class,
            TranslationSeeder::class,
            DataTypeSeeder::class,
            // UserSeeder::class,
            GeneralCustomerSeeder::class,
            NewCustomerSeeder::class,
            CustomTableSeeder::class,
            RpInstallmentPaymentTypeSeeder::class,
            RpInstallmentStatusSeeder::class,
            PaymentMethodSeeder::class,
            // DataTypeSeeder::class,
            // UserSeeder::class
            // ArchiveFileSeeder::class

        ]);
        DB::unprepared(file_get_contents(public_path("country_lookup.sql")));
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'pname' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

    }
}
