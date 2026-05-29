<?php

return [
    'required' => 'Il campo :attribute è obbligatorio.',
    'string'   => 'Il campo :attribute deve essere una stringa.',
    'email'    => 'Il campo :attribute deve essere un indirizzo e-mail valido.',
    'integer'  => 'Il campo :attribute deve essere un numero intero.',
    'boolean'  => 'Il campo :attribute deve essere vero o falso.',
    'image'    => 'Il campo :attribute deve essere un\'immagine.',
    'url'      => 'Il campo :attribute deve essere un URL valido.',
    'regex'    => 'Il formato del campo :attribute non è valido.',
    'in'       => 'Il valore selezionato per :attribute non è valido.',
    'max' => [
        'string'  => 'Il campo :attribute non deve superare :max caratteri.',
        'numeric' => 'Il campo :attribute non deve essere maggiore di :max.',
        'file'    => 'Il file :attribute non deve superare :max kilobyte.',
        'array'   => 'Il campo :attribute non deve avere più di :max elementi.',
    ],
    'min' => [
        'string'  => 'Il campo :attribute deve avere almeno :min caratteri.',
        'numeric' => 'Il campo :attribute deve essere almeno :min.',
        'file'    => 'Il file :attribute deve essere almeno :min kilobyte.',
        'array'   => 'Il campo :attribute deve avere almeno :min elementi.',
    ],

    'custom' => [],

    'attributes' => [
        'name'            => 'nome',
        'email'           => 'indirizzo e-mail',
        'type'            => 'tipo',
        'message'         => 'messaggio',
        'employee_count'  => 'numero di dipendenti',
        'contact_name'    => 'nome del contatto',
        'contact_email'   => 'e-mail del contatto',
        'primary_color'   => 'colore principale',
        'secondary_color' => 'colore secondario',
        'logo'            => 'logo',
        'logo_url'        => 'URL del logo',
        'wants_trophy'    => 'trofeo',
        'answers'         => 'risposte',
    ],
];
