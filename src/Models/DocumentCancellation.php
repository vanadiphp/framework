<?php

namespace Vanadi\Framework\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Vanadi\Framework\Concerns\Model\HasAuditColumns;
use Vanadi\Framework\Concerns\Model\HasCode;
use Vanadi\Framework\Concerns\Model\HasState;
use Vanadi\Framework\Concerns\Model\HasTeam;

class DocumentCancellation extends Model
{
    use HasAuditColumns;
    use HasCode;
    use HasState;
    use HasTeam;
    use HasUuids;

    protected $guarded = ['id'];

    public function getCodePrefix(): string
    {
        return 'DCL/' . now()->isoFormat('YYYY/MM/DD/');
    }

    public function document(): MorphTo
    {
        return $this->morphTo('document');
    }
}
