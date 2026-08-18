<?php

namespace App\Http\Controllers;

use App\Models\BeneficiaryDuplicate;
use Illuminate\Http\Request;

class BeneficiaryDuplicateController extends Controller
{
    public function index(Request $request)
    {
        $query = $request->input('search');

        $duplicates = BeneficiaryDuplicate::query()
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

        return view('beneficiaries.duplicates', compact('duplicates', 'query'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string'],
        ]);

        BeneficiaryDuplicate::create($request->all());

        return back()->with('status', 'Duplicate record added.');
    }

    public function update(Request $request, BeneficiaryDuplicate $duplicate)
    {
        $request->validate([
            'name' => ['required', 'string'],
        ]);

        $duplicate->update($request->all());

        return back()->with('status', 'Duplicate record updated.');
    }

    public function destroy(BeneficiaryDuplicate $duplicate)
    {
        $duplicate->delete();

        return back()->with('status', 'Duplicate record deleted.');
    }
}
