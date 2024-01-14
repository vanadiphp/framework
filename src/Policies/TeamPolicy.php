<?php

namespace Vanadi\Framework\Policies;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Vanadi\Framework\Concerns\Policy\StandardPolicy;
use Vanadi\Framework\Filament\Resources\TeamResource;

class TeamPolicy
{
    use HandlesAuthorization, StandardPolicy;

    function getResourceClass(): string
    {
        return TeamResource::class;
    }
}
