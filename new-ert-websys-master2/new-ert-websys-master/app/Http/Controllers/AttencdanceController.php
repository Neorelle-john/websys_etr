<?php

namespace App\Http\Controllers;

use App\Exports\IndividualExport;
use App\Exports\UnifiedMonthlyExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\IndivualExport;
use App\Models\Attencdance;
use Illuminate\Http\Request;
use App\Models\Employee;
use Carbon\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;
class AttencdanceController extends Controller
{
    public function store(Request $request)
    {
        $idNumber = $request->input('id_number');
        $employee = Employee::where('id_number', $idNumber)->first();

        if (!$employee) {
            return response()->json(['message' => 'Employee not found'], 404);
        }

        $today = Carbon::today('Asia/Manila');
        $now = Carbon::now('Asia/Manila');
        $hour = $now->hour;

        $attendance = Attencdance::firstOrCreate([
            'employee_id' => $employee->id,
            'date' => $today
        ]);

        if ($hour >= 17 && !$attendance->am_time_in && !$attendance->am_time_out && !$attendance->pm_time_in && !$attendance->pm_time_out) {
            return response()->json(['message' => 'Attendance recording is closed for today.'], 400);
        }

        if ($hour < 12) {
            if (!$attendance->am_time_in) {
                $attendance->am_time_in = $now;
            } elseif (!$attendance->am_time_out) {
                $attendance->am_time_out = $now;
            }
        } else {
            if ($attendance->am_time_in && !$attendance->am_time_out) {
                $attendance->am_time_out = Carbon::createFromTime(12, 0, 0, 'Asia/Manila');
                $attendance->pm_time_in = Carbon::createFromTime(13, 0, 0, 'Asia/Manila');
            }

            if (!$attendance->pm_time_in) {
                $attendance->pm_time_in = $now;
            } elseif (!$attendance->pm_time_out) {
                $attendance->pm_time_out = $now;
            }
        }

        $attendance->save();

        return response()->json([
            'message' => 'Attendance saved successfully.',
            'employee' => [
                'name' => $employee->name,
                'id_number' => $employee->id_number,
                'classification' => $employee->classification,
                'college' => $employee->college,
                'picture_path' => asset('images/' . $employee->picture),
            ],
            'attendance' => [
                'am_time_in' => optional($attendance->am_time_in)->format('h:i A'),
                'am_time_out' => optional($attendance->am_time_out)->format('h:i A'),
                'pm_time_in' => optional($attendance->pm_time_in)->format('h:i A'),
                'pm_time_out' => optional($attendance->pm_time_out)->format('h:i A'),
            ]
        ]);
    }
public function viewMonthlyReport(Request $request)
{
    if(session('loggedUser')){
    $role = $request->input('role');
    $month = $request->input('month', now('Asia/Manila')->month); // Use current month by default

    $employees = Employee::when($role, function ($query, $role) {
        return $query->where('classification', $role);
    })->get();

    $attendanceData = [];

    foreach ($employees as $employee) {
        $attendances = Attencdance::where('employee_id', $employee->id)
            ->whereMonth('date', $month)
            ->get();

        $totalUndertimeMinutes = 0;
        $absentDates = [];

        foreach ($attendances as $attendance) {
            $totalUndertimeMinutes += $this->calculateUndertime($attendance);
            if ($this->calculateAbsence($attendance)) {
                $absentDates[] = \Carbon\Carbon::parse($attendance->date)->toDateString();
            }
        }

        $absenceRanges = $this->formatAbsenceDates($absentDates);

        $attendanceData[] = [
            'name' => $employee->name,
            'undertime' => $this->formatMinutesToHoursMinutes($totalUndertimeMinutes),
            'absences' => $absenceRanges
        ];

    }

    // ✅ Paginate data
    $page = $request->input('page', 1);
    $perPage = 10;
    $collection = collect($attendanceData);
    $paginatedRecords = new LengthAwarePaginator(
        $collection->forPage($page, $perPage),
        $collection->count(),
        $perPage,
        $page,
        ['path' => $request->url(), 'query' => $request->query()]
    );

    $monthName = Carbon::createFromDate(null, $month, 1)->format('F');

    return view('scan.dtr', [
        'records' => $paginatedRecords,
        'selectedMonth' => $month,
        'selectedMonthName' => $monthName
    ]);
     } else {
            return redirect()->route('login');
        }
}
 private function calculateUndertime($attendance)
{
    // ✅ Skip calculation for Saturdays and Sundays
    if (Carbon::parse($attendance->date)->isWeekend()) {
        return 0;
    }

    // ✅ If fully absent, no undertime should be counted
    if (
        !$attendance->am_time_in && !$attendance->am_time_out &&
        !$attendance->pm_time_in && !$attendance->pm_time_out
    ) {
        return 0;
    }

    $totalUndertime = 0;

    // AM session
    if ($attendance->am_time_in && $attendance->am_time_out) {
        $amStart = Carbon::parse($attendance->am_time_in);
        $amEnd = Carbon::parse($attendance->am_time_out);
        $workedMinutes = $amStart->diffInMinutes($amEnd);
        $undertime = max(0, 240 - $workedMinutes);
        $totalUndertime += $undertime;
    } elseif ($attendance->am_time_in || $attendance->am_time_out) {
        // Partial scan = full AM undertime
        $totalUndertime += 240;
    }

    // PM session
    if ($attendance->pm_time_in && $attendance->pm_time_out) {
        $pmStart = Carbon::parse($attendance->pm_time_in);
        $pmEnd = Carbon::parse($attendance->pm_time_out);
        $workedMinutes = $pmStart->diffInMinutes($pmEnd);
        $undertime = max(0, 240 - $workedMinutes);
        $totalUndertime += $undertime;
    } elseif ($attendance->pm_time_in || $attendance->pm_time_out) {
        // Partial scan = full PM undertime
        $totalUndertime += 240;
    }

    return $totalUndertime;
}
    private function calculateAbsence($attendance)
    {
        return !$attendance->am_time_in && !$attendance->am_time_out &&
               !$attendance->pm_time_in && !$attendance->pm_time_out;
    }

