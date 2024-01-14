<?php

namespace Vanadi\Framework\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Vanadi\Framework\Concerns\Model\HasAuditColumns;
use Vanadi\Framework\Concerns\Model\HasCode;
use Vanadi\Framework\Concerns\Model\HasState;
use Vanadi\Framework\Concerns\Model\HasTeam;

class Currency extends Model
{
    use HasAuditColumns;
    use HasCode;
    use HasState;
    use HasTeam;

    protected $guarded = ['id'];

    public function getCodePrefix(): string
    {
        return 'CURR';
    }

    public function country(): HasOne
    {
        return $this->hasOne(Country::class, 'currency_code', 'code');
    }
}
