<?php

namespace App\Filament\Resources\FileResource\Pages;

use App\Filament\Resources\FileResource;
use App\Imports\FileImport;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\FileUpload;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Http;

class ListFiles extends ListRecords
{
    protected static string $resource = FileResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
            Action::make('import')
                ->label(__('attributes.import'))
                ->color('danger')
                ->form([
                    FileUpload::make('file')
                ])
                ->action(function (array $data) {
                    if (isset($data['file'])) {
                        Excel::import(new FileImport,  Storage::disk('public')->path($data['file']));
                    }
                })
        ];
    }
    /*     public function getHeader(): ?View
    {
        $data = CreateAction::make();
        return view('filament.custom.upload-file', compact('data'));
    } */
}
