<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Duplicate Review</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container py-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 mb-0">Duplicate Review</h1>
            <a href="{{ route('beneficiaries.index') }}" class="btn btn-outline-secondary">Back to Beneficiaries</a>
        </div>

        @if (session('status'))
            <div class="alert alert-info">{{ session('status') }}</div>
        @endif

        <form action="{{ route('duplicates.index') }}" method="GET" class="row g-2 mb-4">
            <div class="col-md-10">
                <input type="text" name="search" class="form-control" placeholder="Search by name, ID number, email, or birthdate" value="{{ $query ?? '' }}">
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100">Search</button>
            </div>
        </form>

        <div class="card mb-4">
            <div class="card-header bg-warning text-dark">
                <strong>Add Review Entry</strong>
            </div>
            <div class="card-body">
                <form action="{{ route('duplicates.store') }}" method="POST">
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
                            <input class="form-control" name="birthdate" placeholder="Birthdate">
                        </div>
                    </div>
                    <button class="btn btn-primary mt-3">Add</button>
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
                            <th>ID Number</th>
                            <th>Email</th>
                            <th>Birthdate</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($duplicates as $duplicate)
                            <tr>
                                <td>{{ $duplicate->name }}</td>
                                <td>{{ $duplicate->age }}</td>
                                <td>{{ $duplicate->id_number }}</td>
                                <td>{{ $duplicate->email }}</td>
                                <td>{{ $duplicate->birthdate }}</td>
                                <td>
                                    <div class="d-flex gap-2">
                                        <button class="btn btn-sm btn-outline-primary" type="button" data-bs-toggle="collapse" data-bs-target="#edit-{{ $duplicate->id }}">Edit</button>
                                        <form action="{{ route('duplicates.destroy', $duplicate) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-sm btn-danger">Delete</button>
                                        </form>
                                    </div>
                                    <div class="collapse mt-2" id="edit-{{ $duplicate->id }}">
                                        <form action="{{ route('duplicates.update', $duplicate) }}" method="POST" class="border rounded p-3">
                                            @csrf
                                            @method('PUT')
                                            <div class="row g-2">
                                                <div class="col-md-6">
                                                    <input class="form-control form-control-sm" name="name" value="{{ $duplicate->name }}" required>
                                                </div>
                                                <div class="col-md-2">
                                                    <input class="form-control form-control-sm" name="age" value="{{ $duplicate->age }}">
                                                </div>
                                                <div class="col-md-4">
                                                    <input class="form-control form-control-sm" name="id_number" value="{{ $duplicate->id_number }}">
                                                </div>
                                                <div class="col-md-6 mt-2">
                                                    <input class="form-control form-control-sm" name="email" value="{{ $duplicate->email }}">
                                                </div>
                                                <div class="col-md-6 mt-2">
                                                    <input class="form-control form-control-sm" name="birthdate" value="{{ $duplicate->birthdate }}">
                                                </div>
                                            </div>
                                            <button class="btn btn-sm btn-success mt-2">Save</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted">No duplicate review entries.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                <div class="d-flex justify-content-center">
                    {{ $duplicates->links() }}
                </div>
            </div>
        </div>
    </div>
</body>
</html>