    private function formatMinutesToHoursMinutes($minutes)
{
    if ($minutes === 0) {
        return '';
    }

    $hours = floor($minutes / 60);
    $mins = $minutes % 60;

    $parts = [];
    if ($hours > 0) {
        $parts[] = "{$hours} hrs.";
    }
    if ($mins > 0) {
        $parts[] = "{$mins} mins";
    }

    return implode(' & ', $parts);
}

    private function formatAbsenceDates(array $dates)
    {
        if (empty($dates)) return null;

        // Group days by month and year
        $grouped = [];

        foreach ($dates as $dateStr) {
            $date = Carbon::parse($dateStr);
            $month = $date->format('F'); // e.g., May
            $year = $date->year;
            $key = "$month $year";
            $grouped[$key][] = $date->day;
        }

        $formatted = [];

        foreach ($grouped as $monthYear => $days) {
            sort($days);
            $daysList = implode(', ', $days);
            // Split back to insert year at the end
            [$month, $year] = explode(' ', $monthYear);
            $formatted[] = "$month $daysList, $year";
        }

        return implode('; ', $formatted);
    }
        private function formatRange($start, $end)
    {
        if ($start->equalTo($end)) {
            return $start->format('M. j, Y');
        } else {
            return $start->format('M. j') . '–' . $end->format('j, Y');
        }
    }

    public function dashboard(Request $request)
{
    // Get the selected month from the request, default to current month
    $selectedMonth = $request->input('month', now('Asia/Manila')->month);

    // Ensure the month is valid (1-12)
    $selectedMonth = max(1, min(12, (int)$selectedMonth));

    // Fetch counts for instructional and non-instructional employees
    $instructionalCount = Employee::where('classification', 'Instructional')->count();
    $nonInstructionalCount = Employee::where('classification', 'Non-Instructional')->count();

    // Fetch all employees
    $employees = Employee::all();
    $undertimeData = [];

    foreach ($employees as $employee) {
        $attendances = Attencdance::where('employee_id', $employee->id)
            ->whereMonth('date', $selectedMonth)
            ->get();

        $totalUndertimeMinutes = 0;
        foreach ($attendances as $attendance) {
            $totalUndertimeMinutes += $this->calculateUndertime($attendance);
        }

        if ($totalUndertimeMinutes > 0) {
            $undertimeData[] = [
                'id' => $employee->id, // Added for routing
                'name' => $employee->name,
                'minutes' => $totalUndertimeMinutes
            ];
        }
    }

    // Sort descending and take top 5
    $topUndertime = collect($undertimeData)
        ->sortByDesc('minutes')
        ->take(5)
        ->values()
        ->all();

    return view('scan.dashboard', [
        'instructionalCount' => $instructionalCount,
        'nonInstructionalCount' => $nonInstructionalCount,
        'topUndertime' => $topUndertime,
        'selectedMonth' => $selectedMonth
    ]);
}

    public function downloadGroupedSheet(Request $request)
{
    $month = $request->input('month', now('Asia/Manila')->month);
    return Excel::download(new UnifiedMonthlyExport($month), 'Monthly_Attendance.xlsx');
}

public function showIndividualForm()
{
    $employees = Employee::all();
    return view('attendance.export-form', compact('employees'));
}
  public function showExportForm()
    {
        $employees = Employee::all();
        return view('attendance.export', compact('employees'));
    }

    public function exportIndividual(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'month' => 'required|integer|min:1|max:12',

        ]);

        return Excel::download(
            new IndividualExport($request->employee_id, $request->month, $request->year)
           , 'monthly-report.xlsx');
    }
}

