@extends('layouts.admin.main')
@push('page-haeder')
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <h2 class="page-title">
                    FAQ -> {{ $faqCategory->name }}
                </h2>
            </div>
            <div class="col-auto ms-auto d-print-none">
                <div class="btn-list">
                    <a href="#" class="btn btn-primary open-global-modal"
                        data-url="{{ route('faq.create', $faqCategory->slug) }}" data-title="Add new FAQ">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24"
                            stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round"
                            stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <path d="M12 5l0 14" />
                            <path d="M5 12l14 0" />
                        </svg>
                        Add new FAQ
                    </a>
                </div>
            </div>
        </div>
    </div>
@endpush
@section('content')
    <div class="card">
        <div class="card-body">
            <table class="table table-bordered" id="faq-table">
                <thead>
                    <tr>
                        <th>Question</th>
                        <th>Answer</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
            </table>
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
        $('#faq-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: '{{ route('faq.getData', $faqCategory->slug) }}',
            columns: [
                { data: 'question', name: 'question' },
                { data: 'answer', name: 'answer' },
                { data: 'status', name: 'status' },
                { data: 'action', name: 'action', orderable: false, searchable: false },
            ]
        });

        $(document).on('click', '.btn-delete-faq', function (e) {
            e.preventDefault();
            let id = $(this).data('id');

            Swal.fire({
                title: 'Yakin ingin menghapus FAQ ini?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal',
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: `/~admin/content-management/faq/destroy/${id}`,
                        type: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        success: function (res) {
                            Swal.fire('Berhasil', res.message, 'success');
                            $('#faq-table').DataTable().ajax.reload(null, false);
                        },
                        error: function (xhr) {
                            let res = xhr.responseJSON;
                            let msg = res?.message || 'Terjadi kesalahan saat menghapus.';
                            Swal.fire('Gagal', msg, 'error');
                        }
                    });
                }
            });
        });

    </script>
@endpush