<!DOCTYPE html>
<html>
<head>
    <title>Sign Lingua Registration</title>
    <style>
        html, body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 14px;
            color: #22223b;
            width: 100%;
            height: 100%;
            margin: 0;
            text-align: center;
        }

    </style>
</head>
<body>
    <div class="mail-body-container" style="max-width: 400px; border: 1px solid rgb(182, 182, 182); margin: auto; padding: 20px;">
        <img src="{{ $message->embed($emailData['logo']) }}" alt="Company Logo">
        <!-- Include the user's name -->
        <p>Dear, {{ $emailData['firstname'] }}!</p><br>
        <p>We regret to inform you that your account with SignLingua ASL has been terminated effective <b>{{ date('F d, Y') }}</b>.</p>
        {{-- <p style="font-size: 12px;">You might need to log out and log in again for the changes to take effect.</p> --}}
        {{-- This decision was made due to [briefly state the reason, if applicable, such as violation of terms of service, inactivity, etc.]. --}}
        <!-- Login Button -->
        <p>Please note that you will no longer have access to your account, and any associated data has been removed as per our data retention policy.</p>
        <br>
        <p><b>Thank you for your understanding and cooperation.</b></p>
        <br>
        <p>Sincerly, <br>SignLingua ASL</p>
    </div>
</body>
</html>
