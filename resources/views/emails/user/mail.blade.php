@component('mail::message')
    Tes
    @component('mail::button',['url' => '/'])
        View Component
    @endcomponent
    Thanks,<br>
    {{config('app.name')}}
@endcomponent
