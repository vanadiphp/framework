<?php

namespace Vanadi\Framework\Concerns\Filament;

use Coolsam\FilamentExcel\Actions\ImportAction;
use Coolsam\FilamentExcel\Actions\ImportField;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

trait HasVanadiImports
{
    protected function makeHeaderImportAction(?\Closure $createRecordUsing = null)
    {
        $action = ImportAction::make('import')->fields($this->getImportFields())->color('success');
        if ($createRecordUsing) {
            $action->createRecordUsing($createRecordUsing);
        } else {
            $action->createRecordUsing(fn ($data) => $this->importRecord($data));
        }

        return $action;
    }

    /**
     * @deprecated use getImportFields() instead
     */
    /**
     * @return ImportField[]|array
     */
    abstract public function getImportColumns(): array;

    public function getImportFields(): array
    {
        return $this->getImportColumns();
    }

    public function enableUpserts(): bool
    {
        return true;
    }

    public function mutateFieldsForRecordCreation(array $data): array
    {
        return $data;
    }

    public function identifyForUpsertUsing(array $data): array
    {
        return ['code' => $data['code']];
    }

    public function importRecord(array $data)
    {
        $data = collect($this->mutateFieldsForRecordCreation($data));
        $identifiers = collect($this->identifyForUpsertUsing($data->toArray()));
        $args = $data->except($identifiers->keys()->toArray());
        Log::info('Upserting record using: ' . $identifiers->toJson());

        try {
            return $this->enableUpserts()
                ? static::getModel()::withoutGlobalScope('team')->updateOrCreate($identifiers->toArray(), $args->toArray())
                : static::getModel()::withoutGlobalScopes('team')->firstOrCreate($identifiers->toArray(), $args->toArray());
        } catch (\Throwable $exception) {
            Log::error($exception);

            throw $exception;
        }
    }

    public function updateOrCreate(Collection $identifiers, Collection $args)
    {
        $existing = static::getModel()::withoutGlobalScopes()->where([$identifiers->toArray()]);
        if ($existing) {
            $existing->update($args->toArray());

            return $existing;
        } else {
            return static::getModel()::create($args->merge($identifiers->toArray()));
        }
    }
}
