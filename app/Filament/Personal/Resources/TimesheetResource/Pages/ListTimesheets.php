<?php

namespace App\Filament\Personal\Resources\TimesheetResource\Pages;

use App\Filament\Personal\Resources\TimesheetResource;
use App\Imports\MyTimesheetImport;
use App\Models\Timesheet;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use EightyNine\ExcelImport\ExcelImportAction;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;

class ListTimesheets extends ListRecords
{
    protected static string $resource = TimesheetResource::class;

    protected function getHeaderActions(): array
    {
        $lastTimesheet = Timesheet::where('user_id', auth()->user()->id)->latest('id')->first();
        if($lastTimesheet == null){
            return [
            Action::make('inWork')
                ->label('Marcar entrada')
                ->color('primary')
                ->requiresConfirmation()
                ->action(function ()
                    {
                        $user = auth()->user();
                        $timesheet = new Timesheet();
                        $timesheet->calendar_id = 1;
                        $timesheet->user_id = $user->id;
                        $timesheet->day_in = Carbon::now();
                        $timesheet->type = "work";
                        $timesheet->save();
                        Notification::make()
                            ->title('Entrada registrada')
                            ->color('primary')
                            ->body('Has empezado a las ' . Carbon::now()->format('H:i:s'))
                            ->success()
                            ->send();
                    }
                ),
            ];
        }
        return [
            Action::make('inWork')
                ->label('Marcar entrada')
                ->color('primary')
                ->visible($lastTimesheet->day_out != null)
                ->requiresConfirmation()
                ->action(function ()
                    {
                        $user = auth()->user();
                        $timesheet = new Timesheet();
                        $timesheet->calendar_id = 1;
                        $timesheet->user_id = $user->id;
                        $timesheet->day_in = Carbon::now();
                        $timesheet->type = "work";
                        $timesheet->save();
                        Notification::make()
                            ->title('Entrada registrada')
                            ->color('primary')
                            ->body('Has empezado a las ' . Carbon::now()->format('H:i:s'))
                            ->success()
                            ->send();
                    }
                ),
            Action::make('stopWork')
                ->label('Parar de trabajar')
                ->visible($lastTimesheet->day_out == null && $lastTimesheet->type != 'pause')
                ->color('primary')
                ->requiresConfirmation()
                ->action(function() use($lastTimesheet){
                    $lastTimesheet->day_out = Carbon::now();
                    $lastTimesheet->save();
                    Notification::make()
                        ->title('Jornada finalizada')
                        ->body('Sales de trabajar a las ' . Carbon::now()->format('H:i:s'))
                        ->color('primary')
                        ->success()
                        ->send();
                    }
                ),
            Action::make('inPause')
                ->label('Comenzar pausa')
                ->color('info')
                ->visible($lastTimesheet->day_out == null && $lastTimesheet->type != 'pause')
                ->requiresConfirmation()
                ->action(function() use($lastTimesheet){
                    $lastTimesheet->day_out = Carbon::now();
                    $lastTimesheet->save();
                    $timesheet = new Timesheet();
                    $timesheet->calendar_id = 1;
                    $timesheet->user_id = auth()->user()->id;
                    $timesheet->day_in = Carbon::now();
                    $timesheet->type = "pause";
                    $timesheet->save();

                    Notification::make()
                        ->title('Pausa iniciada')
                        ->color('info')
                        ->success()
                        ->send();
                }),
            Action::make('stopPause')
                ->label('Parar pausa')
                ->color('info')
                ->visible($lastTimesheet->day_out == null && $lastTimesheet->type == 'pause')
                ->requiresConfirmation()
                ->action(function() use($lastTimesheet){
                    $lastTimesheet->day_out = Carbon::now();
                    $lastTimesheet->save();
                    $timesheet = new Timesheet();
                    $timesheet->calendar_id = 1;
                    $timesheet->user_id = auth()->user()->id;
                    $timesheet->day_in = Carbon::now();
                    $timesheet->type = "work";
                    $timesheet->save();
                    Notification::make()
                        ->title('Pausa finalizada')
                        ->color('info')
                        ->success()
                        ->send();
                }),
            ExcelImportAction::make()
                ->iconButton()
                ->tooltip('Importar Timesheets Excel')
                ->icon('fas-file-import')
                ->color("primary")
                ->use(MyTimesheetImport::class),
            Action::make('download_pdf')
                ->iconButton()
                ->tooltip('Descargar PDF')
                ->icon('fas-file-pdf')
                ->requiresConfirmation()
                ->modalIcon('fas-file-pdf')
                ->modalHeading('Download pdf')
                ->modalDescription('Are you sure you\'d like to download this?')
                ->action(function () {
                    $data = [
                        'timesheets' => Timesheet::where('user_id',auth()->user()->id)->get(),
                    ];

                    $pdf = Pdf::loadView('pdf.timesheet.invoice', $data);

                    return response()->streamDownload(function () use ($pdf) {
                        echo $pdf->output();
                    }, 'timesheets.pdf');
            }),
        ];
    }
}
