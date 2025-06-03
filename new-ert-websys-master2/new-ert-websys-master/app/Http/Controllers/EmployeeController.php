<?php

namespace App\Http\Controllers;
use Barryvdh\DomPDF\Facade\Pdf;
use Mdpf\Mdpf;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
class EmployeeController extends Controller
{
    /**
     * Display a listing of the resource.
     */

     public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        session()->flush();
        return redirect('/login');
    }
    public function index(Request $request)
    {
        if(session('loggedUser')){
        $query = Employee::query();

        if ($request->filled('classification')) {
            $query->where('classification', $request->classification);
        }

        if ($request->filled('college')) {
            $query->where('college', $request->college);
        }

        $employees = $query->paginate(10)->appends($request->query());

        // Get unique classifications and colleges for filter dropdowns
        $classifications = Employee::select('classification')->distinct()->pluck('classification');
        $colleges = Employee::select('college')->distinct()->pluck('college');

        return view('employees.index', compact('employees', 'classifications', 'colleges'));
        }
        else {
            return redirect()->route('login');
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
        return view('employees.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
{
    $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|email|unique:staff,email',
        'address' => 'required|string',
        'picture' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
    ]);

    $staff = new Employee();
    $staff->name = $request->name;
    $staff->email = $request->email;
    $staff->address = $request->address;

    if ($request->hasFile('picture')) {
        $path = $request->file('picture')->store('employee_pictures', 'public');
        $staff->picture = $path;
    }

    $staff->save();

    return redirect()->route('staff.index')->with('success', 'Staff created successfully.');
}


    /**
     * Display the specified resource.
     */
    public function show(Employee $employee)
    {
        $qr = QrCode::size(200)->generate(json_encode([
            'id' => $employee->id,
            'name' => $employee->name,
            'id_number' => $employee->id_number,
            'college' => $employee->college,
            'classification' => $employee->classification,
            'picture' => $employee->picture,
        ]));

        return view('employees.show', compact('employee', 'qr'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Employee $employee)
    {
        return view('employees.edit', compact('employee'));
    }

    /**
     * Update the specified resource in storage.
     */
   public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'college' => 'required|string',
            'classification' => 'required|string',
            'picture' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',

        ]);

        $employee = Employee::findOrFail($id);

        $oldImagePath = $employee->picture;

        if ($request->hasFile('picture')) {
            // Delete old image if it exists
            if ($oldImagePath && file_exists(public_path('images/' . $oldImagePath))) {
                unlink(public_path('images/' . $oldImagePath));
            }



            $file = $request->file('picture');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('images'), $filename);

            // ✅ Save new path
            $filepath = $filename;
        } else {
            // ✅ Keep old path
            $filepath = $oldImagePath;
        }

        $employee->update([
            'name' => $request->name,
            'college' => $request->college,
            'classification' => $request->classification,
            'picture' => $filepath,
        ]);

        return redirect()
            ->route('employees.index')
            ->with('success', 'Employee data updated!');
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Employee $employee)
    {
        if ($employee->picture && Storage::exists('public/' . $employee->picture)) {
            // Delete the image from storage
            Storage::delete('public/' . $employee->picture);
        }
        $employee->delete();

        return redirect()->route('employees.index')->with('success', 'Employee data deleted!');
    }


public function downloadQrCode($id)
{
    $employee = Employee::findOrFail($id);

    $qrCode = QrCode::format('png') // Make sure it's a PNG
        ->size(200)
        ->generate(json_encode([
            'name' => $employee->name,
            'id_number' => $employee->id_number,
            'college' => $employee->college,
            'classification' => $employee->classification,
            'picture' => $employee->picture,
        ]));

    $fileName = 'qr_code_' . $employee->name . '.png';

    return response($qrCode)
        ->header('Content-Type', 'image/png')
        ->header('Content-Disposition', 'attachment; filename="' . $fileName . '"');
}
//pdf
// public function downloadQrCodePdf($id)
// {
//     $employee = Employee::findOrFail($id);

//     $qrCode = base64_encode(
//         QrCode::format('png')->size(200)->generate(json_encode([
//             'name' => $employee->name,
//             'id_number' => $employee->id_number,
//             'college' => $employee->college,
//             'classification' => $employee->classification,
//             'picture' => $employee->picture,
//         ]))
//     );

//     $pdf = Pdf::loadView('pdf.qr_code', [
//         'employee' => $employee,
//         'qrCode' => $qrCode,
//     ]);

//     $fileName = 'qr_code_' . $employee->id_number . '.pdf';
//     return $pdf->download($fileName);
// }

// public function downloadQr($id)
// {
//     $mpdf = new \Mpdf\Mpdf();
//     $qrCode = base64_encode(QrCode::format('pdf')->size(200)->generate(...));
//     $html = '<h2>' . $employee->name . '</h2><img src="data:image/png;base64,' . $qrCode . '">';
//     $mpdf->WriteHTML($html);
//     $mpdf->Output('qr_code.pdf', 'D');
// }

// public function downloadQrCode($id)
// {
//     $employee = Employee::findOrFail($id);

//     $qr = base64_encode(QrCode::format('png')->size(200)->generate($employee->id_number));

//     $pdf = Pdf::loadView('pdf.qr', ['employee' => $employee, 'qr' => $qr]);

//     return $pdf->download('qr-code.pdf');
// }
}
