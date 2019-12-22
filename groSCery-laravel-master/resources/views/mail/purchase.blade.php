@component('mail::message')
# {{ $transaction->item->name }} Purchased

{{ $transaction->user->name }} purchased {{ $transaction->item->name }} for ${{ $transaction->price }}. 
You owe them your share of ${{ number_format($transaction->price / $transaction->group->users->count(),2) }}.


{{--  @component('mail::button', ['url' => $url])
View Order
@endcomponent  --}}

Thanks,<br>
{{ config('app.name') }}
@endcomponent