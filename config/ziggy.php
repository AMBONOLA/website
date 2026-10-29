<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Route Groups
    |--------------------------------------------------------------------------
    |
    | Visitors who aren't the admin only receive the "guest" routes, so the
    | login page doesn't reveal the admin URLs. Ziggy falls back to every
    | route when a group is missing, so keep this group defined.
    |
    */

    'groups' => [
        'guest' => ['home', 'login'],
    ],

];
