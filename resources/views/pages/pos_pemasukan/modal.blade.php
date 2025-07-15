<x-adminlte-modal id="modalSettingNominalBulan" title="Setting Nominal Bulan" size="md" icon="fas fa-md fa-cog">
    <p>Silahkan setting nominal untuk tiap bulan</p>
    <form id="formDataSettingNominalBulan" name="formDataSettingNominalBulan">
        @csrf
        <input type="hidden" name="id_pos_pemasukan" id="id_pos_pemasukan" value="{{ $data->id ?? '' }}">
        @foreach($tagihanBulan as $item)
        <table style="width: 100%;">
            <tr>
                <td style="width: 30%">{{ $item->bulan->nama_bulan }}</td>
                <td style="width: 70%">
                    <x-adminlte-input type="number" name="nominal_bulan[]" id="nominal_bulan_{{ $item->bulan->id }}" value="{{ old('nominal_bulan.'.$item->bulan->id, number_format($item->nominal, 0, ',', '')) }}" class="text-right"
                        placeholder="Tulis Nominal Setting Nominal Bulan" size="lg" theme="primary" icon="fas fa-lg fa-cog" disable-feedback enable-old-support error-key/>
                    <input type="hidden" name="bulan_id[]" value="{{ $item->bulan->id }}">
                </td>
            </tr>
        </table>
        @endforeach
    </form>
    <x-slot name="footerSlot">
        <div class="d-flex justify-content-between">
            <x-adminlte-button theme="danger" label="Tutup" data-dismiss="modal" />
            <x-adminlte-button form="formDataSettingNominalBulan" class="btn-flat" type="submit" label="Simpan" theme="success" icon="fas fa-lg fa-save"/>
        </div>
    </x-slot>
</x-adminlte-modal>

<script>
    $('#modalSettingNominalBulan').modal('show');
</script>
<script>
    $('#formDataSettingNominalBulan').submit(function(e) {
        e.preventDefault();
        let form = $(this);
        $.ajax({
            url: "{{ route('pos_pemasukan.store_nominal_bulan') }}",
            type: 'POST',
            data: form.serialize(),
            success: function(response) {
                if (response.success) {
                    Swal.fire('Berhasil', response.message, 'success');
                    $('#modalSettingNominalBulan').modal('hide');
                } else {
                    Swal.fire('Gagal', response.message, 'error');
                }
            },
            error: function(xhr, status, error) {
                Swal.fire('Terjadi Kesalahan', error, 'error');
            }
        });
    });
</script>
