<?php

namespace Vanadi\Framework\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Vanadi\Framework\Concerns\Policy\StandardPolicy;
use Vanadi\Framework\Filament\Resources\DepartmentResource;

class DepartmentPolicy
{
    use HandlesAuthorization;
    use StandardPolicy;

    public function getResourceClass(): string
    {
        return DepartmentResource::class;
    }
}
