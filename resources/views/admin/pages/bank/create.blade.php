<form action="{{ route('bank.store') }}" method="POST" id="bank-form">
    @csrf
    <div class="modal-body">
        <div class="mb-3">
            <label class="form-label">Bank</label>
            <select name="bank_code" class="form-select" id="bank-select" data-placeholder="-- Pilih Bank --">
                <option value="" disabled>-- Pilih Bank --</option>
                @foreach ($banks as $bank)
                    <option value="{{ $bank['code'] }}" data-name="{{ $bank['name'] }}"
                        data-can_disburse="{{ $bank['can_disburse'] }}"
                        data-can_name_check="{{ $bank['can_name_validate'] }}">
                        {{ $bank['name'] }}
                        ({{ $bank['can_disburse'] ? 'Bisa Disburse' : 'Tidak Bisa Disburse' }})
                    </option>
                @endforeach
            </select>
        </div>
        <div class="form-check">
            <input type="checkbox" class="form-check-input" name="status" value="1" id="status">
            <label class="form-check-label" for="status">Status</label>
        </div>
    </div>
    <div class="modal-footer">
        <button type="submit" class="btn btn-primary">Simpan</button>
    </div>

    <input type="hidden" name="bank_name" id="bank_name">
    <input type="hidden" name="can_disburse" id="can_disburse">
    <input type="hidden" name="can_name_check" id="can_name_check">
</form>

<script>
    $('#bank-select').on('change', function () {
        let selected = $(this).find('option:selected');
        $('#bank_name').val(selected.data('name'));
        $('#can_disburse').val(selected.data('can_disburse') ? 1 : 0);
        $('#can_name_check').val(selected.data('can_name_check') ? 1 : 0);
    });
</script>
<script>
    $('#bank-form').on('submit', function (e) {
        e.preventDefault();
        let form = $(this);
        $.ajax({
            url: form.attr('action'),
            method: 'POST',
            data: form.serialize(),
            success: function (response) {
                $('#globalModal').modal('hide');
                $('#bank-table').DataTable().ajax.reload(null, false);
                Swal.fire('Berhasil', response.message, 'success');
            },
            error: function (xhr) {
                Swal.fire('Gagal', xhr.responseJSON?.message || 'Terjadi kesalahan.', 'error');
            }
        });
    });
</script>
<script>
    $(document).ready(function () {
        $('#bank-select').select2({
            dropdownParent: $('#globalModal'),
            placeholder: '-- Pilih Bank --',
            allowClear: true,
            width: '100%'
        });

        $('#bank-select').on('change', function () {
            let selected = $(this).find('option:selected');
            $('#bank_name').val(selected.data('name'));
            $('#can_disburse').val(selected.data('can_disburse') ? 1 : 0);
            $('#can_name_check').val(selected.data('can_name_check') ? 1 : 0);
        });
    });
</script>