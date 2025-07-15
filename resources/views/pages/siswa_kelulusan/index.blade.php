@extends('layouts.app')

{{-- Customize layout sections --}}

@section('subtitle', 'Siswa Kelulusan')
@section('content_header_title', 'Home')
@section('content_header_subtitle', 'Siswa Kelulusan')
@section('plugins.DualListbox', true)

{{-- Content body: main page content --}}

@section('content_body')

<x-adminlte-card title="Form Siswa Kelulusan">
    <form action="" method="post" id="formData">
        @csrf
        <div class="row">
            <x-adminlte-select name="tahun_ajaran_awal" id="tahun_ajaran_awal" label="Tahun Ajaran Awal" fgroup-class="col-md-6" required>
                <x-adminlte-options :options="$tahun_ajaran"
                selected="{{ old('tahun_ajaran_awal', $tahunAjaranAwal ?? '') }}" empty-option=".:: Pilih Tahun Ajaran Awal ::." />
            </x-adminlte-select>

            <x-adminlte-select name="tahun_ajaran_tujuan" id="tahun_ajaran_tujuan" label="Tahun Ajaran Tujuan" fgroup-class="col-md-6" required>
                <x-adminlte-options :options="$tahun_ajaran" :disabled="$tahunAjaranDisabled"
                selected="{{ old('tahun_ajaran_tujuan', $tahunAjaranTujuan ?? '') }}" empty-option=".:: Pilih Tahun Ajaran Tujuan ::." />
            </x-adminlte-select>
        </div>

        <div class="row">
            <x-adminlte-select name="kelas_awal" label="Kelas Awal" fgroup-class="col-md-6">
                <x-adminlte-options :options="$kelas"
                selected="{{ old('kelas_awal') }}" empty-option=".:: Pilih Kelas Awal ::."/>
            </x-adminlte-select>
        </div>

        {{-- <div class="row mt-3">
            <div class="col-md-12">
                <div class="form-group">
                    <label>Status Siswa di Kelas Tujuan</label>
                    <select class="form-control" name="status" id="status">
                        <option value="aktif">Aktif</option>
                        <option value="nonaktif">Nonaktif</option>
                        <option value="alumni">Alumni</option>
                    </select>
                </div>
            </div>
        </div> --}}
        <div class="row">
            <div class="col-md-12">
                <label for="pilih_siswa">Pilih Siswa:</label>
                <select name="siswa_ids[]" class="duallistbox" id="pilih_siswa" multiple="multiple">
                    {{-- @foreach ($siswa as $item)
                        <option value="{{ $item->id }}">{{ $item->nama_lengkap }}</option>
                    @endforeach --}}
                </select>
            </div>
        </div>
    </form>

    <x-adminlte-button form="formData" class="btn-flat" type="submit" label="Submit" theme="success" icon="fas fa-lg fa-save" id="submitButton"/>
</x-adminlte-card>
@stop

{{-- Push extra CSS --}}

@push('css')
    {{-- Add here extra stylesheets --}}
    <link rel="stylesheet" href="{{ asset('dist/css/style.css') }}">
@endpush

{{-- Push extra scripts --}}

@push('js')
    <script>
        //Bootstrap Duallistbox
        $('.duallistbox').bootstrapDualListbox({
            infoTextFiltered: '<span class="badge badge-warning">Difilter</span> {0} dari {1}',
            infoText: 'Total {0}',
            infoTextEmpty: 'Tidak ada data',
            filterTextClear: 'Tampilkan semua',
            filterPlaceHolder: 'Filter',
            moveSelectedLabel: 'Pindahkan terpilih',
            moveAllLabel: 'Pindahkan semua',
            removeSelectedLabel: 'Hapus terpilih',
            removeAllLabel: 'Hapus semua',
            moveOnSelect: true,
            preserveSelectionOnMove: 'moved',
            selectedListLabel: 'Siswa Terpilih',
            nonSelectedListLabel: 'Siswa Tersedia',
            selectorMinimalHeight: 300
        })
        // Event handler ketika kelas sumber berubah
        $('#kelas_awal, #tahun_ajaran_awal').on('change', function() {
            var kelasId = $('#kelas_awal').val();
            var tahunAjaranId = $('#tahun_ajaran_awal').val();

            // Reset pilih_siswa jika kelas belum dipilih
            if (!kelasId || !tahunAjaranId) {
                $('#pilih_siswa').empty().bootstrapDualListbox('refresh');
                return;
            }

            // Ambil data siswa dari kelas yang dipilih
            $.ajax({
                url: "{{ route('siswa_kelas.byKelas') }}",
                type: "GET",
                data: {
                    kelas_id: kelasId,
                    tahun_ajaran_id: tahunAjaranId
                },
                success: function(response) {
                    $('#pilih_siswa').empty();

                    if (response.data.length > 0) {
                        // Tambahkan opsi untuk setiap siswa
                        $.each(response.data, function(index, value) {
                            const siswa = value.siswa;
                            $('#pilih_siswa').append(
                                $('<option></option>')
                                    .attr('value', siswa.id)
                                    .text(siswa.nama_lengkap /* + ' (' + siswa.nis + ')'*/)
                            );
                        });
                    } else {
                        // Tampilkan pesan jika tidak ada siswa
                        $('#pilih_siswa').append(
                            $('<option></option>')
                                .attr('disabled', true)
                                .text('Tidak ada siswa di kelas ini')
                        );
                    }

                    // Refresh dual listbox
                    $('#pilih_siswa').bootstrapDualListbox('refresh');
                },
                error: function(xhr, status, error) {
                    console.error("Error:", error);
                    Swal.fire({
                        title: 'Error!',
                        text: 'Gagal mengambil data siswa',
                        icon: 'error'
                    });
                }
            });
        });
    </script>
    <script>
        $('#formData').submit(function (e) {
            e.preventDefault();
            var selectedSiswa = $('#pilih_siswa').val();

            if (!$('#tahun_ajaran_awal').val()) {
                Swal.fire({
                    title: 'Perhatian!',
                    text: 'Tahun ajaran harus dipilih',
                    icon: 'warning'
                });
                return false;
            }

            if (!selectedSiswa || selectedSiswa.length === 0) {
                Swal.fire({
                    title: 'Perhatian!',
                    text: 'Pilih minimal satu siswa',
                    icon: 'warning'
                });
                return false;
            }

            const data = $('#formData').serialize()
            $.post("{{ route('siswa_kelulusan.store') }}", data)
            .done(function (response) {
                if(response.success){
                    let timer = 1500
                    Swal.fire({
                        title: "Sukses",
                        text: response.message,
                        icon: "success",
                        showConfirmButton: false,
                        timer: timer
                    })
                    setTimeout(() => {
                        location.href = "{{ route('siswa_kelulusan.main') }}"
                    }, timer);
                }else{
                    let message = response.message;
                        $.each(response.message_validation, function (i,msg) {
                            message += msg[0]+', <br>';
                        })
                    Swal.fire('Peringatan', message, 'warning')
                }
            })
            .fail(function(xhr,status,error){
                Swal.fire('Terjadi Kesalahan', error, 'error')
            });
        });
    </script>
@endpush
