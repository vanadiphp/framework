<?php

namespace Vanadi\Framework\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Database\Eloquent\Model;
use Vanadi\Framework\Concerns\Policy\StandardPolicy;
use Vanadi\Framework\Filament\Resources\CountryResource;

class CountryPolicy
{
    use HandlesAuthorization;
    use StandardPolicy;

    public function getResourceClass(): string
    {
        return CountryResource::class;
    }

    public function update(User $user, Model $model): bool
    {
        return false;
    }

    public function delete(User $user, Model $model)
    {
        return false;
    }

    public function deleteAny(User $user): bool
    {
        return false;
    }
}
