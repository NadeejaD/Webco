<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\CustomerAddress;
use Illuminate\Database\Seeder;

class CustomerSeeder extends Seeder
{
    public function run()
    {
        // Create customers with addresses
        $customers = Customer::factory()->count(5)->create()->each(function ($customer) {
            // Create addresses for each customer
            $addresses = [
                [
                    'number' => '123',
                    'street' => 'Main St',
                    'state' => 'CA',
                ],
                [
                    'number' => '456',
                    'street' => 'Second St',
                    'state' => 'CA',
                ],
            ];

            // Loop through each address and create them
            foreach ($addresses as $address) {
                $customer->addresses()->create($address);
            }
        });
    }
}
