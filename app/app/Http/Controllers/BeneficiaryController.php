<?php

namespace App\Http\Controllers;

use App\Imports\BeneficiariesImport;
use App\Models\Beneficiary;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class BeneficiaryController extends Controller
{
    public function index(Request $request)
    {
        $query = $request->input('search');

        $beneficiaries = Beneficiary::query()
            ->when($query, function ($q) use ($query) {
                $q->where(function ($sub) use ($query) {
                    $sub->where('name', 'like', "%{$query}%")
                        ->orWhere('id_number', 'like', "%{$query}%")
                        ->orWhere('email', 'like', "%{$query}%")
                        ->orWhere('birthdate', 'like', "%{$query}%");
                });
            })
            ->paginate(15)
            ->appends(['search' => $query]);

        return view('beneficiaries.index', compact('beneficiaries', 'query'));
    }

    public function import(Request $request)
    {
        // Increase memory limit for large file processing
        ini_set('memory_limit', '512M');
        
        $request->validate([
            'file' => ['required', 'file', 'mimes:xlsx,xls,csv'],
        ]);

        try {
            $file = $request->file('file');
            $import = new BeneficiariesImport();
            Excel::import($import, $file);

            $message = 'Successfully imported ' . $import->getImportedCount() . ' records.';
            if ($import->getSkippedCount() > 0) {
                $message .= ' Skipped ' . $import->getSkippedCount() . ' duplicates/invalid records.';
            }

            return back()->with('status', $message);
        } catch (\Throwable $e) {
            \Log::error('Import error: ' . $e->getMessage(), ['exception' => $e]);
            return back()->withErrors(['file' => 'Import failed: ' . $e->getMessage()]);
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string'],
            'birthdate' => ['nullable', 'string'],
        ]);

        $payload = $request->all();
        $payload['age'] = $payload['age'] ?? null;
        
        // Parse the birthdate to Y-m-d format
        if (!empty($payload['birthdate'])) {
            $payload['birthdate'] = $this->parseDate($payload['birthdate']);
        } else {
            $payload['birthdate'] = null;
        }
        
        $payload['employment_status'] = $payload['employment_status'] ?? null;
        $payload['student_status'] = $payload['student_status'] ?? null;
        $payload['pwd_status'] = $payload['pwd_status'] ?? null;
        $payload['eligibility_status'] = $payload['eligibility_status'] ?? null;

        Beneficiary::create($payload);

        return back()->with('status', 'Beneficiary added successfully.');
    }

    public function update(Request $request, Beneficiary $beneficiary)
    {
        $request->validate([
            'name' => ['required', 'string'],
        ]);

        $payload = $request->all();
        
        // Parse the birthdate to Y-m-d format
        if (!empty($payload['birthdate'])) {
            $payload['birthdate'] = $this->parseDate($payload['birthdate']);
        }

        $beneficiary->update($payload);

        return back()->with('status', 'Beneficiary updated successfully.');
    }

    protected function parseDate(?string $dateStr): ?string
    {
        if (empty($dateStr)) {
            return null;
        }

        $dateStr = trim((string) $dateStr);
        
        $formats = [
            'Y-m-d',
            'm/d/Y',
            'd/m/Y',
            'Y/m/d',
            'F d, Y',
            'F d,Y',
            'F j, Y',
            'F j,Y',
            'j M Y',
            'j-M-Y',
            'd-m-Y',
            'm-d-Y',
            'd/m/y',
            'm/d/y',
        ];

        foreach ($formats as $format) {
            try {
                $date = \DateTime::createFromFormat($format, $dateStr);
                if ($date !== false) {
                    return $date->format('Y-m-d');
                }
            } catch (\Throwable) {
                continue;
            }
        }

        // Last resort: strtotime
        try {
            $timestamp = strtotime($dateStr);
            if ($timestamp !== false && $timestamp > 0) {
                return date('Y-m-d', $timestamp);
            }
        } catch (\Throwable) {
            // ignore
        }

        return null;
    }

    public function destroy(Beneficiary $beneficiary)
    {
        $beneficiary->delete();

        return back()->with('status', 'Beneficiary deleted successfully.');
    }
}
