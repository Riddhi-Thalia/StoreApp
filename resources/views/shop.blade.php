<!DOCTYPE html>
<html>
<head>
    <title>Shop Info</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-5">
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
            <h4 class="mb-0">Shopify Store Information</h4>
        </div>

        <div class="card-body">
            @if (session('error'))
                <div class="alert alert-danger">
                    {{ session('error') }}
                </div>
            @endif

            <table class="table table-bordered table-hover">
                <tbody>
                    <tr><th>ID</th><td>{{ $shop->id }}</td></tr>
                    <tr><th>Name</th><td>{{ $shop->name }}</td></tr>
                    <tr><th>Domain</th><td><a href="{{ $shop->url }}" target="_blank">{{ $shop->myshopify_domain }}</a></td></tr>
                    <tr><th>Email</th><td>{{ $shop->email }}</td></tr>
                    <tr><th>Country</th><td>{{ $shop->billingAddress->country ?? 'NA' }}</td></tr>
                    <tr><th>Plan</th><td>{{ $shop->plan_name ?? 'NA' }}</td></tr>
                    <tr><th>Province</th><td>{{ $shop->billingAddress->province ?? 'NA' }}</td></tr>
                    <tr><th>Address</th><td>{{ $shop->billingAddress->address1 ?? 'NA' }}</td></tr>
                    <tr><th>Created Date</th><td>{{ $shop->createdAt }}</td></tr>
                    <tr><th>Updated Date</th><td>{{ $shop->updatedAt }}</td></tr>
                </tbody>
            </table>
        </div>

        <div class="card-footer text-end">
            <a href="{{ route('dashboard') }}" class="btn btn-secondary">Back to Dashboard</a>
        </div>
    </div>
</div>
</body>
</html>
