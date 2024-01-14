<?php

namespace Vanadi\Framework\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Vanadi\Framework\Concerns\Model\HasAuditColumns;
use Vanadi\Framework\Concerns\Model\HasCode;
use Vanadi\Framework\Concerns\Model\HasState;
use Vanadi\Framework\Concerns\Model\HasTeam;
use Vanadi\Framework\Models\Country;

class Currency extends Model
{
    use HasAuditColumns, HasCode, HasState, HasTeam;
    protected $guarded = ['id'];
    public function getCodePrefix(): string
    {
        return 'CURR';
    }

    public function country(): HasOne
    {
        return $this->hasOne(Country::class,'currency_code','code');
    }
}
