@component('mail::message')
# Reset your password
@php
$appName = DB::table('systemflag')
    ->where('name', 'AppName')
    ->select('value')
    ->first();
@endphp
Dear {{$email}},

Below is the link to verify your account and new Password of your login
@component('mail::button', ['url' => 'http://192.168.29.114:8000/admin/reset-password?token='.$token])
Reset Password
@endcomponent

Thanks,<br>
{{$appName->value}}
@endcomponent
