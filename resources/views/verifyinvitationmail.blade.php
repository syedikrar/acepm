@component('mail::message')

# Please verify your email

An invitation has been sent by <strong>{{ $userInfo->name }}</strong> <br>
Please click the button below to verify your email to register on <strong>{{ config('app.name') }}</strong>


<a href="{{ url('https://acepm.ap.ngrok.io/#/register/'.$data) }}" 
style="background-color: #fead37;border: none;color: white;padding: 5px 10px;text-align: center;font-weight:bold;text-decoration: none;display: inline-block;font-size: 12px;">Verify Email</a><br>
<br>For any query you can contact on <strong>{{ $userInfo->email }}</strong>

Thanks,<br>
<strong>{{ $userInfo->name }}</strong>

@endcomponent
