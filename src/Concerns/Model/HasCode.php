<?php

namespace Vanadi\Framework\Concerns\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

use function Vanadi\Framework\framework;

/**
 * Trait HasCode
 *
 * @mixin Model
 *
 * @property-read string $calculated_code
 * @property-read string $code
 * @property-read string $code_prefix
 */
trait HasCode
{
    public static string $code_prefix = '';

    const CODE_COLUMN_NAME = 'code';

    public function getCodePrefix(): string
    {
        if (empty(static::$code_prefix)) {
            static::$code_prefix = framework()->abbreviateClassName(static::class);
        }

        return static::$code_prefix;
    }

    public function getCodePadLength(): int
    {
        return 3;
    }

    public function getCodePadString(): string
    {
        return '0';
    }

    public function shouldOmitPrefix(): bool
    {
        return false; // Override this to change
    }

    public static function bootHasCode(): void
    {
        static::creating(function (Model $model) {
            if (! $model->getAttribute(static::CODE_COLUMN_NAME)) {
                $uid = uniqid('tmp_');
                $model->{static::CODE_COLUMN_NAME} = $uid;
            }
        });
        static::created(function (Model $model) {
            if (Str::of($model->getAttribute(static::CODE_COLUMN_NAME))->startsWith('tmp_')) {
                $model = $model::withoutGlobalScopes()->where('id', '=', $model->getAttribute('id'))->firstOrFail();
                $model->updateQuietly([static::CODE_COLUMN_NAME => $model->calculated_code]);
            }
        });
    }

    public function getCalculatedCodeAttribute(): string
    {
        $code = Str::of($this->getAttribute('id'))
            ->padLeft(
                length: $this->getCodePadLength() ?? 3,
                pad: $this->getCodePadString() ?: '0'
            );
        if (! $this->shouldOmitPrefix()) {
            $code = $code->prepend($this->getCodePrefix())->upper();
        }

        return $code->toString();
    }
}
