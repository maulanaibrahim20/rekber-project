@extends('layouts.admin.main')
@push('styles')
    <style>
        .avatar-sm {
            width: 40px;
            height: 40px;
        }

        .card {
            transition: all 0.3s ease;
        }

        .card:hover {
            transform: translateY(-2px);
        }

        .list-group-item:hover {
            background-color: rgba(0, 0, 0, 0.02);
        }

        .img-fluid[role="button"]:hover {
            transform: scale(1.02);
            transition: transform 0.2s ease;
        }
    </style>
@endpush
@push('page-haeder')
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <h2 class="fw-bold mb-1">{{ $product->name }}</h2>
            </div>
            <div class="col-auto ms-auto text-end">
                <a href="{{ route('product') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back
                </a>
            </div>
        </div>
    </div>
@endpush
@section('content')
    <div class="container-xl">
        <div class="row">
            <div class="col-lg-8">
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-white border-bottom">
                        <h5 class="card-title mb-0"><i class="bi bi-info-circle"></i> Product Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-muted">Owner</label>
                                <div class="d-flex align-items-center">
                                    <div
                                        class="avatar-sm bg-primary rounded-circle d-flex align-items-center justify-content-center me-2">
                                        <i class="bi bi-person text-white"></i>
                                    </div>
                                    <div>
                                        <div class="fw-semibold">{{ $product->user->name ?? 'N/A' }}</div>
                                        <small class="text-muted">{{ $product->user->email ?? '' }}</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-muted">Location</label>
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-geo-alt text-warning me-2"></i>
                                    <span>{{ $product->location ?? '-' }}</span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-muted">Price</label>
                                <div class="fw-bold text-success fs-5">
                                    Rp {{ number_format($product->price, 0, ',', '.') }}
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-muted">Priority</label>
                                <div>
                                    @if($product->priority == 1)
                                        <span class="badge text-white bg-success fs-6"><i class="bi bi-pin-angle"></i>
                                            Sticky</span>
                                    @else
                                        <span class="badge text-white bg-secondary fs-6">Normal</span>
                                    @endif
                                </div>
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold text-muted">Description</label>
                                <div class="bg-light p-3 rounded">
                                    {!! nl2br(e($product->description)) !!}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Images Section -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-white border-bottom">
                        <h5 class="card-title mb-0"><i class="bi bi-images"></i> Product Images</h5>
                    </div>
                    <div class="card-body">
                        @if($product->images->isEmpty())
                            <div class="text-center py-4">
                                <i class="bi bi-image display-1 text-muted"></i>
                                <p class="text-muted mt-2">No images available</p>
                            </div>
                        @else
                            <div class="row g-3">
                                @foreach($product->images as $img)
                                    <div class="col-md-4 col-sm-6">
                                        <div class="position-relative">
                                            <img src="{{ asset('storage/' . $img->image) }}" alt="Product Image"
                                                class="img-fluid rounded shadow-sm preview-image"
                                                style="height: 200px; width: 100%; object-fit: cover;"
                                                data-src="{{ asset('storage/' . $img->image) }}" role="button">
                                            <div class="position-absolute top-0 end-0 m-2">
                                                <span class="badge text-white bg-dark bg-opacity-75">{{ $loop->iteration }}</span>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Modal tunggal di luar foreach -->
                <div class="modal fade" id="imagePreviewModal" tabindex="-1">
                    <div class="modal-dialog modal-lg modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Product Image</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body text-center">
                                <img id="previewImage" src="" alt="Preview" class="img-fluid rounded">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Comments Section -->
                <div class="card shadow-sm">
                    <div class="card-header bg-white border-bottom">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-chat-dots"></i> Comments
                            <span class="badge text-white bg-primary ms-2">{{ $product->comments->count() }}</span>
                        </h5>
                    </div>
                    <div class="card-body">
                        @if($product->comments->isEmpty())
                            <div class="text-center py-4">
                                <i class="bi bi-chat display-1 text-muted"></i>
                                <p class="text-muted mt-2">No comments yet</p>
                            </div>
                        @else
                            <div class="list-group list-group-flush">
                                @foreach($product->comments as $comment)
                                    <div class="list-group-item px-0 border-0 border-bottom">
                                        <div class="d-flex align-items-start">
                                            <div
                                                class="avatar-sm bg-secondary rounded-circle d-flex align-items-center justify-content-center me-3 flex-shrink-0">
                                                <i class="bi bi-person text-white"></i>
                                            </div>
                                            <div class="flex-grow-1">
                                                <div class="d-flex justify-content-between align-items-center mb-1">
                                                    <h6 class="mb-0 fw-semibold">User ID: {{ $comment->user_id }}</h6>
                                                    <small
                                                        class="text-muted">{{ $comment->created_at->format('d M Y H:i') }}</small>
                                                </div>
                                                <p class="mb-0 text-muted">{{ $comment->comment_text }}</p>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4">
                <!-- Status Management Card -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-white border-bottom">
                        <h5 class="card-title mb-0"><i class="bi bi-gear"></i> Status Management</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('product.updateStatus', $product->uuid) }}" method="POST" id="statusForm">
                            @csrf
                            @method('PUT')
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Current Status</label>
                                <div class="mb-2">
                                    @php
                                        $status = $product->status;
                                        $badgeClass = 'bg-secondary';
                                        $iconClass = 'bi-question-circle';

                                        if ($status === 1) {
                                            $status = 'PUBLISHED';
                                            $badgeClass = 'bg-success';
                                            $iconClass = 'bi-check-circle';
                                        } elseif ($status === 2) {
                                            $status = 'DRAFT';
                                            $badgeClass = 'bg-warning text-dark';
                                            $iconClass = 'bi-pencil';
                                        } elseif ($status === 3) {
                                            $status = 'ARCHIVED';
                                            $badgeClass = 'bg-danger';
                                            $iconClass = 'bi-archive';
                                        } elseif ($status === 4) {
                                            $status = 'BLOCKED';
                                            $badgeClass = 'bg-dark';
                                            $iconClass = 'bi-x-circle';
                                        }
                                    @endphp

                                    <span class="badge fs-6 text-white {{ $badgeClass }}">
                                        <i class="bi {{ $iconClass }}"></i>
                                        {{ ucfirst(strtolower($status)) }}
                                    </span>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="status" class="form-label fw-semibold">Change Status</label>
                                <select class="form-select" id="status" name="status" required>
                                    <option value="" disabled>Select new status...</option>
                                    <option value="1" {{ $product->status == 1 ? 'selected' : '' }}>
                                        Published
                                    </option>
                                    <option value="2" {{ $product->status == 2 ? 'selected' : '' }}>
                                        Draft
                                    </option>
                                    <option value="3" {{ $product->status == 3 ? 'selected' : '' }}>
                                        Archived
                                    </option>
                                    <option value="4" {{ $product->status == 4 ? 'selected' : '' }}>
                                        Blocked
                                    </option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="status_reason" class="form-label fw-semibold">Reason (Optional)</label>
                                <textarea class="form-control" id="status_reason" name="status_reason" rows="3"
                                    placeholder="Enter reason for status change...">{{$product->reason}}</textarea>
                            </div>

                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary" id="updateStatusBtn">
                                    <i class="bi bi-check-lg"></i> Update Status
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-white border-bottom">
                        <h5 class="card-title mb-0"><i class="bi bi-bar-chart"></i> Statistics</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3 text-center">
                            <div class="col-6">
                                <div class="bg-light p-3 rounded">
                                    <i class="bi bi-heart-fill text-danger fs-4"></i>
                                    <div class="fw-bold fs-5 mt-1">{{ $product->likes->count() }}</div>
                                    <small class="text-muted">Likes</small>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="bg-light p-3 rounded">
                                    <i class="bi bi-chat-dots-fill text-primary fs-4"></i>
                                    <div class="fw-bold fs-5 mt-1">{{ $product->comments->count() }}</div>
                                    <small class="text-muted">Comments</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tags Card -->
                <div class="card shadow-sm">
                    <div class="card-header bg-white border-bottom">
                        <h5 class="card-title mb-0"><i class="bi bi-tags"></i> Tags</h5>
                    </div>
                    <div class="card-body">
                        @if($product->tags->isEmpty())
                            <div class="text-center py-3">
                                <i class="bi bi-tag text-muted fs-1"></i>
                                <p class="text-muted mt-2 mb-0">No tags assigned</p>
                            </div>
                        @else
                            <div class="d-flex flex-wrap gap-2">
                                @foreach($product->tags as $tag)
                                    <span class="badge text-white bg-info fs-6">
                                        <i class="bi bi-tag"></i> {{ $tag->name }}
                                    </span>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    <script>
        $(document).ready(function () {
            $('.preview-image').on('click', function () {
                const imageSrc = $(this).data('src');
                $('#previewImage').attr('src', imageSrc);
                $('#imagePreviewModal').modal('show');
            });
        });
    </script>
@endpush