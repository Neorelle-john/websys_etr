<?php

namespace App\Exports;

use App\Models\Employee;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;

class IndividualExport implements FromCollection, WithTitle, WithStyles, WithColumnWidths, WithEvents
{
    protected $employeeId;
    protected $month;
    protected $year;

    public function __construct($employeeId, $month, $year = null)
    {
        $this->employeeId = $employeeId;
        $this->month = $month;
        $this->year = $year ?? now()->year;
    }

    private function calculateUndertime($timeIn, $timeOut, $expectedTimeIn, $expectedTimeOut)
    {
        $actualTimeIn = Carbon::createFromFormat('h:i A', $timeIn);
        $actualTimeOut = Carbon::createFromFormat('h:i A', $timeOut);
        $expectedTimeIn = Carbon::createFromFormat('H:i', $expectedTimeIn);
        $expectedTimeOut = Carbon::createFromFormat('H:i', $expectedTimeOut);

        $undertimeMinutes = 0;

        // Check for late arrival
        if ($actualTimeIn->gt($expectedTimeIn)) {
            $undertimeMinutes += $actualTimeIn->diffInMinutes($expectedTimeIn);
        }

        // Check for early departure
        if ($actualTimeOut->lt($expectedTimeOut)) {
            $undertimeMinutes += $actualTimeOut->diffInMinutes($expectedTimeOut);
        }

        return $undertimeMinutes;
    }

    private function formatUndertime($minutes)
    {
        if ($minutes <= 0) {
            return '-';
        }
        $hours = floor($minutes / 60);
        $mins = $minutes % 60;
        return ($hours > 0 ? $hours . 'h ' : '') . ($mins > 0 ? $mins . 'm' : '');
    }

    public function collection()
    {
        $rows = collect();
        $employee = Employee::findOrFail($this->employeeId);
        $daysInMonth = Carbon::create($this->year, $this->month)->daysInMonth;

        // Headers
        $rows->push(['', '', '', 'Pangasinan State University']);
        $rows->push(['', '', '', 'Urdaneta City Campus']);
        $rows->push(['', '', '', 'MONTHLY ATTENDANCE REPORT']);
        $rows->push(['', '', '', 'Employee: ' . $employee->name]);
        $rows->push(['', '', '', 'Month: ' . Carbon::create($this->year, $this->month)->format('F Y')]);
        $rows->push([]);

        // Table headings
        $rows->push(['#', 'Day', 'AM Time In', 'AM Time Out', 'PM Time In', 'PM Time Out', 'Status', 'Undertime']);

        // Generate data for weekdays only
        $counter = 1;
        for ($day = 1; $day <= $daysInMonth; $day++) {
            $date = Carbon::create($this->year, $this->month, $day);
            $dayName = $date->format('l');

            // Skip weekends
            if (in_array($dayName, ['Saturday', 'Sunday'])) {
                continue;
            }

            // Randomly determine if the employee is present or absent (80% present, 20% absent)
            $isPresent = (rand(1, 100) <= 80);

            if ($isPresent) {
                // Generate time strings for present employees
                $amIn = $this->randomTime('07:30', '08:30');
                $amOut = $this->randomTime('11:00', '11:59');
                $pmIn = $this->randomTime('12:00', '12:45');
                $pmOut = $this->randomTime('17:00', '18:00');

                // Calculate undertime
                $amUndertime = $this->calculateUndertime($amIn, $amOut, '07:30', '12:00');
                $pmUndertime = $this->calculateUndertime($pmIn, $pmOut, '13:00', '17:00');
                $totalUndertime = $amUndertime + $pmUndertime;

                $rows->push([
                    $counter++,
                    $dayName . ' - ' . $date->format('F j'),
                    $amIn,
                    $amOut,
                    $pmIn,
                    $pmOut,
                    'Present',
                    $this->formatUndertime($totalUndertime)
                ]);
            } else {
                // Push empty record for absent employees
                $rows->push([
                    $counter++,
                    $dayName . ' - ' . $date->format('F j'),
                    '',  // AM Time In
                    '',  // AM Time Out
                    '',  // PM Time In
                    '',  // PM Time Out
                    'Absent',
                    ''   // Undertime
                ]);
            }
        }

        return $rows;
    }

    private function randomTime($start, $end)
    {
        $startTime = Carbon::createFromFormat('H:i', $start);
        $endTime = Carbon::createFromFormat('H:i', $end);
        $randomMinutes = rand(0, $startTime->diffInMinutes($endTime));
        return $startTime->copy()->addMinutes($randomMinutes)->format('h:i A');
    }

    public function title(): string
    {
        return 'Employee Report';
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]],
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 5,
            'B' => 20,
            'C' => 15,
            'D' => 15,
            'E' => 15,
            'F' => 15,
            'G' => 12,
            'H' => 15,
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $sheet->getPageSetup()->setOrientation(PageSetup::ORIENTATION_LANDSCAPE);
            },
        ];
    }
}
