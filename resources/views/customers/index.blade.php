<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Customers</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            display: flex;
            flex-direction: row;
        }
        .sidebar {
            min-width: 250px;
            background-color: #f8f9fa;
            padding: 15px;
        }
        .content {
            flex-grow: 1;
            padding: 15px;
        }
        .address-container {
            display: none;
        }
    </style>
</head>
<body>

<div class="sidebar">
    <h4>Navigation</h4>
    <ul class="nav flex-column">
        <li class="nav-item">
            <a class="nav-link" href="{{url('/')}}">Dashboard</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{url('/customers')}}">Manage Customers</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{url('/projects')}}">Manage Projects</a>
        </li>
    </ul>
</div>

<div class="content">
    <h2>Manage Customers</h2>
    <button class="btn btn-primary" onclick="openAddCustomerModal()">Add Customer</button>
    <table class="table mt-3" id="customer-table">
        <thead>
        <tr>
            <th>Name</th>
            <th>Company</th>
            <th>Contact Number</th>
            <th>Email</th>
            <th>Country</th>
            <th>Addresses</th>
            <th>Actions</th>
        </tr>
        </thead>
        <tbody>
        <!-- Customer rows will be populated here -->
        </tbody>
    </table>

    <!-- Customer Modal -->
    <div class="modal fade" id="customerModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add/Edit Customer</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="customer-form">
                        <!-- Hidden input for customer ID (used for updates) -->
                        <input type="hidden" id="customer-id">

                        <div class="row">
                            <div class="form-group col-md-6">
                                <label for="customer-name">Name</label>
                                <input type="text" class="form-control" id="customer-name" required>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="customer-company">Company</label>
                                <input type="text" class="form-control" id="customer-company" required>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="customer-contact-number">Contact Number</label>
                                <input type="text" class="form-control" id="customer-contact-number" required>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="customer-email">Email</label>
                                <input type="email" class="form-control" id="customer-email" required>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="customer-country">Country</label>
                                <input type="text" class="form-control" id="customer-country" required>
                            </div>
                        </div>
                        <div id="address-container">
                            <!-- Address fields will be added here -->
                        </div>

                        <button type="button" class="btn btn-secondary" onclick="addAddress()">Add Address</button>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary" form="customer-form">Save</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Confirm Deletion</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete this customer?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" id="confirm-delete">Delete</button>
                </div>
            </div>
        </div>
    </div>

</div>

