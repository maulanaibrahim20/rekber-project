@extends('layouts.admin.main')
@push('page-haeder')
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <h2 class="page-title">
                    Permission
                </h2>
            </div>
            <div class="col-auto ms-auto d-print-none">
                <div class="btn-list">
                    <a href="#" class="btn btn-primary d-none d-sm-inline-block open-global-modal"
                        data-url="{{ route('config.permission.create') }}" data-title="Add Permission">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24"
                            stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round"
                            stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <path d="M12 5l0 14" />
                            <path d="M5 12l14 0" />
                        </svg>
                        Add new permission
                    </a>

                </div>
            </div>
        </div>
    </div>
@endpush
@section('content')
    <div class="container-xl">
        <div class="card">
            <div class="card-body">
                <div id="table-default" class="table-responsive">
                    <table class="table table-bordered" id="permission-table">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Guard Name</th>
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
        $(document).on('click', '.open-global-modal', function (e) {
            e.preventDefault();

            let url = $(this).data('url');
            let title = $(this).data('title') ?? 'Loading...';

            $('#globalModal').modal('show');
            $('#modal-content-container').html('<div class="modal-body text-center p-5"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div></div>');

            $.ajax({
                url: url,
                method: 'GET',
                success: function (response) {
                    $('#modal-content-container').html(response);
                },
                error: function () {
                    $('#modal-content-container').html('<div class="modal-body"><div class="alert alert-danger">Gagal memuat data. Coba lagi nanti.</div></div>');
                }
            });
        })

        $(function () {
            $('#permission-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route('config.permission.getData') }}',
                columns: [
                    { data: 'name', name: 'name' },
                    { data: 'guard_name', name: 'guard_name' },
                    { data: 'action', name: 'action', orderable: false, searchable: false },
                ]
            });
        });

        $(document).on('click', '.btn-delete-user', function () {
            const id = $(this).data('id');
            const url = '{{ route('user.destroy', ':id') }}'.replace(':id', id);

            Swal.fire({
                title: 'Yakin ingin menghapus?',
                text: "Data user akan dihapus permanen.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: url,
                        type: 'DELETE',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function (response) {
                            Swal.fire('Terhapus!', response.message, 'success');
                            $('#permission-table').DataTable().ajax.reload(null, false);
                        },
                        error: function (xhr) {
                            let msg = 'Terjadi kesalahan saat menghapus.';
                            if (xhr.responseJSON?.message) {
                                msg = xhr.responseJSON.message;
                            }
                            Swal.fire('Gagal', msg, 'error');
                        }
                    });
                }
            });
        });

    </script>
@endpush
