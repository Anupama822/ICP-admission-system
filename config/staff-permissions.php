<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Staff Permissions
    |--------------------------------------------------------------------------
    |
    | Pre-ticked on the "Add Staff" form. Admin can add or remove any of
    | these permissions (or any other one) before creating the account, and
    | can change the grant again later from the staff member's profile page.
    |
    */

    'defaults' => [
        'students.view',
        'applications.view',
        'reports.view',
    ],

];
