<?php

// config for Vanadi/Vanadi
return [
    'default_team_column_name' => env('VANADI_DEFAULT_TEAM_COLUMN_NAME', 'team_id'),
    'default_code_column_name' => env('VANADI_DEFAULT_CODE_COLUMN_NAME', 'code'),
    'app_scheme' => env('APP_SCHEME', 'https'),
    'use_ldap' => env('USE_LDAP', false),
    'ldap_masquerade' => env('LDAP_MASQUERADE', false),
    'multitenancy' => env('USE_MULTITENANCY', false),
    'shared_models' => [],
];
