<?php

namespace Database\Seeders;

use App\Models\Project;
use App\Models\Customer;
use Illuminate\Database\Seeder;

class ProjectSeeder extends Seeder
{
    public function run()
    {
        // Create customers
        $customers = Customer::factory()->count(5)->create();

        // Create projects and associate them with customers
        foreach ($customers as $customer) {
            $project = Project::factory()->create([
                'name' => "Project for {$customer->name}",
                'description' => 'Description for the project.',
            ]);

            // Associate customers with the project
            $project->customers()->attach($customer->id);
        }
    }
}
