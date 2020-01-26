@component('mail::message')
# Hallo {{ $loan->borrower_name }},

du hast gerade etwas aus der Werkzeug AG ausgeliehen.

Bitte überprüfe die von dir
ausgeliehenen Gegenstände auf Richtigkeit und Vollständigkeit:

@component('mail::button', ['url' => route('loans.show', $loan->id)])
Leihgabe prüfen
@endcomponent

Sollte etwas nicht stimmen, melde dich bitte umgehend bei der Werkzeug AG.

Mit freundlichen Grüßen,

die Werkzeug AG

@endcomponent