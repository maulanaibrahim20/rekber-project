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
                <h2 class="fw-bold mb-1">Post Caption : {{ $post->caption }}</h2>
            </div>
            <div class="col-auto ms-auto text-end">
                <a href="{{ route('post') }}" class="btn btn-secondary">
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
                <!-- Post Info -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-white border-bottom">
                        <h5 class="card-title mb-0"><i class="bi bi-info-circle"></i> Post Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-muted">Author</label>
                                <div class="d-flex align-items-center">
                                    <div
                                        class="avatar-sm bg-primary rounded-circle d-flex align-items-center justify-content-center me-2">
                                        <i class="bi bi-person text-white"></i>
                                    </div>
                                    <div>
                                        <div class="fw-semibold">{{ $post->user->name ?? 'N/A' }}</div>
                                        <small class="text-muted">{{ $post->user->email ?? '' }}</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-muted">Created At</label>
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-calendar text-info me-2"></i>
                                    <span>{{ $post->created_at->format('d M Y H:i') }}</span>
                                </div>
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold text-muted">Caption</label>
                                <div class="bg-light p-3 rounded">{!! nl2br(e($post->caption)) !!}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Media Preview -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-white border-bottom">
                        <h5 class="card-title mb-0"><i class="bi bi-images"></i> Post Media</h5>
                    </div>
                    <div class="card-body">
                        @if($post->media->isEmpty())
                            <div class="text-center py-4">
                                <i class="bi bi-image display-1 text-muted"></i>
                                <p class="text-muted mt-2">No media available</p>
                            </div>
                        @else
                            <div id="postCarousel" class="carousel slide" data-bs-ride="carousel">
                                <div class="carousel-inner">
                                    @foreach($post->media as $index => $media)
                                        <div class="carousel-item {{ $index == 0 ? 'active' : '' }}">
                                            @if($media->file_type == 'image')
                                                <img src="{{ Storage::url($media->file_url) }}" class="d-block w-100 rounded"
                                                    style="object-fit: cover; height: 400px;">
                                            @elseif($media->file_type == 'video')
                                                <video controls class="d-block w-100 rounded" style="height: 400px; object-fit: cover;">
                                                    <source src="{{ Storage::url($media->file_url) }}" type="video/mp4">
                                                </video>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                                @if($post->media->count() > 1)
                                    <button class="carousel-control-prev" type="button" data-bs-target="#postCarousel"
                                        data-bs-slide="prev">
                                        <span class="carousel-control-prev-icon"></span>
                                    </button>
                                    <button class="carousel-control-next" type="button" data-bs-target="#postCarousel"
                                        data-bs-slide="next">
                                        <span class="carousel-control-next-icon"></span>
                                    </button>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Comments -->
                <div class="card shadow-sm">
                    <div class="card-header bg-white border-bottom">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-chat-dots"></i> Comments
                            <span class="badge text-white bg-primary ms-2">{{ $post->comments->count() }}</span>
                        </h5>
                    </div>
                    <div class="card-body">
                        @if($post->comments->isEmpty())
                            <div class="text-center py-4">
                                <i class="bi bi-chat display-1 text-muted"></i>
                                <p class="text-muted mt-2">No comments yet</p>
                            </div>
                        @else
                            <div class="list-group list-group-flush">
                                @foreach($post->comments as $comment)
                                    <div class="list-group-item px-0 border-0 border-bottom">
                                        <div class="d-flex align-items-start">
                                            <div
                                                class="avatar-sm bg-secondary rounded-circle d-flex align-items-center justify-content-center me-3 flex-shrink-0">
                                                <i class="bi bi-person text-white"></i>
                                            </div>
                                            <div class="flex-grow-1">
                                                <div class="d-flex justify-content-between align-items-center mb-1">
                                                    <h6 class="mb-0 fw-semibold">
                                                        {{ $comment->user->name ?? 'User ID: ' . $comment->user_id }}
                                                    </h6>
                                                    <small
                                                        class="text-muted">{{ $comment->created_at->format('d M Y H:i') }}</small>
                                                </div>
                                                <p class="mb-0 text-muted">{{ $comment->comment }}</p>
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
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-white border-bottom">
                        <h5 class="card-title mb-0"><i class="bi bi-gear"></i> Status Management</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('post.updateStatus', $post->uuid) }}" method="POST" id="statusForm">
                            @csrf
                            @method('PUT')
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Current Status</label>
                                <div class="mb-2">
                                    @php
                                        $status = $post->status;
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
                                    <option value="1" {{ $post->status == 1 ? 'selected' : '' }}>
                                        Published
                                    </option>
                                    <option value="2" {{ $post->status == 2 ? 'selected' : '' }}>
                                        Draft
                                    </option>
                                    <option value="3" {{ $post->status == 3 ? 'selected' : '' }}>
                                        Archived
                                    </option>
                                    <option value="4" {{ $post->status == 4 ? 'selected' : '' }}>
                                        Blocked
                                    </option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="status_reason" class="form-label fw-semibold">Reason (Optional)</label>
                                <textarea class="form-control" id="status_reason" name="status_reason" rows="3"
                                    placeholder="Enter reason for status change...">{{$post->reason}}</textarea>
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
                                    <div class="fw-bold fs-5 mt-1">{{ $post->likes->count() }}</div>
                                    <small class="text-muted">Likes</small>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="bg-light p-3 rounded">
                                    <i class="bi bi-chat-dots-fill text-primary fs-4"></i>
                                    <div class="fw-bold fs-5 mt-1">{{ $post->comments->count() }}</div>
                                    <small class="text-muted">Comments</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tags (jika ada) -->
                @if ($post->tags && $post->tags->count())
                    <div class="card shadow-sm">
                        <div class="card-header bg-white border-bottom">
                            <h5 class="card-title mb-0"><i class="bi bi-tags"></i> Tags</h5>
                        </div>
                        <div class="card-body">
                            <div class="d-flex flex-wrap gap-2">
                                @foreach ($post->tags as $tag)
                                    <span class="badge text-white bg-info fs-6">
                                        <i class="bi bi-tag"></i> {{ $tag->name }}
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif
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