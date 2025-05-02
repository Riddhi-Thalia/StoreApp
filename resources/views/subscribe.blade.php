<!DOCTYPE html>
<html>
<head>
    <title>Subscribe to Continue</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container d-flex justify-content-center align-items-center vh-100">
        <div class="card shadow-sm p-4" style="max-width: 500px; width: 100%;">
            <h4 class="text-center mb-3">Subscription Required</h4>
            <p class="text-center text-muted">You must subscribe to a plan to continue using the app.</p>

            @if (session('error'))
                <div class="alert alert-danger">
                    {{ session('error') }}
                </div>
            @endif

            <form method="POST" action="{{ secure_url(route('subscribe.plan')) }}">
                @csrf
                <input type="hidden" name="shop" value="{{ $shop ?? '' }}">

                <div class="text-center">
                    <button type="submit" class="btn btn-primary">
                        Subscribe to Basic Plan ($10/month)
                    </button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
