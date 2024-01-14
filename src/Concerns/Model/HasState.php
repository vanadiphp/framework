<?php

namespace Vanadi\Framework\Concerns\Model;

use Carbon\Carbon;
use DB;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use RuntimeException;
use Vanadi\Framework\Contracts\State;
use Vanadi\Framework\Models\DocumentCancellation;

/**
 * Trait HasState
 *
 * @mixin Model
 *
 * @property string $state
 * @property bool $is_active
 * @property Carbon $submitted_at
 * @property Carbon $cancelled_at
 */
trait HasState
{
    public static function bootHasState(): void
    {
        static::addGlobalScope('not-cancelled', function (Builder $builder) {
            $builder->where('state', '!=', State::CANCELLED);
        });
        static::creating(callback: function (Model | self $model) {
            if (! $model->getAttribute('state')) {
                $model->state = State::DRAFT;
            }
        });
        static::updating(function (Model | self $model) {
            if (! $model->isDraft()) {
                throw new RuntimeException('You can only update documents which are in draft mode.');
            }
        });

        static::deleting(function (Model | self $model) {
            if (! $model->isDraft()) {
                throw new RuntimeException('You can only delete documents which are in draft mode.');
            }
        });
    }

    protected function initializeHasState(): void
    {
        $this->casts['is_active'] = 'bool';
    }

    public function scopeWhereDraft(Builder $builder): Builder
    {
        return $builder->where('state', '=', State::DRAFT);
    }

    public function scopeWhereSubmitted(Builder $builder): Builder
    {
        return $builder->where('state', '=', State::SUBMITTED);
    }

    public function scopeWhereCancelled(Builder $builder): Builder
    {
        return $builder->where('state', '=', State::CANCELLED)
            ->orWhere('is_cancelled', '=', true);
    }

    public function scopeWithCancelled(Builder $builder): Builder
    {
        return $builder->withoutGlobalScope('not-cancelled');
    }

    public function scopeOnlyCancelled(Builder $builder): Builder
    {
        return $builder->withoutGlobalScope('not-cancelled')->whereCancelled();
    }

    public function isDraft(): bool
    {
        return $this->state === State::DRAFT;
    }

    public function isSubmitted(): bool
    {
        return $this->state === State::SUBMITTED;
    }

    public function isCancelled(): bool
    {
        return $this->state === State::CANCELLED;
    }

    public function isNotCancelled(): bool
    {
        return ! $this->isCancelled();
    }

    public function submit($onlyIfDraft = true): static
    {
        if ($onlyIfDraft && ! $this->isDraft()) {
            return $this;
        }
        abort_unless($this->isDraft(), 403, 'Only Draft Documents can be Submitted.');
        $this->submitting();
        $this->state = State::SUBMITTED;
        $this->submitted_at = now();
        $this->saveQuietly();
        $this->submitted();

        return $this;
    }

    public function cancel(?string $reason = ''): static
    {
        abort_unless($this->isSubmitted(), 403, 'Only Submitted Documents can be Cancelled.');
        DB::transaction(function () use ($reason) {
            $this->canceling($reason);
            $this->state = State::CANCELLED;
            $this->cancelled_at = now();
            $this->saveQuietly();
            // Create a Doc Cancellation log:
            $log = DocumentCancellation::create([
                'reason' => $reason,
                'document_code' => $this->code,
                'document_type' => $this->getMorphClass(),
                'document_id' => $this->id,
            ]);
            $log->submit();
            $this->cancelled($reason);
        });

        return $this;
    }

    public function submitting()
    {
        // Hook your logic here
    }

    public function submitted()
    {
        // Hook your logic here
    }

    public function canceling(?string $reason = '')
    {
        // Hook your logic here
    }

    public function cancelled(?string $reason = '')
    {
        // Hook your logic here.
    }
}
