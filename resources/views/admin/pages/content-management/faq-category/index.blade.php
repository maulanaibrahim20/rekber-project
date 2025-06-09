@extends('layouts.admin.main')
@push('page-haeder')
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <h2 class="page-title">FAQ Categories</h2>
            </div>
            <div class="col-auto ms-auto d-print-none">
                <div class="btn-list">
                    <a href="#" class="btn btn-primary d-none d-sm-inline-block open-global-modal"
                        data-url="{{ route('faq.category.create') }}" data-title="Add FAQ Category">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24"
                            stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round"
                            stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <path d="M12 5l0 14" />
                            <path d="M5 12l14 0" />
                        </svg>
                        Add FAQ Category
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
                <table class="table table-bordered" id="faq-category-table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Slug</th>
                            <th>Jumlah FAQ</th>
                            <th>Created At</th>
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>
                </table>
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
        $('#faq-category-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: '{{ route('faq.category.getData') }}',
            columns: [
                { data: 'name', name: 'name' },
                { data: 'slug', name: 'slug' },
                { data: 'faq_count', name: 'faq_count', searchable: false, orderable: false },
                { data: 'created_at', name: 'created_at' },
                { data: 'action', name: 'action', orderable: false, searchable: false },
            ]
        });

        $(document).on('click', '.btn-delete-category', function () {
            const id = $(this).data('id');
            const url = '{{ route('faq.category.destroy', ':id') }}'.replace(':id', id);

            Swal.fire({
                title: 'Yakin ingin menghapus?',
                text: "Kategori ini beserta FAQ-nya akan dihapus permanen.",
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
                        data: { _token: '{{ csrf_token() }}' },
                        success: function (response) {
                            Swal.fire('Berhasil', response.message, 'success');
                            $('#faq-category-table').DataTable().ajax.reload(null, false);
                        },
                        error: function (xhr) {
                            Swal.fire('Gagal', 'Terjadi kesalahan saat menghapus.', 'error');
                        }
                    });
                }
            });
        });
    </script>
@endpush