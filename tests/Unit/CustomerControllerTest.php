<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Customer;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CustomerControllerTest extends TestCase
{
    use RefreshDatabase; // This trait will reset the database for each test

    /** @test */
    public function it_retrieves_all_customers()
    {
        Customer::factory()->count(3)->create();

        $response = $this->getJson('/api/get-all-customers');

        $response->assertStatus(200);
        $this->assertCount(3, $response->json());
    }


    /** @test */
    public function it_stores_a_customer()
    {
        $data = [
            'name' => 'John Doe',
            'company' => 'Doe Enterprises',
            'contact_phone' => '123-456-7890',
            'email' => 'john.doe@example.com',
            'country' => 'USA',
            'addresses' => [
                [
                    'number' => '123',
                    'street' => 'Main St',
                    'state' => 'CA',
                ],
            ],
        ];

        $response = $this->postJson('/api/create-customer', $data);

        $response->assertStatus(201);
        $this->assertDatabaseHas('customers', ['email' => 'john.doe@example.com']);
    }

    /** @test */
    public function it_updates_a_customer()
    {
        $customer = Customer::factory()->create([
            'name' => 'Jane Doe',
            'company' => 'Doe Industries',
            'contact_phone' => '123-456-7890',
            'email' => 'jane.doe@example.com',
            'country' => 'USA',
        ]);

        $data = [
            'name' => 'Jane Smith',
            'company' => 'Smith Industries',
            'contact_phone' => '987-654-3210',
            'email' => 'jane.smith@example.com',
            'country' => 'USA',
            'addresses' => [
                [
                    'number' => '456',
                    'street' => 'Second St',
                    'state' => 'NY',
                ],
            ],
        ];

        $response = $this->putJson("/api/update-customer/{$customer->id}", $data);

        $response->assertStatus(200);
        $this->assertDatabaseHas('customers', ['id' => $customer->id, 'name' => 'Jane Smith']);
    }

    /** @test */
    public function it_deletes_a_customer()
    {
        $customer = Customer::factory()->create();

        $response = $this->deleteJson("/api/delete-customer/{$customer->id}");

        $response->assertStatus(200);
        $this->assertDatabaseMissing('customers', [
            'id' => $customer->id,
        ]);
    }


}
