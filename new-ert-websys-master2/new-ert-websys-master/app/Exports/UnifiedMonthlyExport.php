<?php

namespace App\Exports;

use App\Models\Employee;
use App\Models\Attencdance; // Should be corrected to "Attendance"
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;

class UnifiedMonthlyExport implements FromCollection, WithTitle, WithStyles, WithColumnWidths, WithEvents
{
    protected $month;
    protected $year;

    public function __construct($month, $year = null)
    {
        $this->month = $month;
        $this->year = $year ?: now()->year;
    }

    public function collection()
    {
        $rows = collect();

        // Custom header rows
        $rows->push(['', '', '', 'Pangasinan State University']);
        $rows->push(['', '', '', 'Urdaneta City Campus']);
        $rows->push(['', '', '', 'MONTHLY REPORT ON SERVICE OF INSTRUCTIONAL AND NON-INSTRUCTIONAL PERSONNEL']);
        $rows->push(['', '', '', Carbon::createFromDate($this->year, $this->month, 1)->format('F Y')]);

        // Spacer row
        $rows->push([]);

        // Instructional Section header
        $rows->push(['INSTRUCTIONAL STAFF']);
        $rows->push(['#', 'Name', 'Undertime', 'Inclusive Dates of Absence']);
        $rows = $rows->merge($this->sectionData('Instructional'));

        // Spacer row
        $rows->push([]);

        // Non-Instructional Section header
        $rows->push(['NON-INSTRUCTIONAL STAFF']);
        $rows->push(['#', 'Name', 'Undertime', 'Inclusive Dates of Absence']);
        $rows = $rows->merge($this->sectionData('Non-Instructional'));

        return $rows;
    }

    private function sectionData($classification)
    {
        $data = collect();
        $employees = Employee::where('classification', $classification)->get();
        $counter = 1;

        foreach ($employees as $employee) {
            $attendances = Attencdance::where('employee_id', $employee->id)
                ->whereMonth('date', $this->month)
                ->get();

            $totalMinutes = 0;
            $absenceDates = [];

            foreach ($attendances as $attendance) {
                // Skip weekends
                if (Carbon::parse($attendance->date)->isWeekend()) {
                    continue;
                }

                // Absence logic: Mark as absent if no AM or PM times
                if (!$attendance->am_time_in && !$attendance->am_time_out &&
                    !$attendance->pm_time_in && !$attendance->pm_time_out) {
                    $absenceDates[] = Carbon::parse($attendance->date)->format('M j');
                    continue; // Skip undertime for absent days
                }

                // Calculate undertime
                $totalMinutes += $this->calculateUndertime($attendance);
            }

            $absenceString = $this->formatAbsenceDates($absenceDates);

            $data->push([
                $counter++,
                $employee->name,
                $this->formatMinutes($totalMinutes),
                $absenceString
            ]);
        }

        return $data;
    }

    private function calculateUndertime($attendance)
    {
        $totalUndertime = 0;

        // AM session
        if ($attendance->am_time_in && $attendance->am_time_out) {
            try {
                $amStart = Carbon::parse($attendance->am_time_in);
                $amEnd = Carbon::parse($attendance->am_time_out);
                $workedMinutes = $amStart->diffInMinutes($amEnd);
                $undertime = max(0, 240 - $workedMinutes); // 240 minutes = 4 hours
                $totalUndertime += $undertime;
            } catch (\Exception $e) {
                // Skip invalid timestamps
                Log::error("Invalid AM time for attendance ID {$attendance->id}: {$attendance->am_time_in} - {$attendance->am_time_out}");
            }
        } elseif ($attendance->am_time_in || $attendance->am_time_out) {
            // Partial scan = full AM undertime (240 minutes)
            $totalUndertime += 240;
        }

        // PM session
        if ($attendance->pm_time_in && $attendance->pm_time_out) {
            try {
                $pmStart = Carbon::parse($attendance->pm_time_in);
                $pmEnd = Carbon::parse($attendance->pm_time_out);
                $workedMinutes = $pmStart->diffInMinutes($pmEnd);
                $undertime = max(0, 240 - $workedMinutes); // 240 minutes = 4 hours
                $totalUndertime += $undertime;
            } catch (\Exception $e) {
                // Skip invalid timestamps
                Log::error("Invalid PM time for attendance ID {$attendance->id}: {$attendance->pm_time_in} - {$attendance->pm_time_out}");
            }
        } elseif ($attendance->pm_time_in || $attendance->pm_time_out) {
            // Partial scan = full PM undertime (240 minutes)
            $totalUndertime += 240;
        }

        return $totalUndertime;
    }

    private function formatMinutes($mins)
    {
        if ($mins === 0) {
            return '';
        }

        $hours = floor($mins / 60);
        $minutes = $mins % 60;

        if ($hours > 0 && $minutes > 0) {
            return "{$hours} hr" . ($hours > 1 ? 's' : '') . " & {$minutes} mins";
        } elseif ($hours > 0) {
            return "{$hours} hr" . ($hours > 1 ? 's' : '');
        } elseif ($minutes > 0) {
            return "{$minutes} mins";
        }

        return '';
    }

    private function formatAbsenceDates(array $dates)
    {
        if (empty($dates)) {
            return '';
        }

        $firstDateParts = explode(' ', $dates[0]);
        $month = $firstDateParts[0] ?? '';
        $year = $this->year;

        $days = array_map(function ($date) {
            $parts = explode(' ', $date);
            return $parts[1] ?? $date;
        }, $dates);

        $daysString = implode(', ', $days);

        return "{$month} {$daysString}, {$year}";
    }

    public function title(): string
    {
        return 'Monthly Attendance';
    }

    public function styles(Worksheet $sheet)
    {
        $highestRow = $sheet->getHighestRow();

        return [
            'A1:D4' => [
                'font' => ['bold' => true, 'size' => 14],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
            ],
            'A7:D' . $highestRow => [
                'borders' => [
                    'allBorders' => ['borderStyle' => Border::BORDER_THIN],
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_LEFT,
                    'wrapText' => true,
                ],
            ]
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 8,
            'B' => 35,
            'C' => 20,
            'D' => 50,
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
            $sheet = $event->sheet->getDelegate();
            $highestRow = $sheet->getHighestRow();
            $highestColumn = $sheet->getHighestColumn();

            // Set page setup: landscape orientation and fit-to-page
            $sheet->getPageSetup()
                ->setOrientation(PageSetup::ORIENTATION_LANDSCAPE)
                ->setFitToWidth(1)
                ->setFitToHeight(0);

            // Set print margins
            $sheet->getPageMargins()->setTop(0.75);
            $sheet->getPageMargins()->setBottom(0.75);
            $sheet->getPageMargins()->setLeft(0.7);
            $sheet->getPageMargins()->setRight(0.7);

            // Define the data range for borders (starting from row 8)
            $dataRange = "A8:{$highestColumn}{$highestRow}";

            // Apply borders to the data range
            $styleArray = [
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['argb' => 'FF000000'],
                    ],
                ],
            ];
            $sheet->getStyle($dataRange)->applyFromArray($styleArray);

            // Apply wrapText to column D dynamically
            $sheet->getStyle("D8:D{$highestRow}")->getAlignment()->setWrapText(true);
        },
        ];
    }
}