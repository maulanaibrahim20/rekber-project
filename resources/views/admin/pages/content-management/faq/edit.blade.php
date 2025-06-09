<form id="form-edit-faq" action="{{ route('faq.update', $faq->id) }}" method="POST">
    @csrf
    @method('PUT')
    <input type="hidden" name="category_id" value="{{ $category->id }}">
    <div class="modal-header">
        <h5 class="modal-title">Edit FAQ</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
    </div>
    <div class="modal-body">
        <div class="mb-3">
            <label for="question" class="form-label">Question</label>
            <input type="text" class="form-control" id="question" name="question" value="{{ $faq->question }}" required>
        </div>
        <div class="mb-3">
            <label for="answer" class="form-label">Answer</label>
            <input type="text" class="form-control" id="answer" name="answer" value="{{ $faq->answer }}" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Status</label>
            <select name="status" class="form-select" required>
                @foreach ($status as $key => $label)
                    <option value="{{ $key }}" {{ $faq->status == $key ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-link" data-bs-dismiss="modal">Cancel</button>
        <button type="submit" class="btn btn-primary">Update</button>
    </div>
</form>

<script>
    $('#form-edit-faq').on('submit', function (e) {
        e.preventDefault();
        let form = $(this);
        let url = form.attr('action');
        let data = form.serialize();

        $.ajax({
            url: url,
            type: 'POST',
            data: data,
            beforeSend: function () {
                form.find('button[type=submit]').prop('disabled', true);
            },
            success: function (res) {
                $('#globalModal').modal('hide');
                Swal.fire('Berhasil', res.message, 'success');
                $('#faq-table').DataTable().ajax.reload(null, false);
            },
            error: function (xhr) {
                let res = xhr.responseJSON;
                let msg = res?.message || 'Terjadi kesalahan.';
                Swal.fire('Gagal', msg, 'error');
            },
            complete: function () {
                form.find('button[type=submit]').prop('disabled', false);
            }
        });
    });
</script>