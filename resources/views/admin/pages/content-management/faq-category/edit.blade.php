<form id="form-create-faq-category" action="{{ route('faq.category.update', $faqCategory->id) }}" method="POST">
    @csrf
    @method('PUT')
    <div class="modal-header">
        <h5 class="modal-title">Tambah FAQ Category</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
    </div>
    <div class="modal-body">
        <div class="mb-3">
            <label for="name" class="form-label">Nama Kategori</label>
            <input type="text" class="form-control" id="name" value="{{ $faqCategory->name }}" name="name"
                placeholder="Masukkan nama kategori" required>
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-link" data-bs-dismiss="modal">Cancel</button>
        <button type="submit" class="btn btn-primary">Save</button>
    </div>
</form>

<script>
    $('#form-create-faq-category').on('submit', function (e) {
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
                $('#faq-category-table').DataTable().ajax.reload(null, false);
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