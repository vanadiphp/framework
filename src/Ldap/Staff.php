<?php

namespace Vanadi\Framework\Ldap;

use LdapRecord\Models\ActiveDirectory\User;

class Staff extends User
{
    protected ?string $connection = 'staff';
}
