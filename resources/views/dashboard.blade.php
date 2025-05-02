<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="text-center mb-4">
            <h2 class="fw-bold">Welcome, {{ $shopName }}!</h2>
        </div>

        @if (session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif

        <div class="text-center mb-4">
            <a href="{{ route('store') }}" class="btn btn-outline-primary">View Store</a>
        </div>

        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">Subscription Info</h5>
            </div>
            <div class="card-body">
                <ul class="list-group list-group-flush">
                    <li class="list-group-item d-flex justify-content-between">
                        <strong>Plan Name:</strong>
                        <span class="text-dark"> Basic Plan </span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between">
                        <strong>Status:</strong>
                        <span class="badge bg-success">Active</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</body>
</html>
