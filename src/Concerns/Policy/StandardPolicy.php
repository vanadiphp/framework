<?php

namespace Vanadi\Framework\Concerns\Policy;

use App\Models\User;
use BezhanSalleh\FilamentShield\Facades\FilamentShield;
use Illuminate\Database\Eloquent\Model;
use Vanadi\Framework\Concerns\Model\Immutable;
use Vanadi\Framework\Helpers\Framework;

trait StandardPolicy
{
    abstract public function getResourceClass(): string;

    public function getSuffix(): string
    {
        return FilamentShield::getPermissionIdentifier($this->getResourceClass());
    }

    public function makeSuffixFromModel(Model | string $model): string
    {
        if (is_string($model)) {
            $class = $model::getModel()->getMorphClass();
        } else {
            $class = $model->getMorphClass();
        }

        return \Str::of(\Str::of($class)->explode('\\')->last() ?? '')
            ->snake('::')->toString();
    }

    public function viewAny(User $user): bool
    {
        return $user->can($this->perm('view_any'));
    }

    public function view(User $user, Model $model): bool
    {
        return $user->can($this->perm('view'));
    }

    public function create(User $user): bool
    {
        return $user->can($this->perm('create'));
    }

    public function update(User $user, Model $model): bool
    {
        return ! $this->isImmutable() && $user->can($this->perm('update')) && (! Framework::model_has_state($model) || $model->isDraft());
    }

    public function deleteAny(User $user): bool
    {
        return ! $this->isImmutable() && $user->can($this->perm('delete_any'));
    }

    public function delete(User $user, Model $model)
    {
        return ! $this->isImmutable() && $user->can($this->perm('delete')) && (! Framework::model_has_state($model) || $model->isDraft());
    }

    public function submit(User $user, Model $model): bool
    {
        return $user->can($this->perm('submit')) && Framework::model_has_state($model) && $model->isDraft();
    }

    public function cancel(User $user, Model $model): bool
    {
        return ! $this->isImmutable() && $user->can($this->perm('cancel')) && Framework::model_has_state($model) && $model->isSubmitted();
    }

    public function reverse(User $user, Model $model): bool
    {
        return $user->can($this->perm('reverse')) && Framework::model_has_state($model) && $model->isSubmitted();
    }

    public function restoreAny(User $user): bool
    {
        return false;
    }

    public function restore(User $user, Model $model): bool
    {
        return false;
    }

    public function forceDeleteAny(User $user)
    {
        return $user->can($this->perm('delete_any'));
    }

    public function forceDelete(User $user, Model $model)
    {
        return $user->can($this->perm('delete')) && (! Framework::model_has_state($model) || $model->isDraft());
    }

    public function isImmutable(): bool
    {
        $model = $this->getResourceClass()::getModel();

        return Framework::classHasTrait($model, Immutable::class);
    }

    public function perm(string $prefix): string
    {
        return "{$prefix}_{$this->getSuffix()}";
    }
}
