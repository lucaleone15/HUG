<?php

return [
    'required' => 'Le champ :attribute est obligatoire.',
    'string'   => 'Le champ :attribute doit être une chaîne de caractères.',
    'email'    => 'Le champ :attribute doit être une adresse e-mail valide.',
    'integer'  => 'Le champ :attribute doit être un nombre entier.',
    'boolean'  => 'Le champ :attribute doit être vrai ou faux.',
    'image'    => 'Le champ :attribute doit être une image.',
    'url'      => 'Le champ :attribute doit être une URL valide.',
    'regex'    => 'Le format du champ :attribute est invalide.',
    'in'       => 'La valeur sélectionnée pour :attribute est invalide.',
    'max' => [
        'string'  => 'Le champ :attribute ne doit pas dépasser :max caractères.',
        'numeric' => 'Le champ :attribute ne doit pas dépasser :max.',
        'file'    => 'Le fichier :attribute ne doit pas dépasser :max kilo-octets.',
        'array'   => 'Le champ :attribute ne doit pas avoir plus de :max éléments.',
    ],
    'min' => [
        'string'  => 'Le champ :attribute doit avoir au moins :min caractères.',
        'numeric' => 'Le champ :attribute doit être supérieur ou égal à :min.',
        'file'    => 'Le fichier :attribute doit faire au moins :min kilo-octets.',
        'array'   => 'Le champ :attribute doit avoir au moins :min éléments.',
    ],

    'custom' => [],

    'attributes' => [
        'name'            => 'nom',
        'email'           => 'adresse e-mail',
        'type'            => 'type',
        'message'         => 'message',
        'employee_count'  => 'effectif',
        'contact_name'    => 'nom du contact',
        'contact_email'   => 'adresse e-mail du contact',
        'primary_color'   => 'couleur principale',
        'secondary_color' => 'couleur secondaire',
        'logo'            => 'logo',
        'logo_url'        => 'URL du logo',
        'wants_trophy'    => 'trophée',
        'answers'         => 'réponses',
    ],
];
