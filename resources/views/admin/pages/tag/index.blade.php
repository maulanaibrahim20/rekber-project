@extends('layouts.admin.main')

@push('page-haeder')
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <h2 class="page-title">Tag</h2>
            </div>
        </div>
    </div>
@endpush

@section('content')
    <div class="container-xl">
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered" id="tag-table">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th class="text-center">Action</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(function () {
            $('#tag-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route('tag.getData') }}',
                columns: [
                    { data: 'name', name: 'name' },
                    { data: 'action', name: 'action', orderable: false, searchable: false },
                ]
            });
        });
    </script>
@endpush