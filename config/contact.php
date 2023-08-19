<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Contactable Methods
    |--------------------------------------------------------------------------
    |
    | Just add another option and don't delete previous ones.
    | But still can edit the value of the KEY --> ['name']
    */

    'contact-methods' => [
        'regular_phone_call' => [
            'name' => 'Regular Phone Call',
            'value' => 'regular_phone_call'
        ],
        'signal' => [
            'name' => 'Signal',
            'value' => 'signal'
        ],
        'telegram' => [
            'name' => 'Telegram',
            'value' => 'telegram'
        ],
        'viber' => [
            'name' => 'Viber',
            'value' => 'viber'
        ],
        'whatsapp' => [
            'name' => 'Whatsapp',
            'value' => 'whatsapp'
        ],
    ],
];