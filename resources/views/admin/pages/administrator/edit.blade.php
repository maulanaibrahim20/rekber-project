<form action="{{ route('administrator.update', $admin->id) }}" method="POST" id="form-edit-admin">
    @csrf
    @method('PUT')
    <div class="modal-header">
        <h5 class="modal-title">Edit Administrator</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
    </div>

    <div class="modal-body">
        <div class="mb-3">
            <label class="form-label">Name</label>
            <input type="text" name="name" value="{{ $admin->name }}" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" name="email" value="{{ $admin->email }}" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Username</label>
            <input type="text" name="username" value="{{ $admin->username }}" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Password <small class="text-muted">(kosongkan jika tidak ingin
                    mengganti)</small></label>
            <input type="password" name="password" class="form-control">
        </div>

        <div class="mb-3">
            <label class="form-label">Confirm Password</label>
            <input type="password" name="password_confirmation" class="form-control">
        </div>

        <div class="mb-3">
            <label class="form-label">Status</label>
            <select name="status" class="form-select" required>
                <option value="active" {{ $admin->status == 'active' ? 'selected' : '' }}>Active</option>
                <option value="inactive" {{ $admin->status == 'inactive' ? 'selected' : '' }}>Inactive</option>
            </select>
        </div>

        <div class="form-check">
            <input type="checkbox" class="form-check-input" name="is_super_admin" value="1" id="superadmin" {{ $admin->is_super_admin ? 'checked' : '' }}>
            <label class="form-check-label" for="superadmin">Super Admin</label>
        </div>
    </div>

    <div class="modal-footer">
        <button type="button" class="btn btn-link" data-bs-dismiss="modal">Cancel</button>
        <button type="submit" class="btn btn-primary">Update</button>
    </div>
</form>
<script>
    $('#form-edit-admin').on('submit', function (e) {
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

                $('#admin-table').DataTable().ajax.reload(null, false);
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil',
                    text: 'Data administrator berhasil diperbarui.'
                });
            },
            error: function (xhr) {
                let errMsg = 'Terjadi kesalahan.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errMsg = xhr.responseJSON.message;
                }
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal',
                    text: errMsg
                });
            }
        });
    });

    $('#form-edit-admin').on('submit', function (e) { });
</script>
