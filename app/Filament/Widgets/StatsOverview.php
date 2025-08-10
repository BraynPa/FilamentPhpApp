<?php

namespace App\Filament\Widgets;

use App\Models\Holiday;
use App\Models\Timesheet;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;

class StatsOverview extends BaseWidget
{
    use HasWidgetShield;
    protected function getStats(): array
    {
        $totalEmployees = User::all()->count();
        $totalHoliday = Holiday::where('type','=', 'pending')->count();
        $totalTimesheets = Timesheet::all()->count();
        return [
            Stat::make('Employees', $totalEmployees),
            Stat::make('Pending Holiday', $totalHoliday),
            Stat::make('Timesheets', $totalTimesheets),
        ];
    }
}
