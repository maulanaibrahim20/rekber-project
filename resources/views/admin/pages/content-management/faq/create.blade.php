<form id="form-create-faq" action="{{ route('faq.store', $category->slug) }}" method="POST">
    @csrf
    <div class="modal-header">
        <h5 class="modal-title">Tambah FAQ Category</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
    </div>
    <div class="modal-body">
        <div class="mb-3">
            <label for="name" class="form-label">Question</label>
            <input type="text" class="form-control" id="question" name="question" placeholder="Masukkan pertanyaan"
                required>
        </div>
        <div class="mb-3">
            <label for="name" class="form-label">Answer</label>
            <input type="text" class="form-control" id="answer" name="answer" placeholder="Masukkan jawaban" required>
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
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-link" data-bs-dismiss="modal">Cancel</button>
        <button type="submit" class="btn btn-primary">Save</button>
    </div>
    <input type="hidden" name="category_id" value="{{ $category->id }}">
</form>

<script>
    $('#form-create-faq').on('submit', function (e) {
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