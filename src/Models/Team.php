<?php

namespace Vanadi\Framework\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Kalnoy\Nestedset\NodeTrait;
use Vanadi\Framework\Concerns\Model\HasAuditColumns;
use Vanadi\Framework\Concerns\Model\HasCode;
use Vanadi\Framework\Concerns\Model\HasState;

class Team extends Model
{
    use HasAuditColumns;
    use HasCode;
    use HasState;
    use NodeTrait;

    protected $guarded = ['id'];

    public function getCodePrefix(): string
    {
        return 'TEAM';
    }

    public function shouldOmitPrefix(): bool
    {
        return false;
    }
    public function members(): BelongsToMany
    {
        return $this->belongsToMany(User::class,'team_user');
    }
}
