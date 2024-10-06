<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class ProjectController extends Controller
{
    public function index()
    {

    }

    public function all_projects()
    {
        return Project::with('customers')->get();
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'customers' => 'required|array',
            ]);

            $project = Project::create($validated);
            $project->customers()->sync($validated['customers']);

            return response()->json($project->load('customers'), 201);
        } catch (ValidationException $e) {
            // Return validation errors as JSON
            return response()->json([
                'errors' => $e->errors()
            ], 422);
        }
    }

    public function show($id)
    {
        // Fetch the project with its related customers
        $project = Project::with('customers')->findOrFail($id);

        $projectData = [
            'id' => $project->id,
            'name' => $project->name,
            'description' => $project->description,
            'customers' => $project->customers->pluck('id'), // Get customer IDs
        ];

        // Return the project data
        return response()->json($projectData);
    }

    public function update(Request $request, $id)
    {

        try {

            $project = Project::findOrFail($id);

            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'customers' => 'required|array',
            ]);

            $project->update($validated);
            $project->customers()->sync($validated['customers']);

            return response()->json($project->load('customers'), 200);
        } catch (ValidationException $e) {
            // Return validation errors as JSON
            return response()->json([
                'errors' => $e->errors()
            ], 422);
        }
    }

    public function delete($id)
    {
        $project = Project::findOrFail($id);

        $project->delete();
        return response()->json(['message' => 'Project deleted successfully.'], 200);
    }
}
