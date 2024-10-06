<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
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
    </style>
</head>
<body>

<div class="sidebar">
    <h4>Navigation</h4>
    <ul class="nav flex-column">
        <li class="nav-item">
            <a class="nav-link" href="{{url('/customers')}}">Manage Customers</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="projects.html">Manage Projects</a>
        </li>
    </ul>
</div>

<div class="content">
    <h2>Welcome to the Management Dashboard</h2>
    <p>Select a section from the sidebar to manage Customers or Projects.</p>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