<!-- Bootstrap & jQuery -->
<script src="https://code.jquery.com/jquery-3.5.1.min.js"> </script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
    // API URLs
    const listCustomersApi = 'http://127.0.0.1:8000/api/get-all-customers'; // API URL to get all customers
    const addCustomerUrl = 'http://127.0.0.1:8000/api/create-customer'; // Change to actual API URL for adding customer
    const showCustomerUrl = (id) => `http://127.0.0.1:8000/api/show-customer/${id}`; // Change to actual API URL for updating customer
    const updateCustomerUrl = (id) => `http://127.0.0.1:8000/api/update-customer/${id}`; // Change to actual API URL for updating customer
    const deleteCustomerUrl = (id) => `http://127.0.0.1:8000/api/delete-customer/${id}`; // Change to actual API URL for updating customer


    // Function to fetch and display customers
    function fetchCustomers() {
        $.get(listCustomersApi, function(data) {
            const customerTableBody = $('#customer-table tbody');
            customerTableBody.empty();
            data.forEach(customer => {
                customerTableBody.append(`
                <tr>
                    <td>${customer.name}</td>
                    <td>${customer.company}</td>
                    <td>${customer.contact_phone}</td>
                    <td>${customer.email}</td>
                    <td>${customer.country}</td>
                    <td>
                        <button class="btn btn-primary" onclick="toggleAddresses(${customer.id})">Addresses</button>
                        <div class="address-container" id="address-container-${customer.id}">
                            ${customer.addresses.map(addr => `<div>${addr.number}, ${addr.street}, ${addr.state}</div>`).join('')}
                        </div>
                    </td>
                    <td>
                        <button class="btn btn-warning btn-sm" onclick="editCustomer(${customer.id})">Edit</button>
                        <button class="btn btn-danger btn-sm" onclick="confirmDeleteCustomer(${customer.id})">Delete</button>
                    </td>
                </tr>
            `);
            });
        });
    }

    // Function to open the "Add Customer" modal
    function openAddCustomerModal() {
        // Clear form and reset to add mode
        $('#customer-form')[0].reset();
        $('#customer-id').val(''); // Clear the customer ID
        $('#address-container').empty(); // Clear address fields
        $('#customerModal').modal('show');
    }

    // Function to open the "Edit Customer" modal
    function editCustomer(id) {
        $.get(`${showCustomerUrl(id)}`, function(customer) {
            $('#customer-id').val(customer.id);
            $('#customer-name').val(customer.name);
            $('#customer-company').val(customer.company);
            $('#customer-contact-number').val(customer.contact_phone);
            $('#customer-email').val(customer.email);
            $('#customer-country').val(customer.country);
            $('#address-container').empty();
            customer.addresses.forEach(addr => {
                const addressHtml = `
                <div class="address mb-3">
                    <input type="text" class="form-control address_id" value="${addr.id}" required>
                    <div class="row">
                        <div class="form-group col-md-3">
                            <label>Number:</label>
                            <input type="text" class="form-control number" value="${addr.number}" required>
                        </div>
                        <div class="form-group col-md-3">
                            <label>Street:</label>
                            <input type="text" class="form-control street" value="${addr.street}" required>
                        </div>
                        <div class="form-group col-md-3">
                            <label>State:</label>
                            <input type="text" class="form-control state" value="${addr.state}" required>
                        </div>
                        <div class="form-group col-md-3">
                            <label>Delete</label>
                            <button type="button" class="btn btn-danger" onclick="removeAddress(this)">Delete Address</button>
                        </div>
                    </div>
                </div>
            `;
                $('#address-container').append(addressHtml);
            });
            $('#customerModal').modal('show');
        });
    }

    // Function to toggle addresses
    function toggleAddresses(customerId) {
        $(`#address-container-${customerId}`).toggle();
    }

    // Function to add address field
    function addAddress() {
        const addressHtml = `
        <div class="address mb-3">
            <div class="row">
                <div class="form-group col-md-3">
                    <label>Number:</label>
                    <input type="text" class="form-control number" required>
                </div>
                <div class="form-group col-md-3">
                    <label>Street:</label>
                    <input type="text" class="form-control street" required>
                </div>
                <div class="form-group col-md-3">
                    <label>State:</label>
                    <input type="text" class="form-control state" required>
                </div>
                <div class="form-group col-md-3">
                    <label>Delete</label>
                    <button type="button" class="btn btn-danger" onclick="removeAddress(this)">Delete Address</button>
                </div>
            </div>
        </div>
    `;
        $('#address-container').append(addressHtml);
    }

    // Function to remove address field
    function removeAddress(button) {
        $(button).closest('.address').remove();
    }

    // Handle form submission for Add/Edit customer
    $('#customer-form').on('submit', function(e) {
        e.preventDefault();

        const customerId = $('#customer-id').val();
        const customerData = {
            name: $('#customer-name').val(),
            company: $('#customer-company').val(),
            contact_phone: $('#customer-contact-number').val(),
            email: $('#customer-email').val(),
            country: $('#customer-country').val(),
            addresses: []
        };

        // Gather address data
        $('.address').each(function() {
            const address = {
                id: $(this).find('.address_id').val(),
                number: $(this).find('.number').val(),
                street: $(this).find('.street').val(),
                state: $(this).find('.state').val(),
            };
            customerData.addresses.push(address);
        });

        if (customerId) {
            // Update customer (PUT request)
            $.ajax({
                url: updateCustomerUrl(customerId),
                type: 'PUT',
                data: customerData,
                success: function() {
                    fetchCustomers();
                    $('#customerModal').modal('hide');
                }
            });
        } else {
            // Add new customer (POST request)
            $.post(addCustomerUrl, customerData, function() {
                fetchCustomers();
                $('#customerModal').modal('hide');
            });
        }
    });

    // Initial customer fetch
    $(document).ready(function() {
        fetchCustomers();
    });

    function confirmDeleteCustomer(customerId) {
        // Show the delete confirmation modal
        $('#deleteModal').modal('show');

        // Handle the delete confirmation
        $('#confirm-delete').off('click').on('click', function() {
            $.ajax({
                url: deleteCustomerUrl(customerId),  // API to delete customer
                type: 'DELETE',
                success: function(response) {
                    // Successfully deleted the customer
                    $('#deleteModal').modal('hide');
                    fetchCustomers(); // Refresh the customer list
                    alert('Customer deleted successfully.');
                },
                error: function(error) {
                    // Handle error
                    console.error('Error deleting customer:', error);
                    alert('Error deleting customer.');
                }
            });
        });
    }
</script>

</body>
</html>
