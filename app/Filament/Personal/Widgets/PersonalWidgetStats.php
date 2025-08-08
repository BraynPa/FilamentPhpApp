<?php

namespace App\Filament\Personal\Widgets;

use App\Models\Holiday;
use App\Models\Timesheet;
use App\Models\User;
use Carbon\CarbonInterval;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Carbon;
use PhpParser\Node\Stmt\Foreach_;

class PersonalWidgetStats extends BaseWidget
{
    
    protected function getStats(): array
    {
        return [
            Stat::make('Pending Holidays', $this->getPendingHolidays(auth()->user())),
            Stat::make('Aproved Holidays', $this->getAprovedHolidays(auth()->user())),
            Stat::make('Total work', $this->getTotalWork(auth()->user())),
        ];
    }
    protected function getPendingHolidays(User $user){
        $totalPendingHolidays = Holiday::where('user_id','=', $user->id)
            ->where('type','=','pending')->get()->count();
        return $totalPendingHolidays;
    }
    protected function getAprovedHolidays(User $user){
        $totalPendingHolidays = Holiday::where('user_id','=', $user->id)
            ->where('type','=','aproved')->get()->count();
        return $totalPendingHolidays;
    }
    protected function getTotalWork(User $user): string
    {
        $timesheets = Timesheet::where('user_id',$user->id)
            ->where('type','work')
            ->select('day_in','day_out')
            ->get();
        $sumSeconds = $timesheets->sum(function ($timesheet) {
            // Verificación de valores nulos para evitar errores
            if (empty($timesheet->day_in) || empty($timesheet->day_out)) {
                return 0;
            }
            return Carbon::parse($timesheet->day_in)
                ->diffInSeconds(Carbon::parse($timesheet->day_out));
        });
        // Formateo más robusto usando CarbonInterval
        return CarbonInterval::seconds($sumSeconds)->cascade()->format('%H:%I:%S');
    }
}
