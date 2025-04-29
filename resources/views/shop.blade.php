<!DOCTYPE html>
<html>
<head>
    <title>Shop Info</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h2>Shopify Store Information</h2>

    @if (session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    <table class="table table-striped">
        <tr><th>ID</th><td>{{ $shop->id }}</td></tr>
        <tr><th>Name</th><td>{{ $shop->name }}</td></tr>
        <tr><th>Domain</th><td>{{ $shop->domain }}</td></tr>
        <tr><th>Email</th><td>{{ $shop->email }}</td></tr>
        <tr><th>Country</th><td>{{ $shop->country }}</td></tr>
        <tr><th>Plan</th><td>{{ $shop->plan_name }}</td></tr>
    </table>
    
    <!-- Back to Dashboard -->
    <a href="{{ route('dashboard') }}" class="btn btn-secondary">Back</a>
</div>
</body>
</html>



