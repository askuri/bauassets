@component('mail::message')
# Hallo {{ $loan->borrower_name }},

du hast gerade die von dir ausgeliehenen Gegenstände zurückgegeben.

Du kannst die ausgeliehenen Gegenstände weiterhin ansehen:

@component('mail::button', ['url' => route('loans.show', $loan->id)])
Leihgabe ansehen
@endcomponent

Sollte etwas nicht stimmen, melde dich bitte umgehend bei der Werkzeug AG.

Mit freundlichen Grüßen,

die Werkzeug AG

@endcomponent