<?php

namespace Vanadi\Framework\Ldap;

use LdapRecord\Models\ActiveDirectory\User;

class Student extends User
{
    protected ?string $connection = 'students';
}
