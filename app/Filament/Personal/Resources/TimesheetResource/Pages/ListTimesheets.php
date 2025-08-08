<?php

namespace App\Filament\Personal\Resources\TimesheetResource\Pages;

use App\Filament\Personal\Resources\TimesheetResource;
use App\Models\Timesheet;
use Carbon\Carbon;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Resources\Pages\ListRecords;

class ListTimesheets extends ListRecords
{
    protected static string $resource = TimesheetResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('inWork')
                ->label('Marcar entrada')
                ->color('success')
                ->requiresConfirmation()
                ->action(function ()
                    {
                        $user = auth()->user();
                        $timesheet = new Timesheet();
                        $timesheet->calendar_id = 1;
                        $timesheet->user_id = $user->id;
                        $timesheet->day_in = Carbon::now();
                        $timesheet->day_out = Carbon::now();
                        $timesheet->type = "work";
                        $timesheet->save();
                    }
                ),
            Action::make('inPause')
                ->label('Comenzar pausa')
                ->color('info')
                ->requiresConfirmation(),
            Actions\CreateAction::make(),
        ];
    }
}
