<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Project;
use App\Models\Customer;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProjectControllerTest extends TestCase
{
    use RefreshDatabase; // This trait will reset the database for each test

    /** @test */
    public function it_retrieves_all_projects()
    {
        Project::factory()->count(3)->create();

        $response = $this->getJson('/api/get-all-projects');

        $response->assertStatus(200);
        $this->assertCount(3, $response->json());
    }

    /** @test */
    public function it_stores_a_project()
    {
        $customer = Customer::factory()->create(); // Create a customer first

        $data = [
            'name' => 'New Project',
            'description' => 'Project description',
            'customers' => [$customer->id], // Associate the project with the customer
        ];

        $response = $this->postJson('/api/create-project', $data);

        $response->assertStatus(201);
        $this->assertDatabaseHas('projects', ['name' => 'New Project']);
        $this->assertDatabaseHas('customer_project', [
            'project_id' => $response->json('id'), // Check the pivot table
            'customer_id' => $customer->id,
        ]);
    }

    /** @test */
    public function it_updates_a_project()
    {
        $project = Project::factory()->create([
            'name' => 'Old Project',
            'description' => 'Old description',
        ]);

        $customer = Customer::factory()->create(); // Create a new customer

        $data = [
            'name' => 'Updated Project',
            'description' => 'Updated description',
            'customers' => [$customer->id], // Update the associated customer
        ];

        $response = $this->putJson("/api/update-project/{$project->id}", $data);

        $response->assertStatus(200);
        $this->assertDatabaseHas('projects', ['id' => $project->id, 'name' => 'Updated Project']);
        $this->assertDatabaseHas('customer_project', [
            'project_id' => $project->id,
            'customer_id' => $customer->id,
        ]);
    }

    /** @test */
    public function it_deletes_a_project()
    {
        $project = Project::factory()->create();

        $response = $this->deleteJson("/api/delete-project/{$project->id}");

        $response->assertStatus(200);
        $this->assertDatabaseMissing('projects', ['id' => $project->id]);
    }


}
