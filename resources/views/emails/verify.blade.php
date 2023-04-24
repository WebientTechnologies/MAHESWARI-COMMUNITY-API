@component('mail::message')
# Email Verification

Thank you for signing up. 
Use this temporary password to login <h4>{{$pin}}</h4>

Thanks,<br>
{{ config('app.name') }}
@endcomponent
