<div class="modal-content">
    <div class="modal-body p-0">
        <div class="row g-0">
            <!-- Media Section -->
            <div class="col-md-7 bg-dark text-center d-flex align-items-center justify-content-center position-relative"
                style="min-height: 400px; max-height: 80vh;">
                @if ($post->media->isEmpty())
                    <p class="text-white p-5">Tidak ada media.</p>
                @else
                    <div id="postCarousel{{ $post->id }}" class="carousel slide w-100 h-100" data-bs-ride="carousel">
                        <div class="carousel-inner h-100">
                            @foreach ($post->media as $index => $media)
                                <div class="carousel-item {{ $index === 0 ? 'active' : '' }} h-100">
                                    <div
                                        class="media-container w-100 h-100 d-flex align-items-center justify-content-center bg-black">
                                        @if ($media->file_type === 'image')
                                            <img src="{{ Storage::url($media->file_url) }}" class="carousel-media img-fluid"
                                                style="max-width: 100%; max-height: 100%; object-fit: contain;"
                                                onload="adjustMediaFit(this)" alt="Post media">
                                        @elseif ($media->file_type === 'video')
                                            <video autoplay controls class="carousel-media"
                                                style="max-width: 100%; max-height: 100%; object-fit: contain;"
                                                onloadedmetadata="adjustMediaFit(this)">
                                                <source src="{{ Storage::url($media->file_url) }}" type="video/mp4">
                                                Browser tidak mendukung video.
                                            </video>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        @if ($post->media->count() > 1)
                            <!-- Carousel indicators -->
                            <div class="carousel-indicators">
                                @foreach ($post->media as $index => $media)
                                    <button type="button" data-bs-target="#postCarousel{{ $post->id }}"
                                        data-bs-slide-to="{{ $index }}" class="{{ $index === 0 ? 'active' : '' }}"
                                        aria-current="{{ $index === 0 ? 'true' : 'false' }}"
                                        aria-label="Slide {{ $index + 1 }}"></button>
                                @endforeach
                            </div>

                            <!-- Carousel controls -->
                            <button class="carousel-control-prev" type="button" data-bs-target="#postCarousel{{ $post->id }}"
                                data-bs-slide="prev">
                                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                <span class="visually-hidden">Sebelumnya</span>
                            </button>
                            <button class="carousel-control-next" type="button" data-bs-target="#postCarousel{{ $post->id }}"
                                data-bs-slide="next">
                                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                <span class="visually-hidden">Berikutnya</span>
                            </button>
                        @endif
                    </div>
                @endif
            </div>

            <!-- Content Section -->
            <div class="col-md-5 d-flex flex-column" style="min-height: 400px; max-height: 80vh;">
                <!-- Header -->
                <div class="p-3 border-bottom bg-white">
                    <div class="d-flex align-items-center">
                        <div class="avatar avatar-sm me-2">
                            <div class="avatar-placeholder bg-primary text-white rounded-circle d-flex align-items-center justify-content-center"
                                style="width: 32px; height: 32px;">
                                {{ substr($post->user->name ?? 'U', 0, 1) }}
                            </div>
                        </div>
                        <strong class="text-dark">{{ $post->user->name ?? 'Unknown' }}</strong>
                    </div>
                </div>

                <!-- Content Area -->
                <div class="flex-grow-1 p-3 bg-white" style="overflow-y: auto;">
                    <!-- Caption -->
                    @if($post->caption)
                        <div class="mb-3">
                            <div class="d-flex">
                                <div class="avatar avatar-sm me-2">
                                    <div class="avatar-placeholder bg-primary text-white rounded-circle d-flex align-items-center justify-content-center"
                                        style="width: 24px; height: 24px; font-size: 12px;">
                                        {{ substr($post->user->name ?? 'U', 0, 1) }}
                                    </div>
                                </div>
                                <div>
                                    <strong>{{ $post->user->name ?? 'Unknown' }}</strong>
                                    <span class="ms-1">{!! nl2br(e($post->caption)) !!}</span>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Comments -->
                    <div class="comments-section">
                        @forelse ($post->comments as $comment)
                            <div class="mb-2 d-flex">
                                <div class="avatar avatar-sm me-2">
                                    <div class="avatar-placeholder bg-secondary text-white rounded-circle d-flex align-items-center justify-content-center"
                                        style="width: 24px; height: 24px; font-size: 12px;">
                                        {{ substr($comment->user->name ?? 'U', 0, 1) }}
                                    </div>
                                </div>
                                <div>
                                    <strong>{{ $comment->user->name ?? 'Unknown' }}</strong>
                                    <span class="ms-1">{{ $comment->comment }}</span>
                                </div>
                            </div>
                        @empty
                            <p class="text-muted small">Belum ada komentar.</p>
                        @endforelse
                    </div>
                </div>

                <!-- Footer -->
                <div class="p-3 border-top bg-white">
                    <!-- Likes and stats -->
                    <div class="mb-2">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-heart text-danger me-1"></i>
                            <small class="text-muted">{{ $post->likes->count() }} likes</small>
                        </div>
                        @if($post->created_at)
                            <small class="text-muted d-block mt-1">
                                {{ $post->created_at->diffForHumans() }}
                            </small>
                        @endif
                    </div>

                    <!-- Close button -->
                    <div class="text-end">
                        <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">
                            <i class="fas fa-times me-1"></i> Tutup
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function adjustMediaFit(element) {
        if (!element) return;

        setTimeout(() => {
            let aspectRatio;

            // Get aspect ratio based on element type
            if (element.tagName === 'IMG') {
                if (element.naturalWidth === 0 || element.naturalHeight === 0) return;
                aspectRatio = element.naturalWidth / element.naturalHeight;
            } else if (element.tagName === 'VIDEO') {
                if (element.videoWidth === 0 || element.videoHeight === 0) return;
                aspectRatio = element.videoWidth / element.videoHeight;
            } else {
                return;
            }

            const container = element.closest('.media-container');
            if (!container) return;

            const containerAspectRatio = container.offsetWidth / container.offsetHeight;

            // Instagram-style logic: prioritize portrait orientation
            // Portrait media (aspectRatio < 1) should fit contain to show full image
            // Landscape media (aspectRatio > 1) can be cropped more aggressively

            if (aspectRatio < 0.8) {
                // Very portrait - always contain to show full image
                element.style.objectFit = 'contain';
                element.style.width = 'auto';
                element.style.height = '100%';
            } else if (aspectRatio > 1.8) {
                // Very landscape - crop to fit better
                element.style.objectFit = 'cover';
                element.style.width = '100%';
                element.style.height = '100%';
            } else {
                // Moderate ratios - use contain for better visibility
                element.style.objectFit = 'contain';
                element.style.width = '100%';
                element.style.height = '100%';
            }

        }, 100); // Small delay to ensure proper loading
    }

    // Handle carousel slide changes
    document.addEventListener('DOMContentLoaded', function () {
        const carousels = document.querySelectorAll('.carousel');
        carousels.forEach(function (carousel) {
            carousel.addEventListener('slid.bs.carousel', function (e) {
                const activeItem = e.target.querySelector('.carousel-item.active');
                const media = activeItem.querySelector('.carousel-media');
                if (media) {
                    adjustMediaFit(media);
                }
            });
        });
    });
</script>