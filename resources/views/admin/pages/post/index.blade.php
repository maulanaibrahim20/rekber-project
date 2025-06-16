@extends('layouts.admin.main')
@push('styles')
    <style>
        /* Custom modal styles for Instagram-like appearance */
        .modal-full-width .modal-dialog {
            max-width: 90vw;
            width: 90vw;
            margin: 1.75rem auto;
        }

        .media-container {
            background: #000;
            position: relative;
        }

        .carousel-media {
            transition: all 0.3s ease;
        }

        .carousel-indicators {
            bottom: 10px;
        }

        .carousel-indicators [data-bs-target] {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            margin: 0 3px;
            background-color: rgba(255, 255, 255, 0.5);
            border: none;
        }

        .carousel-indicators .active {
            background-color: white;
        }

        .carousel-control-prev,
        .carousel-control-next {
            width: 40px;
            opacity: 0.7;
        }

        .carousel-control-prev:hover,
        .carousel-control-next:hover {
            opacity: 1;
        }

        .comments-section {
            max-height: 300px;
            overflow-y: auto;
        }

        .avatar-placeholder {
            font-size: 14px;
            font-weight: 600;
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .modal-full-width .modal-dialog {
                max-width: 95vw;
                width: 95vw;
                margin: 0.5rem auto;
            }

            .modal-content .row {
                flex-direction: column;
            }

            .col-md-7,
            .col-md-5 {
                max-width: 100%;
                flex: 0 0 100%;
            }

            .col-md-7 {
                max-height: 60vh;
            }

            .col-md-5 {
                max-height: 40vh;
            }
        }
    </style>
@endpush
@push('page-haeder')
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <h2 class="page-title">Posting</h2>
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
                                <th>No</th>
                                <th>User</th>
                                <th>Caption</th>
                                <th>Location</th>
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

    <!-- Instagram-style Full Width Modal -->
    <div class="modal modal-blur fade" id="modal-full-width" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-full-width modal-dialog-centered" role="document">

        </div>
    </div>
@endsection
@push('scripts')
    <script>
        $(function () {
            $('#product-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route('post.getData') }}',
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                    { data: 'user', name: 'user.name' },
                    { data: 'caption', name: 'caption', orderable: false, searchable: false },
                    { data: 'location', name: 'location' },
                    { data: 'status', name: 'status' },
                    { data: 'created_at', name: 'created_at' },
                    { data: 'action', name: 'action', orderable: false, searchable: false },
                ]
            });

            $(document).on('click', '.caption-clickable', function (e) {
                e.preventDefault();

                let postId = $(this).data('id');
                let url = '{{ url('/~admin/post/showModal') }}/' + postId;
                let modal = $('#modal-full-width');

                modal.modal('show');
                modal.find('.modal-dialog').html('<div class="modal-body text-center p-5"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div></div>');

                $.ajax({
                    url: url,
                    method: 'GET',
                    success: function (response) {
                        modal.find('.modal-dialog').html(response);
                    },
                    error: function () {
                        modal.find('.modal-dialog').html('<div class="modal-body"><div class="alert alert-danger">Gagal memuat data. Coba lagi nanti.</div></div>');
                    }
                });
            });

        });
    </script>
@endpush