<?php

namespace App\Filament\Personal\Resources\HolidayResource\Pages;

use App\Filament\Personal\Resources\HolidayResource;
use App\Mail\HolidayPending;
use App\Models\User;
use Filament\Actions;
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
        return $data;
    }
}
