<?php

namespace Vanadi\Framework\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Kalnoy\Nestedset\NodeTrait;
use Vanadi\Framework\Concerns\Model\HasAuditColumns;
use Vanadi\Framework\Concerns\Model\HasCode;
use Vanadi\Framework\Concerns\Model\HasState;
use Vanadi\Framework\Concerns\Model\HasTeam;

class Department extends Model
{
    use HasAuditColumns;
    use HasCode;
    use HasState;
    use HasTeam;
    use NodeTrait;

    protected $guarded = ['id'];

    public function shouldOmitPrefix(): bool
    {
        return false;
    }

    public function users(): HasMany
    {
        return $this->hasMany("App\Models\User", 'department_short_name', 'short_name');
    }
}
