<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Projects</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" />
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
    <h2>Manage Projects</h2>
    <button class="btn btn-primary" data-toggle="modal" data-target="#projectModal" onclick="resetForm()">Add Project</button>
    <table class="table mt-3" id="project-table">
        <thead>
        <tr>
            <th>Project Name</th>
            <th>Description</th>
            <th>Customers</th>
            <th>Actions</th>
        </tr>
        </thead>
        <tbody>
        <!-- Project rows will be populated here -->
        </tbody>
    </table>

    <!-- Project Modal -->
    <div class="modal fade" id="projectModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add/Edit Project</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="project-form">
                        <div class="form-group">
                            <label for="project-name">Project Name</label>
                            <input type="text" class="form-control" id="project-name" required>
                        </div>
                        <div class="form-group">
                            <label for="project-description">Description</label>
                            <textarea class="form-control" id="project-description" required></textarea>
                        </div>
                        <div class="form-group">
                            <label for="project-customer">Customers</label>
                            <select class="form-control select2 col-md-4" id="project-customer" multiple required>
                                <!-- Customers will be populated here -->
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary" id="save-button">Save</button>
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
                    <p>Are you sure you want to delete this project?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" id="confirm-delete">Delete</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
<script>
    // API URLs
    const apiUrlCustomers = 'http://127.0.0.1:8000/api/get-all-customers'; // Change this to your actual API URL
    const addProjectUrl = 'http://127.0.0.1:8000/api/create-project'; // Change to actual API URL for adding project
    const showProjectUrl = (id) => `http://127.0.0.1:8000/api/show-project/${id}`; // Change to actual API URL for updating customer
    const deleteProjectUrl = (id) => `http://127.0.0.1:8000/api/delete-project/${id}`; // Change to actual API URL for updating customer
    const updateProjectUrl = (id) => `http://127.0.0.1:8000/api/update-project/${id}`; // Change to actual API URL for updating project
    let list_project_api = 'http://127.0.0.1:8000/api/get-all-projects'; // Change this to your actual API URL

    // Function to fetch and display projects
    function fetchProjects() {
        $.get(list_project_api, function(data) {
            const projectTableBody = $('#project-table tbody');
            projectTableBody.empty();
            data.forEach(project => {
                const customerNames = project.customers.map(customer => customer.name).join(', '); // Assuming project.customers returns an array of customer objects
                projectTableBody.append(`
                <tr>
                    <td>${project.name}</td>
                    <td>${project.description}</td>
                    <td>${customerNames}</td>
                    <td>
                        <button class="btn btn-warning btn-sm" onclick="editProject(${project.id})">Edit</button>
                        <button class="btn btn-danger btn-sm" onclick="confirmDeleteProject(${project.id})">Delete</button>
                    </td>
                </tr>
            `);
            });
        });
    }

    // Save Project (Create or Update)
    $('#save-button').on('click', function() {
        const projectData = {
            name: $('#project-name').val(),
            description: $('#project-description').val(),
            customers: $('#project-customer').val() // Get selected customer IDs
        };

        if (currentProjectId) {
            // Update project
            $.ajax({
                url: updateProjectUrl(currentProjectId),
                type: 'PUT',
                data: projectData,
                success: function() {
                    fetchProjects();
                    $('#projectModal').modal('hide');
                }
            });
        } else {
            // Create new project
            $.post(addProjectUrl, projectData, function() {
                fetchProjects();
                $('#projectModal').modal('hide');
            });
        }
    });

    // Confirm Project Deletion
    function confirmDeleteProject(id) {
        $('#deleteModal').modal('show');
        $('#confirm-delete').off('click').on('click', function() {
            $.ajax({
                url: deleteProjectUrl(id),
                type: 'DELETE',
                success: function() {
                    fetchProjects();
                    $('#deleteModal').modal('hide');
                }
            });
        });
    }

    // Edit Project
    function editProject(id) {
        currentProjectId = id; // Store the current project ID for updating
        $.get(`${showProjectUrl(id)}`, function(project) {
            $('#project-name').val(project.name);
            $('#project-description').val(project.description);

            const selectedCustomerIds = project.customers; // This should be an array of IDs
            $('#project-customer').val(selectedCustomerIds).change();

            $('#projectModal').modal('show');
        });
    }

    // Reset Form
    function resetForm() {
        currentProjectId = null; // Reset project ID for new project creation
        $('#project-name').val('');
        $('#project-description').val('');
        $('#project-customer').val([]);
    }

    // Fetch projects and customers when the page loads
    $(document).ready(function() {
        fetchProjects();
        fetchCustomersForSelect();
    });

    function fetchCustomersForSelect() {
        $.get(apiUrlCustomers, function(data) {
            const customerSelect = $('#project-customer');
            customerSelect.empty(); // Clear the existing options
            data.forEach(customer => {
                customerSelect.append(`<option value="${customer.id}">${customer.name}</option>`);
            });

            customerSelect.select2({
                placeholder: 'Select Customers',
                allowClear: true,
                width: '100%'
            });
        });


    }



</script>
</body>
</html>
