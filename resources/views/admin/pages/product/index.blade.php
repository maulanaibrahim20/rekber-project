@extends('layouts.admin.main')

@push('page-haeder')
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <h2 class="page-title">Products</h2>
            </div>
        </div>
    </div>
@endpush

@section('content')
    <div class="container-xl">
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered" id="product-table">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Price</th>
                                <th>Location</th>
                                <th>Priority</th>
                                <th>Status</th>
                                <th>Created At</th>
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
            $('#product-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route('product.getData') }}',
                columns: [
                    { data: 'name', name: 'name' },
                    { data: 'price', name: 'price' },
                    { data: 'location', name: 'location' },
                    { data: 'priority', name: 'priority', orderable: false, searchable: false },
                    { data: 'status', name: 'status' },
                    { data: 'created_at', name: 'created_at' },
                    { data: 'action', name: 'action', orderable: false, searchable: false },
                ]
            });
        });
    </script>
@endpush