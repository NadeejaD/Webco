<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\CustomerAddress;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class CustomerController extends Controller
{
    public function index()
    {
        return view('customer.index');
    }

    public function all_customers()
    {
        return Customer::with('addresses')->get();
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required',
                'company' => 'required',
                'contact_phone' => 'required',
                'email' => 'required|email',
                'country' => 'required',
                'addresses.*.number' => 'required|string',
                'addresses.*.street' => 'required|string',
                'addresses.*.state' => 'required|string',
            ]);

            $customer = Customer::create($validated);

            if ($request->has('addresses')) {
                foreach ($request->addresses as $address) {
                    $customer->addresses()->create($address);
                }
            }

            return response()->json($customer->load('addresses'), 201);

        } catch (ValidationException $e) {
            // Return validation errors as JSON
            return response()->json([
                'errors' => $e->errors()
            ], 422);
        }
    }

    public function show($id)
    {
        $customer = Customer::with('addresses')->findOrFail($id);
        return response()->json($customer);
    }

    public function update(Request $request, $id)
    {
        try {
            // Validate the request data
            $validatedData = $request->validate([
                'name' => 'required|string|max:255',
                'company' => 'required|string|max:255',
                'contact_phone' => 'required|string|max:15',
                'email' => 'required|email|max:255',
                'country' => 'required|string|max:255',
                'addresses' => 'array',
                'addresses.*.id' => 'nullable|integer|exists:customer_addresses,id',
                'addresses.*.number' => 'required|string|max:255',
                'addresses.*.street' => 'required|string|max:255',
                'addresses.*.state' => 'required|string|max:255',
            ]);

            // Find the customer by ID
            $customer = Customer::findOrFail($id);

            // Update customer details
            $customer->update([
                'name' => $request->input('name'),
                'company' => $request->input('company'),
                'contact_phone' => $request->input('contact_phone'),
                'email' => $request->input('email'),
                'country' => $request->input('country'),
            ]);

            // Collect existing address IDs
            $existingAddressIds = $customer->addresses->pluck('id')->toArray();
            // Track the addresses that need to be kept or updated
            $updatedAddressIds = [];

            // Process each address from the form
            foreach ($request->input('addresses', []) as $addressData) {
                // If an address ID exists, update the existing address
                if (isset($addressData['id'])) {
                    $address = CustomerAddress::findOrFail($addressData['id']);
                    $address->update($addressData);
                    $updatedAddressIds[] = $addressData['id']; // Add to the list of updated addresses
                } else {
                    // Otherwise, create a new address for the customer
                    $customer->addresses()->create($addressData);
                }
            }

            // Delete any addresses that were removed (i.e., not in the updated list)
            $addressesToDelete = array_diff($existingAddressIds, $updatedAddressIds);
            CustomerAddress::whereIn('id', $addressesToDelete)->delete();


            return response()->json($customer->load('addresses'), 200);

        } catch (ValidationException $e) {
            // Return validation errors as JSON
            return response()->json([
                'errors' => $e->errors()
            ], 422);
        }
    }

    public function delete($id)
    {
        // Find the customer by ID or return 404 if not found
        $customer = Customer::findOrFail($id);

        // Delete the customer
        $customer->delete();

        // Return a response indicating success
        return response()->json(['message' => 'Customer deleted successfully.'], 200);
    }
}
