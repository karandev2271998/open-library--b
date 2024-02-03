<div>
    To reset your password, simply follow these steps: <br>

    Click on the following link to go to the password reset page: <a href="{{ env('MAIL_BASE_URL') . $mailInfo['userId'] }}">Reset Password</a> <br>

    Best regards,

    {{ env('APP_NAME') }}
</div>
