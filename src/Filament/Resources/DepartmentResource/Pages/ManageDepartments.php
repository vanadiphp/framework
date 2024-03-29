<?php

namespace Vanadi\Framework\Filament\Resources\DepartmentResource\Pages;

use Coolsam\FilamentExcel\Actions\ImportField;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ManageRecords;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use pxlrbt\FilamentExcel\Columns\Column;
use Vanadi\Framework\Concerns\Filament\HasExportActions;
use Vanadi\Framework\Concerns\Filament\HasVanadiImports;
use Vanadi\Framework\Filament\Resources\DepartmentResource;
use Vanadi\Framework\Models\Department;

use function Vanadi\Framework\framework;

class ManageDepartments extends ManageRecords
{
    use HasExportActions;
    use HasVanadiImports;

    protected static string $resource = DepartmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            $this->getPageTableExportAction(),
            $this->makeHeaderImportAction()->label('Import KFS Linking Data'),
            Actions\Action::make('synchronize')->label('Sync from PnC')
                ->requiresConfirmation()
                ->color('success')
                ->icon('heroicon-o-arrow-path-rounded-square')->action(fn () => $this->synchronize()),
            Actions\CreateAction::make(),
        ];
    }

    public function synchronize()
    {
        try {
            $records = framework()->synchronizeDepartments();
            Notification::make('success')
                ->title('Sync Successful')
                ->body('The synchronization has been completed successfully. ' . $records->count() . ' records synchronized.')
                ->success()
                ->send();
        } catch (\Throwable $exception) {
            Notification::make('error')
                ->body($exception->getMessage() ?: 'Http Error')
                ->title('Synchronization Error')
                ->danger()
                ->send();
        }
    }

    public static function getExportColumns(): array
    {
        return [
            Column::make('code'),
            Column::make('short_name'),
            Column::make('name'),
            Column::make('account_number'),
            Column::make('revenue_account_number'),
            Column::make('object_code'),
            Column::make('revenue_object_code'),

        ];
    }

    public function getImportColumns(): array
    {
        return [
            ImportField::make('code')->required(),
            ImportField::make('short_name')->required(),
            ImportField::make('account_number')->required(),
            ImportField::make('object_code')->required(),
            ImportField::make('chart_code')->required(),
            ImportField::make('revenue_object_code')->required(),
            ImportField::make('revenue_chart_code')->required(),

        ];
    }

    public function importRecord(array $data)
    {
        $data = collect($this->mutateFieldsForRecordCreation($data));

        try {
            /**
             * @var Department $model
             */
            $model = static::getModel()::whereCode($data['code'])->orWhere('short_name', $data['short_name'])->first();
            if ($model) {
                $model->update([
                    'chart_code' => Str::of($data['chart_code'])->toString(),
                    'object_code' => ($chartCode = $data['object_code']) ? Str::of($chartCode)->padLeft(4, '0') : $chartCode,
                    'account_number' => ($acNo = $data['account_number']) ? Str::of($acNo)->padLeft(7, '0') : $acNo,
                ]);
            }

            return $model;
        } catch (\Throwable $exception) {
            Log::error($exception);

            throw $exception;
        }
    }
}
