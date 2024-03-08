<?php

namespace Vanadi\Framework\Concerns\Model;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;
use Vanadi\Framework\Models\Team;

use function Vanadi\Framework\default_team;

trait HasTeam
{
    public final const TEAM_COLUMN_NAME = 'team_id';

    public static function bootHasTeam(): void
    {
        self::creating(function (Model | self $model) {
            $col = $model::TEAM_COLUMN_NAME;
            if (! $model->getAttribute($model::TEAM_COLUMN_NAME)) {
                if (auth()->check()) {
                    $model->{self::TEAM_COLUMN_NAME} = auth()->user()->{self::TEAM_COLUMN_NAME};
                } else {
                    $model->{self::TEAM_COLUMN_NAME} = default_team()?->getAttribute('id');
                }
            }
            /*if ($model->getAttribute('is_cross_team')) {
                $model->{$col} = null;
            }*/

        });

        // Add scope
        if (auth()->check()) {
            static::addGlobalScope('team', function (Builder $query) use ($model) {
                $model->makeTeamScope($query);
            });
        }
    }

    public function makeTeamScope(Builder $query): void
    {
        if (!config('vanadi.multitenancy', false)) {
            return;
        }
        if (in_array($query->getModel()->getMorphClass(), static::getSharedModels())) {
            return;
        }
        if (Schema::hasColumn($query->getModel()->getTable(), 'team_id')) {
            $user = auth()->user();
            if ($user) {
                $query->whereBelongsTo($user->team);
            } else {
                $query->whereNull('team_id');
            }
        }
    }

    public function team()
    {
        return $this->belongsTo(Team::class, static::TEAM_COLUMN_NAME);
    }

    protected function initializeHasTeam()
    {
        //        $this->casts['is_cross_team'] = 'bool';
    }

    protected static function getSharedModels()
    {
        return config('vanadi.shared_models', []);
    }
}
