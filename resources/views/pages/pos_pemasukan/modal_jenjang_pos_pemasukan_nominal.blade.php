<x-adminlte-modal id="modalJenjangPosPemaukanNominal" title="Edit Nominal Bulan" size="md" icon="fas fa-md fa-cog">
    <p>Edit Nominal Bulan {{ $data->bulan->nama_bulan }}</p>
    <form id="formDataJenjangPosPemaukanNominal" name="formDataJenjangPosPemaukanNominal">
        @csrf
        <input type="hidden" name="jenjang_pos_pemasukan_nominal_id" id="jenjang_pos_pemasukan_nominal_id" value="{{ $data->id ?? '' }}">

        <x-adminlte-input type="number" name="nominal" id="nominal_bulan" value="{{ old('nominal_bulan',number_format($data->nominal,0,',','')) }}" class="text-right"
            placeholder="Tulis Nominal Bulan" size="lg" theme="primary" icon="fas fa-lg fa-cog" disable-feedback enable-old-support error-key/>
    </form>
    <x-slot name="footerSlot">
        <div class="d-flex justify-content-between">
            <x-adminlte-button theme="danger" label="Tutup" data-dismiss="modal" />
            <x-adminlte-button form="formDataJenjangPosPemaukanNominal" class="btn-flat" type="submit" label="Simpan" theme="success" icon="fas fa-lg fa-save"/>
        </div>
    </x-slot>
</x-adminlte-modal>

<script>
    $('#modalJenjangPosPemaukanNominal').modal('show')
    $('#formDataJenjangPosPemaukanNominal').submit(function(e) {
        e.preventDefault();
        const formData = $(this).serialize();
        const jenjang_pos_pemasukan_nominal_id = $('#jenjang_pos_pemasukan_nominal_id').val();

        $.ajax({
            url: "{{ route('pos_pemasukan.updateJenjangPosPemasukanNominal') }}",
            type: 'PUT',
            data: formData,
            success: function(response) {
                if (response.success) {
                    $('#modalJenjangPosPemaukanNominal').modal('hide');
                    // Refresh the detail view or perform any other action
                    location.reload();
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: response.message || 'Gagal menyimpan data. Silakan coba lagi.'
                    });
                }
            },
            error: function(xhr) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Terjadi kesalahan saat menyimpan data.'
                });
            }
        });
    });
</script>
