<?php

return [
    'required' => 'Das Feld :attribute ist erforderlich.',
    'string'   => 'Das Feld :attribute muss eine Zeichenkette sein.',
    'email'    => 'Das Feld :attribute muss eine gültige E-Mail-Adresse sein.',
    'integer'  => 'Das Feld :attribute muss eine ganze Zahl sein.',
    'boolean'  => 'Das Feld :attribute muss wahr oder falsch sein.',
    'image'    => 'Das Feld :attribute muss ein Bild sein.',
    'url'      => 'Das Feld :attribute muss eine gültige URL sein.',
    'regex'    => 'Das Format des Feldes :attribute ist ungültig.',
    'in'       => 'Der gewählte Wert für :attribute ist ungültig.',
    'max' => [
        'string'  => 'Das Feld :attribute darf nicht mehr als :max Zeichen haben.',
        'numeric' => 'Das Feld :attribute darf nicht größer als :max sein.',
        'file'    => 'Die Datei :attribute darf nicht größer als :max Kilobyte sein.',
        'array'   => 'Das Feld :attribute darf nicht mehr als :max Elemente haben.',
    ],
    'min' => [
        'string'  => 'Das Feld :attribute muss mindestens :min Zeichen haben.',
        'numeric' => 'Das Feld :attribute muss mindestens :min sein.',
        'file'    => 'Die Datei :attribute muss mindestens :min Kilobyte groß sein.',
        'array'   => 'Das Feld :attribute muss mindestens :min Elemente haben.',
    ],

    'custom' => [],

    'attributes' => [
        'name'            => 'Name',
        'email'           => 'E-Mail-Adresse',
        'type'            => 'Typ',
        'message'         => 'Nachricht',
        'employee_count'  => 'Mitarbeiterzahl',
        'contact_name'    => 'Name der Kontaktperson',
        'contact_email'   => 'E-Mail der Kontaktperson',
        'primary_color'   => 'Hauptfarbe',
        'secondary_color' => 'Sekundärfarbe',
        'logo'            => 'Logo',
        'logo_url'        => 'Logo-URL',
        'wants_trophy'    => 'Trophäe',
        'answers'         => 'Antworten',
    ],
];
