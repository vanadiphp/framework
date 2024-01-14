<?php

namespace Vanadi\Framework\Policies;
use Vanadi\Framework\Concerns\Policy\StandardPolicy;
use App\Models\User;
use Vanadi\Framework\Filament\Resources\DepartmentResource;
use Illuminate\Auth\Access\HandlesAuthorization;

class DepartmentPolicy
{
    use HandlesAuthorization, StandardPolicy;

    function getResourceClass(): string
    {
        return DepartmentResource::class;
    }
}
