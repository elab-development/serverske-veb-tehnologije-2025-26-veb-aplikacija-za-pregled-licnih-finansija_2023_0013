@component('mail::message')
# 💰 Licne finansije

## Upozorenje o budzetu

Pozdrav, **{{ $user->name }}**,

{{ $message }}

**Kategorija:** {{ $category->name }}
**Limit:** {{ number_format($limit, 2, ',', '.') }} RSD
**Potroseno:** {{ number_format($spent, 2, ',', '.') }} RSD
**Procenat:** {{ $percent }}%

@component('mail::button', ['url' => $url])
Pregledaj budzete
@endcomponent

Hvala sto koristite aplikaciju.
@endcomponent
