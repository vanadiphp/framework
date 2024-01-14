<?php

namespace Vanadi\Framework\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Vanadi\Framework\Concerns\Model\HasAuditColumns;
use Vanadi\Framework\Concerns\Model\HasCode;
use Vanadi\Framework\Concerns\Model\HasState;
use Vanadi\Framework\Concerns\Model\HasTeam;

class Country extends Model
{
    use HasAuditColumns;
    use HasCode;
    use HasState;
    use HasTeam;

    protected $guarded = ['id'];

    public function getCodePrefix(): string
    {
        return 'COUNTRY';
    }

    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class, 'currency_code', 'code');
    }

    public function getFlagAttribute(): string
    {
        return file_get_contents($this->flag_svg_path) ?: '';
    }

    public function getFlagUrlAttribute(): string
    {
        return route('countries.code.flag', ['code' => $this->code]);
    }
}
