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
        <tr><th>Domain</th><td><a href="{{ $shop->url }}">{{ $shop->myshopify_domain }}</a></td></tr>
        <tr><th>Email</th><td>{{ $shop->email }}</td></tr>
        <tr><th>Country</th><td>{{ $shop->billingAddress->country }}</td></tr>
        <tr><th>Plan</th><td>{{ $shop->plan_name }}</td></tr>
        <tr><th>Province</th><td>{{ $shop->billingAddress->province ?? 'NA'}}</td></tr>
        <tr><th>Address</th><td>{{ $shop->billingAddress->address1 ?? 'NA'}}</td></tr>
        <tr><th>Created Date</th><td>{{ $shop->createdAt }}</td></tr>
        <tr><th>Updated Date</th><td>{{ $shop->updatedAt }}</td></tr>
    </table>

    <!-- Back to Dashboard -->
    <a href="{{ route('dashboard') }}" class="btn btn-secondary">Back</a>
</div>
</body>
</html>



