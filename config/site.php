<?php

return [

    'contact' => [
        'name' => env('SITE_CONTACT_NAME', 'Academia Desportiva da Mealhada'),
        'address' => env('SITE_CONTACT_ADDRESS', "Pavilhão Municipal da Mealhada\n3050 Mealhada, Portugal"),
        'email' => env('SITE_CONTACT_EMAIL', 'geral@admealhada.pt'),
        'phone' => env('SITE_CONTACT_PHONE', '+351 231 000 000'),
        'facebook' => env('SITE_CONTACT_FACEBOOK'),
        'instagram' => env('SITE_CONTACT_INSTAGRAM'),
    ],

    'user' => [
        'name' => env('USER_NAME', 'Samuel Pereira'),
        'email' => env('USER_EMAIL', 'samuel@datahost.pt'),
        'password' => env('USER_PASSWORD', '12345678'),
    ],

];
