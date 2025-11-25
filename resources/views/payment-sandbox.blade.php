<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sandbox Payment Simulation</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css">
</head>
<body class="bg-light">
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header">
                    <h4 class="mb-0">Sandbox Mode &mdash; {{ ucfirst($gateway) }} gateway</h4>
                </div>
                <div class="card-body">
                    <p class="mb-4">You are in sandbox mode. Choose an outcome to simulate the payment flow for
                        payment request <strong>#{{ $paymentRequest->id }}</strong>.</p>
                    <div class="d-flex justify-content-between">
                        <a class="btn btn-success" href="{{ route('payment.sandbox.result', ['gateway' => $gateway, 'payment_request' => $paymentRequest->id, 'result' => 'success']) }}">
                            Simulate Success
                        </a>
                        <a class="btn btn-danger" href="{{ route('payment.sandbox.result', ['gateway' => $gateway, 'payment_request' => $paymentRequest->id, 'result' => 'fail']) }}">
                            Simulate Failure
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>
