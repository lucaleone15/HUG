<?php

return [
    'required' => 'The :attribute field is required.',
    'string'   => 'The :attribute field must be a string.',
    'email'    => 'The :attribute field must be a valid email address.',
    'integer'  => 'The :attribute field must be an integer.',
    'boolean'  => 'The :attribute field must be true or false.',
    'image'    => 'The :attribute field must be an image.',
    'url'      => 'The :attribute field must be a valid URL.',
    'regex'    => 'The :attribute field format is invalid.',
    'in'       => 'The selected value for :attribute is invalid.',
    'max' => [
        'string'  => 'The :attribute field must not exceed :max characters.',
        'numeric' => 'The :attribute field must not exceed :max.',
        'file'    => 'The :attribute file must not exceed :max kilobytes.',
        'array'   => 'The :attribute field must not have more than :max items.',
    ],
    'min' => [
        'string'  => 'The :attribute field must be at least :min characters.',
        'numeric' => 'The :attribute field must be at least :min.',
        'file'    => 'The :attribute file must be at least :min kilobytes.',
        'array'   => 'The :attribute field must have at least :min items.',
    ],

    'custom' => [],

    'attributes' => [
        'name'            => 'name',
        'email'           => 'email address',
        'type'            => 'type',
        'message'         => 'message',
        'employee_count'  => 'headcount',
        'contact_name'    => 'contact name',
        'contact_email'   => 'contact email address',
        'primary_color'   => 'primary colour',
        'secondary_color' => 'secondary colour',
        'logo'            => 'logo',
        'logo_url'        => 'logo URL',
        'wants_trophy'    => 'trophy',
        'answers'         => 'answers',
    ],
];
