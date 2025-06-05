<form action="{{ route('user.store') }}" method="POST" id="form-add-user">
    @csrf
    <div class="modal-header">
        <h5 class="modal-title">Add User</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
    </div>
    <div class="modal-body">
        <div class="mb-3">
            <label class="form-label">Name</label>
            <input type="text" name="name" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Username</label>
            <input type="text" name="username" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Phone</label>
            <input type="text" name="phone" class="form-control">
        </div>
        <div class="mb-3">
            <label class="form-label">Gender</label>
            <select name="gender" class="form-select">
                <option value="">-- Select Gender --</option>
                <option value="male">Male</option>
                <option value="female">Female</option>
            </select>
        </div>
        <div class="mb-3">
            <label class="form-label">Password</label>
            <input type="password" name="password" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Confirm Password</label>
            <input type="password" name="password_confirmation" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Status</label>
            <select name="status" class="form-select" required>
                <option disabled selected>-- Pilih Status --</option>
                @foreach ($status as $key => $label)
                    <option value="{{ $key }}">{{ $label }}</option>
                @endforeach
            </select>
        </div>

        <div class="form-check">
            <input type="checkbox" class="form-check-input" name="is_private" value="1" id="isprivate">
            <label class="form-check-label" for="isprivate">Private Profile</label>
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-link" data-bs-dismiss="modal">Cancel</button>
        <button type="submit" class="btn btn-primary">Save</button>
    </div>
</form>
<script>
    $('#form-add-user').on('submit', function (e) {
        e.preventDefault();

        let form = $(this);
        let url = form.attr('action');
        let formData = new FormData(this);

        $.ajax({
            url: url,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function (response) {
                $('#globalModal').modal('hide');

                form[0].reset();

                $('#user-table').DataTable().ajax.reload(null, false);
                Swal.fire('Berhasil', response.message, 'success');
            },
            error: function (xhr) {
                let errMsg = 'Terjadi kesalahan.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errMsg = xhr.responseJSON.message;
                }
                Swal.fire('Gagal', errMsg, 'error');
            }
        });
    });

</script>