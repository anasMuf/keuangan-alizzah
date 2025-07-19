<x-adminlte-modal id="modalJenjangPosPemaukanDetail" title="Setting Item Jenjang Pos Pemasukan" size="md" icon="fas fa-md fa-cog">
    <p>Tambahkan Item untuk Pos Pemasukan {{ $pos_pemasukan->nama_pos_pemasukan.' untuk jenjang '.$pos_pemasukan->jenjang_pos_pemasukan[0]->jenjang->nama_jenjang }}</p>
    <form id="formDataJenjangPosPemaukanDetail" name="formDataJenjangPosPemaukanDetail">
        @csrf
        <input type="hidden" name="id_pos_pemasukan" id="id_pos_pemasukan" value="{{ $pos_pemasukan->id ?? '' }}">
        <input type="hidden" name="jenjang_pos_pemasukan_id" id="jenjang_pos_pemasukan_id" value="{{ $pos_pemasukan->jenjang_pos_pemasukan[0]->id ?? '' }}">
        {{-- jenis_kelamin --}}
        <x-adminlte-select name="jenis_kelamin" label="Jenis Kelamin (kosongkan jika tidak ada)" fgroup-class="col-md-12">
            <x-adminlte-options :options="$jenisKelamin"
            :selected="$data ? old('jenis_kelamin',$jenisKelaminSelected) : old('jenis_kelamin')" empty-option=".:: Pilih Jenis Kelamin ::."/>
        </x-adminlte-select>

        <x-adminlte-select name="is_nominal_bulanan" label="Jenis Pembayaran Nominal" fgroup-class="col-md-12">
            <x-adminlte-options :options="['bulanan'=>'Bulanan','nonbulan'=>'Non-Bulan']"
            :selected="$data ? old('is_nominal_bulanan',$isNominalbulananSelected) : old('is_nominal_bulanan')" empty-option=".:: Pilih Jenis Pembayaran Nominal ::."/>
        </x-adminlte-select>

        <x-adminlte-input type="number" min="0" step="500" name="nominal_valid" label="Nominal" placeholder="Tulis Nominal" id="nominal_valid"
            fgroup-class="col-md-12" disable-feedback enable-old-support error-key/>

        <div id="nominal_valid_bulanan">
            @foreach($bulan as $key => $item)
            <table style="width: 100%;">
                <tr>
                    <td style="width: 30%">{{ $item->nama_bulan }}</td>
                    <td style="width: 70%">
                        <x-adminlte-input type="number" name="bulan[{{ $key }}][nominal]" id="nominal_bulan_{{ $item->id }}" value="{{ old('nominal_bulan.'.$item->id,0) }}" class="text-right"
                            placeholder="Tulis Nominal Setting Nominal Bulan" size="lg" theme="primary" icon="fas fa-lg fa-cog" disable-feedback enable-old-support error-key/>
                        <input type="hidden" name="bulan[{{ $key }}][id]" value="{{ $item->id }}">
                    </td>
                </tr>
            </table>
            @endforeach
        </div>
    </form>
    <x-slot name="footerSlot">
        <div class="d-flex justify-content-between">
            <x-adminlte-button theme="danger" label="Tutup" data-dismiss="modal" />
            <x-adminlte-button form="formDataJenjangPosPemaukanDetail" class="btn-flat" type="submit" label="Simpan" theme="success" icon="fas fa-lg fa-save"/>
        </div>
    </x-slot>
</x-adminlte-modal>

<script>
    $('#modalJenjangPosPemaukanDetail').modal('show');

    $(() => {
        $('#nominal_valid').parents('.form-group').hide();
        $('#nominal_valid_bulanan').hide();
    })

    $('#is_nominal_bulanan').change(function() {
        if ($(this).val() === 'bulanan') {
            $('#nominal_valid').parents('.form-group').hide();
            $('#nominal_valid_bulanan').show();
        } else if ($(this).val() === 'nonbulan') {
            $('#nominal_valid').parents('.form-group').show();
            $('#nominal_valid_bulanan').hide();
        } else {
            $('#nominal_valid').parents('.form-group').hide();
            $('#nominal_valid_bulanan').hide();
        }
    })

    $('#formDataJenjangPosPemaukanDetail').submit(function(e) {
        e.preventDefault();
        const formData = $(this).serialize();
        const idPosPemasukan = $('#id_pos_pemasukan').val();

        $.ajax({
            url: "{{ route('pos_pemasukan.storeJenjangPosPemasukanDetail') }}",
            type: 'POST',
            data: formData,
            success: function(response) {
                if (response.success) {
                    $('#modalJenjangPosPemaukanDetail').modal('hide');
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
