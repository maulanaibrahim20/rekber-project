@extends('layouts.auth.main')
@section('content')
    <div class="col-12 col-lg-6 d-none d-lg-block">
        <div class="h-100 min-vh-100 d-flex flex-column justify-content-center align-items-center text-white position-relative"
            style="background: linear-gradient(135deg, #00d4aa 0%, #4c63d2 100%);">

            <div class="mb-4">
                <svg width="80" height="80" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M12 1L3 5V11C3 16.55 6.84 21.74 12 23C17.16 21.74 21 16.55 21 11V5L12 1Z" stroke="white"
                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round" fill="none" />
                </svg>
            </div>

            <h1 class="h2 mb-4 text-center fw-bold">TrustPay</h1>

            <p class="text-center mb-5 px-4" style="font-size: 1.1rem; opacity: 0.9;">
                The secure way to conduct transactions with<br>
                escrow protection.
            </p>

            <div class="row g-4 px-4" style="max-width: 500px;">
                <div class="col-6">
                    <div class="text-center">
                        <h5 class="mb-2">Secure Transactions</h5>
                        <p class="small" style="opacity: 0.8;">
                            Protect your money with our escrow service
                        </p>
                    </div>
                </div>
                <div class="col-6">
                    <div class="text-center">
                        <h5 class="mb-2">Instant Verification</h5>
                        <p class="small" style="opacity: 0.8;">
                            Quick identity and payment verification
                        </p>
                    </div>
                </div>
                <div class="col-6">
                    <div class="text-center">
                        <h5 class="mb-2">Dispute Resolution</h5>
                        <p class="small" style="opacity: 0.8;">
                            Fair resolution for transaction disputes
                        </p>
                    </div>
                </div>
                <div class="col-6">
                    <div class="text-center">
                        <h5 class="mb-2">Low Fees</h5>
                        <p class="small" style="opacity: 0.8;">
                            Transparent pricing with no hidden costs
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12 col-lg-6 border-top-wide border-primary d-flex flex-column justify-content-center">
        <div class="container container-tight my-5 px-lg-5">
            <div class="text-center mb-4">
                <a href="." class="navbar-brand navbar-brand-autodark">
                    <svg width="36" height="36" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M12 1L3 5V11C3 16.55 6.84 21.74 12 23C17.16 21.74 21 16.55 21 11V5L12 1Z"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                            fill="none" />
                    </svg>
                </a>
                <h1 class="h2 mb-4 text-center fw-bold">TrustPay</h1>

            </div>
            <h2 class="h3 text-center mb-3">
                Login to your account
            </h2>
            <form id="form-login" method="POST" autocomplete="off" novalidate>
                @csrf
                <div class="mb-3">
                    <label class="form-label">Email atau Username</label>
                    <input type="text" name="login" class="form-control" placeholder="your@email.com / username"
                        value="superadmin@mailinator.com" required>
                </div>
                <div class="mb-2">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" class="form-control" placeholder="Your password" required>
                </div>
                <div class="form-footer">
                    <button type="submit" class="btn btn-primary w-100">Sign in</button>
                </div>
            </form>
            <div class="text-center text-secondary mt-3">
                Don't have account yet? <a href="./sign-up.html" tabindex="-1">Sign up</a>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    <script>
        $('#form-login').on('submit', function (e) {
            e.preventDefault();
            const form = $(this);

            $.ajax({
                url: "{{ route('login') }}",
                method: "POST",
                data: form.serialize(),
                beforeSend: function () {
                    Swal.showLoading();
                },
                success: function (response) {
                    if (response.status) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: response.message,
                            timer: 1500,
                            showConfirmButton: false,
                            willClose: () => {
                                window.location.href = response.redirect;
                            }
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            text: response.message
                        });
                    }
                },
                error: function (xhr) {
                    const res = xhr.responseJSON;
                    let msg = res?.message || 'Terjadi kesalahan';

                    if (xhr.status === 422 && res.errors) {
                        msg = Object.values(res.errors).join('<br>');
                    }

                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        html: msg
                    });
                }
            });
        });
    </script>
@endpush