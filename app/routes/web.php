<?php

use App\Http\Controllers\BeneficiaryController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('form');
});

Route::get('/dashboard', function () {
    $seniors = \App\Models\Beneficiary::where('age', '>=', 60)->count();
    $students = \App\Models\Beneficiary::where('student_status', 'Yes')->count();
    $governmentEmployees = \App\Models\Beneficiary::where('employment_status', 'Gov Employee')->count();
    $pwds = \App\Models\Beneficiary::where('pwd_status', 'Yes')->count();
    $ineligible = \App\Models\Beneficiary::where('eligibility_status', 'Ineligible due to underage')->count();
    $eligible = \App\Models\Beneficiary::where('eligibility_status', 'Eligible')->count();

    return view('form', compact('seniors', 'students', 'governmentEmployees', 'pwds', 'ineligible', 'eligible'));
})->name('dashboard');

Route::get('/beneficiaries', [BeneficiaryController::class, 'index'])->name('beneficiaries.index');
Route::post('/beneficiaries', [BeneficiaryController::class, 'store'])->name('beneficiaries.store');
Route::put('/beneficiaries/{beneficiary}', [BeneficiaryController::class, 'update'])->name('beneficiaries.update');
Route::delete('/beneficiaries/{beneficiary}', [BeneficiaryController::class, 'destroy'])->name('beneficiaries.destroy');
Route::post('/beneficiaries/import', [BeneficiaryController::class, 'import'])->name('beneficiaries.import');
Route::get('/beneficiaries/duplicates', [\App\Http\Controllers\BeneficiaryDuplicateController::class, 'index'])->name('duplicates.index');
Route::post('/beneficiaries/duplicates', [\App\Http\Controllers\BeneficiaryDuplicateController::class, 'store'])->name('duplicates.store');
Route::put('/beneficiaries/duplicates/{duplicate}', [\App\Http\Controllers\BeneficiaryDuplicateController::class, 'update'])->name('duplicates.update');
Route::delete('/beneficiaries/duplicates/{duplicate}', [\App\Http\Controllers\BeneficiaryDuplicateController::class, 'destroy'])->name('duplicates.destroy');
