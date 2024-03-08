<?php

namespace Vanadi\Framework\Helpers;

use Filament\Support\Colors\Color;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\UrlGenerator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use LaravelReady\ReadableNumbers\Facades\ReadableNumbers;
use ReflectionClass;
use Vanadi\Framework\Concerns\Model\HasState;
use Vanadi\Framework\Themes\StrathmoreTheme;

class Framework
{
    public static function url(string $path, ?array $parameters = []): \Illuminate\Foundation\Application | string | UrlGenerator | Application
    {
        return url($path, $parameters, secure: config('vanadi.app_scheme', 'https') === 'https');
    }

    public function abbreviateClassName(string $class): string
    {
        $class = Str::of($class)->explode('\\')->last();
        $prefix = Str::of($class)->replace('Model', '')->snake()->upper();

        // pick only the first letter of each word
        return Str::of($prefix)->explode('_')->map(fn ($word) => Str::of($word)->substr(0, 1))->implode('');
    }

    public static function get_models($scanPath, string $namespace = 'App\\'): Collection
    {
        $models = collect(File::allFiles($scanPath))
            ->map(function ($item) use ($namespace) {
                $path = $item->getRelativePathName();
                $class = sprintf(
                    '\%s%s',
                    $namespace,
                    strtr(substr($path, 0, strrpos($path, '.')), '/', '\\')
                );

                return $class;
            })
            ->filter(function ($class) {
                $valid = false;

                if (class_exists($class)) {
                    $reflection = new ReflectionClass($class);
                    $valid = $reflection->isSubclassOf(Model::class) &&
                        ! $reflection->isAbstract();
                }

                return $valid;
            });

        return $models->values();
    }

    public static function classHasTrait(mixed $class, string $trait): bool
    {
        $traits = class_uses_recursive($class);

        return in_array($trait, $traits);
    }

    /**
     * @deprecated Use model_has_state instead
     */
    public static function model_has_doc_status(Model $model): bool
    {
        return static::model_has_state($model);
    }

    public static function model_has_state(Model $model): bool
    {
        return static::classHasTrait($model, HasState::class);
    }

    public static function human_readable(float | int $number, int $decimals = 2, ?string $locale = null): string
    {
        if (! $locale) {
            $locale = app()->getLocale();
        }

        return ReadableNumbers::make($number, $decimals, $locale);
    }

    public function calculate(string $mathExpression, array $variables = []): mixed
    {
        $expression = $this->substitute($mathExpression, $variables);
        $math = "return $expression;";

        return eval($math);
    }

    public function substitute(string $expression, array $substitutions = [], string $substitutionIdentifier = ':'): string
    {
        $keys = collect($substitutions)->keys()->map(fn ($key) => "{$substitutionIdentifier}{$key}");
        $values = collect($substitutions)->values();

        $replacement = Str::of($expression)->replace($keys->toArray(), $values->toArray())->toString();
        // ROUND 2
        if (Str::of($replacement)->contains($keys)) {
            $replacement = Str::of($replacement)->replace($keys->toArray(), $values->toArray())->toString();
        }

        // ROUND 3
        if (Str::of($replacement)->contains($keys)) {
            $replacement = Str::of($replacement)->replace($keys->toArray(), $values->toArray())->toString();
        }

        return $replacement;
    }

    public function getByCode(Model | string $model, string | array $code)
    {
        if (! Schema::hasColumn($model::query()->getModel()->getTable(), 'code')) {
            return null;
        }
        if (is_array($code)) {
            return $model::query()->whereIn('code', $code)->get();
        }

        return $model::query()->where('code', '=', trim($code))->first();
    }

    public function strToBool(mixed $string): bool
    {
        return in_array(
            strtolower(
                (string) $string
            ),
            ['y', 't', '1', 'yes', 'true', 'on', 'active', 'activated', 'enabled']
        );
    }

    public function tailwind_palette(string | array $color): array
    {
        if (is_array($color)) {
            return $color;
        } // Already a palette

        return Str::of($color)->contains('#') ? Color::hex($color) : Color::rgb($color);
    }

    public function rgba_primary($level = 500, $alpha = 1.0): string
    {
        $primary = $this->tailwind_palette(StrathmoreTheme::PRIMARY_COLOR)[$level];

        return "rgba($primary,$alpha)";
    }

    public function rgba_info($level = 500, $alpha = 1.0): string
    {
        $color = $this->tailwind_palette(StrathmoreTheme::INFO_COLOR)[$level];

        return "rgba($color,$alpha)";
    }

    public function rgba_danger($level = 500, $alpha = 1.0): string
    {
        $color = $this->tailwind_palette(StrathmoreTheme::DANGER_COLOR)[$level];

        return "rgba($color,$alpha)";
    }

    public function rgba_success($level = 500, $alpha = 1.0): string
    {
        $color = $this->tailwind_palette(StrathmoreTheme::SUCCESS_COLOR)[$level];

        return "rgba($color,$alpha)";
    }

    public function append_after_line(string $file, string $reference_line, string $append_line, bool $skip_if_exists = false): string
    {
        $search = $reference_line;
        $replace = "$reference_line\n$append_line";
        if ($skip_if_exists && Str::of(file_get_contents($file))->contains($append_line)) {
            return file_get_contents($file);
        }
        $contents = file_get_contents($file);
        $contents = Str::of($contents)->replace($search, $replace)->toString();
        file_put_contents($file, $contents);

        return $contents;
    }
}
