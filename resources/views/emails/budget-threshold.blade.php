@component('mail::message')
# 💰 Licne finansije

## Upozorenje o budzetu

Pozdrav, **{{ $user->name }}**,

{{ $alert_message }}

**Kategorija:** {{ $category->name }}
**Limit:** {{ number_format($limit, 2, ',', '.') }} RSD
**Potroseno:** {{ number_format($spent, 2, ',', '.') }} RSD
**Procenat:** {{ $percent }}%

[Pregledaj budzete]({{ $url }})

Hvala sto koristite aplikaciju.
