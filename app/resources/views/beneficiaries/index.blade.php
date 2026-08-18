<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Beneficiaries</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container py-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 mb-0">Beneficiaries</h1>
            <a href="/dashboard" class="btn btn-outline-secondary">Back to Dashboard</a>
        </div>

        @if (session('status'))
            <div class="alert alert-success">{{ session('status') }}</div>
        @endif

        <div class="d-flex justify-content-between align-items-center mb-3">
            <a href="{{ route('duplicates.index') }}" class="btn btn-outline-warning">Review Duplicates</a>
        </div>

        <form action="{{ route('beneficiaries.index') }}" method="GET" class="row g-2 mb-4">
            <div class="col-md-10">
                <input type="text" name="search" class="form-control" placeholder="Search by name, ID number, email, or birthdate" value="{{ $query ?? '' }}">
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100">Search</button>
            </div>
        </form>

        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <strong>Import Excel</strong>
            </div>
            <div class="card-body">
                <form action="{{ route('beneficiaries.import') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <label for="file" class="form-label">Upload Excel file</label>
                        <input class="form-control" type="file" id="file" name="file" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Import</button>
                </form>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header bg-success text-white">
                <strong>Manual Add Beneficiary</strong>
            </div>
            <div class="card-body">
                <form action="{{ route('beneficiaries.store') }}" method="POST">
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-4">
                            <input class="form-control" name="name" placeholder="Name" required>
                        </div>
                        <div class="col-md-2">
                            <input class="form-control" name="age" placeholder="Age">
                        </div>
                        <div class="col-md-2">
                            <input class="form-control" name="id_number" placeholder="ID Number">
                        </div>
                        <div class="col-md-2">
                            <input class="form-control" name="email" placeholder="Email">
                        </div>
                        <div class="col-md-2">
                            <input class="form-control" type="date" name="birthdate" placeholder="Birthdate">
                        </div>
                        <div class="col-md-3">
                            <input class="form-control" name="employment_status" placeholder="Employment Status">
                        </div>
                        <div class="col-md-3">
                            <input class="form-control" name="student_status" placeholder="Student Status">
                        </div>
                        <div class="col-md-3">
                            <input class="form-control" name="pwd_status" placeholder="PWD Status">
                        </div>
                        <div class="col-md-3">
                            <input class="form-control" name="eligibility_status" placeholder="Eligibility Status">
                        </div>
                    </div>
                    <button class="btn btn-success mt-3">Add Beneficiary</button>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <table class="table table-striped table-bordered align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th>Name</th>
                            <th>Age</th>
                            <th>Employment Status (Gov Employee)</th>
                            <th>Student Status</th>
                            <th>PWD Status</th>
                            <th>Eligibility Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($beneficiaries as $beneficiary)
                            <tr>
                                <td>{{ $beneficiary->name }}</td>
                                <td>{{ $beneficiary->age }}</td>
                                <td>{{ $beneficiary->employment_status }}</td>
                                <td>{{ $beneficiary->student_status }}</td>
                                <td>{{ $beneficiary->pwd_status }}</td>
                                <td>{{ $beneficiary->eligibility_status }}</td>
                                <td>
                                    <div class="d-flex gap-2">
                                        <button class="btn btn-sm btn-outline-primary" type="button" data-bs-toggle="collapse" data-bs-target="#edit-beneficiary-{{ $beneficiary->id }}">Edit</button>
                                        <form action="{{ route('beneficiaries.destroy', $beneficiary) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-sm btn-danger">Delete</button>
                                        </form>
                                    </div>
                                    <div class="collapse mt-2" id="edit-beneficiary-{{ $beneficiary->id }}">
                                        <form action="{{ route('beneficiaries.update', $beneficiary) }}" method="POST" class="border rounded p-3">
                                            @csrf
                                            @method('PUT')
                                            <div class="row g-2">
                                                <div class="col-md-6">
                                                    <input class="form-control form-control-sm" name="name" value="{{ $beneficiary->name }}" required>
                                                </div>
                                                <div class="col-md-2">
                                                    <input class="form-control form-control-sm" name="age" value="{{ $beneficiary->age }}">
                                                </div>
                                                <div class="col-md-4">
                                                    <input class="form-control form-control-sm" name="id_number" value="{{ $beneficiary->id_number }}">
                                                </div>
                                                <div class="col-md-6 mt-2">
                                                    <input class="form-control form-control-sm" name="email" value="{{ $beneficiary->email }}">
                                                </div>
                                                <div class="col-md-6 mt-2">
                                                    <input class="form-control form-control-sm" type="date" name="birthdate" value="{{ $beneficiary->birthdate }}">
                                                </div>
                                                <div class="col-md-4 mt-2">
                                                    <input class="form-control form-control-sm" name="employment_status" value="{{ $beneficiary->employment_status }}">
                                                </div>
                                                <div class="col-md-4 mt-2">
                                                    <input class="form-control form-control-sm" name="student_status" value="{{ $beneficiary->student_status }}">
                                                </div>
                                                <div class="col-md-4 mt-2">
                                                    <input class="form-control form-control-sm" name="pwd_status" value="{{ $beneficiary->pwd_status }}">
                                                </div>
                                                <div class="col-md-12 mt-2">
                                                    <input class="form-control form-control-sm" name="eligibility_status" value="{{ $beneficiary->eligibility_status }}">
                                                </div>
                                            </div>
                                            <button class="btn btn-sm btn-success mt-2">Save</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted">No beneficiaries found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                <div class="d-flex justify-content-center">
                    {{ $beneficiaries->links() }}
                </div>
            </div>
        </div>
    </div>
</body>
</html>
