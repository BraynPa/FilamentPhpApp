<?php

namespace App\Filament\Personal\Resources\HolidayResource\Pages;

use App\Filament\Personal\Resources\HolidayResource;
use App\Mail\HolidayPending;
use App\Models\User;
use App\Notifications\VacationRequestNotification;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Mail;

class CreateHoliday extends CreateRecord
{
    protected static string $resource = HolidayResource::class;
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = auth()->id();
        $data['type'] = 'pending';
        $userAdmin = User::find(1);
        $dataToSend = array(
            'day' => $data['day'],
            'name' => auth()->user()->name,
            'email' => auth()->user()->email,
        );
        Mail::to($userAdmin)->send(new HolidayPending($dataToSend ));
        /* Notification::make()
            ->title('solicitud de vacaciones')
            ->body('El dia '. $data['day'] . ' esta pendiente para aprobar.')
            ->warning()
            ->send(); */
        $recipient = auth()->user();

        $recipient->notify(Notification::make()
            ->title('Solicitud de vacaciones')
            ->body('El día ' . $data['day'] . ' está pendiente para aprobar.')
            ->warning()
            ->toDatabase(),
        );
        return $data;
    }
}
