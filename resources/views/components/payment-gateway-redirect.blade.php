{{--
    Generic auto-submit form to hand off to an external payment gateway.
    Accepts $action (target URL) and $fields (associative array of inputs).
--}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Redirecting to payment gateway...</title>
</head>
<body>
<form id="gateway-redirect-form" action="{{ $action }}" method="POST">
    @foreach($fields as $name => $value)
        <input type="hidden" name="{{ $name }}" value="{{ $value }}">
    @endforeach
</form>
<script>
    document.getElementById('gateway-redirect-form').submit();
</script>
<p>{{ translate('Redirecting you to the payment gateway...') }}</p>
</body>
</html>
