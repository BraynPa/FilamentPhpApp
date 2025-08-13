<?php

namespace App\Imports;

use App\Models\Calendar;
use App\Models\Timesheet;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class MyTimesheetImport implements ToCollection, WithHeadingRow
{
    /**
    * @param Collection $collection
    */
    public function collection(Collection $rows)
    {
        foreach ($rows as $row) 
        {
            $calendar_id = Calendar::where('name',$row['calendario'])->first()->id;
            Timesheet::create([
                'calendar_id' => $calendar_id,
                'user_id' => auth()->user()->id,
                'type' => $row['tipo'],
                'day_in' => $row['dia_de_entrada'],
                'day_out' => $row['dia_de_salida'],
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
        }
    }
}
