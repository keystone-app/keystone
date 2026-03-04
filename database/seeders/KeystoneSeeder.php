<?php

namespace Database\Seeders;

use App\Domain\Identity\Models\User;
use App\Domain\Legal\Models\Document;
use App\Domain\Legal\Models\Lease;
use App\Domain\Property\Models\Property;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class KeystoneSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Create a Landlord
        $landlord = User::create([
            'name' => 'Alice Landlord',
            'email' => 'alice@landlord.com',
            'password' => Hash::make('password'),
            'role' => 'landlord',
        ]);

        // 2. Create a Tenant
        $tenant = User::create([
            'name' => 'Bob Tenant',
            'email' => 'bob@tenant.com',
            'password' => Hash::make('password'),
            'role' => 'tenant',
        ]);

        // 3. Create a Property for the Landlord
        $property = Property::create([
            'user_id' => $landlord->id,
            'name' => 'Modern Apartment 101',
            'address' => '123 Legal Lane, Suite 101',
            'description' => 'A spacious modern apartment with great views.',
            'status' => 'rented',
        ]);

        // 4. Create a Lease connecting them
        $lease = Lease::create([
            'property_id' => $property->id,
            'landlord_id' => $landlord->id,
            'tenant_id' => $tenant->id,
            'start_date' => now()->startOfMonth(),
            'end_date' => now()->addYear(),
            'rent_amount' => 2500.00,
            'status' => 'active',
        ]);

        // 5. Create a compliance document for the lease
        Document::create([
            'lease_id' => $lease->id,
            'name' => 'Signed Lease Agreement',
            'path' => 'leases/agreements/lease-001.pdf',
            'type' => 'lease_agreement',
        ]);
    }
}
