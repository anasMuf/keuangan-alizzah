@extends('layouts.app')

{{-- Customize layout sections --}}

@section('subtitle', 'Siswa')
@section('content_header_title', 'Home')
@section('content_header_subtitle', 'Siswa')

{{-- Content body: main page content --}}

@section('content_body')

<x-adminlte-card title="Form Siswa">
    <form action="" method="post" id="formData">
        @csrf
        <input type="hidden" name="id_siswa" id="id_siswa" value="{{ $data ? old('id_siswa',$data->id) : old('id_siswa') }}">
        <div class="row">
            <x-adminlte-input name="nama_lengkap" label="Nama Lengkap" placeholder="Tulis Nama Lengkap (contoh: Ahmad Budi Santoso)" id="nama_lengkap"
                fgroup-class="col-md-6" disable-feedback enable-old-support error-key value="{{ $data ? old('nama_lengkap',$data->nama_lengkap) : old('nama_lengkap') }}"/>
            <x-adminlte-input name="nama_panggilan" label="Nama Panggilan" placeholder="Tulis Nama Panggilan (contoh: Budi)" id="nama_panggilan"
                fgroup-class="col-md-6" disable-feedback enable-old-support error-key value="{{ $data ? old('nama_panggilan',$data->nama_panggilan) : old('nama_panggilan') }}"/>
        </div>
        <div class="row">
            <x-adminlte-input name="nik" label="No. KTP" placeholder="Tulis No. KTP (contoh: 3273010101990001)" id="nik" maxLength="16"
                fgroup-class="col-md-6" disable-feedback enable-old-support error-key value="{{ $data ? old('nik',$data->nik) : old('nik') }}">
                <x-slot name="bottomSlot">
                    <small id="nik-error" style="color: red"></small>
                </x-slot>
            </x-adminlte-input>
            {{-- jenis_kelamin --}}
            <x-adminlte-select name="jenis_kelamin" label="Jenis Kelamin" fgroup-class="col-md-6">
                <x-adminlte-options :options="$jenisKelamin"
                :selected="$data ? old('jenis_kelamin',$jenisKelaminSelected) : old('jenis_kelamin')" empty-option=".:: Pilih Jenis Kelamin ::."/>
            </x-adminlte-select>
            {{-- tempat_lahir --}}
            <x-adminlte-input name="tempat_lahir" label="Tempat Lahir" placeholder="Tulis Tempat Lahir (contoh: Jakarta)" id="tempat_lahir"
                fgroup-class="col-md-6" disable-feedback enable-old-support error-key value="{{ $data ? old('tempat_lahir',$data->tempat_lahir) : old('tempat_lahir') }}"/>
            {{-- tanggal_lahir --}}
            <x-adminlte-input type="date" name="tanggal_lahir" label="Tanggal Lahir" placeholder="Tulis Tanggal Lahir (contoh: 1999-01-01)" id="tanggal_lahir"
                fgroup-class="col-md-6" disable-feedback enable-old-support error-key value="{{ $data ? old('tanggal_lahir',$data->tanggal_lahir) : old('tanggal_lahir') }}"/>
            {{-- agama --}}
            <x-adminlte-input name="agama" label="Agama" placeholder="Tulis Agama (contoh: Jl. Merdeka No. 123)" id="agama"
                fgroup-class="col-md-6" disable-feedback enable-old-support error-key value="{{ $data ? old('agama',$data->agama) : old('agama') }}"/>
        </div>
        <div class="row">
            {{-- alamat_lengkap --}}
            <x-adminlte-textarea name="alamat_lengkap" label="Alamat Lengkap" placeholder="Tulis Alamat Lengkap (contoh: Menteng)" id="alamat_lengkap"
                    fgroup-class="col-md-6" disable-feedback enable-old-support error-key>{{ $data ? old('alamat_lengkap',$data->alamat_lengkap) : old('alamat_lengkap') }}</x-adminlte-textarea>
            {{-- desa --}}
            <x-adminlte-input name="desa" label="Desa" placeholder="Tulis Desa (contoh: Menteng)" id="desa"
                    fgroup-class="col-md-6" disable-feedback enable-old-support error-key value="{{ $data ? old('desa',$data->desa) : old('desa') }}"/>
            {{-- kecamatan --}}
            <x-adminlte-input name="kecamatan" label="Kecamatan" placeholder="Tulis Kecamatan (contoh: Jakarta Pusat)" id="kecamatan"
                    fgroup-class="col-md-6" disable-feedback enable-old-support error-key value="{{ $data ? old('kecamatan',$data->kecamatan) : old('kecamatan') }}"/>
            {{-- kabupaten_kota --}}
            <x-adminlte-input name="kabupaten_kota" label="Kabupaten/Kota" placeholder="Tulis Kabupaten/Kota (contoh: DKI Jakarta)" id="kabupaten_kota"
                    fgroup-class="col-md-6" disable-feedback enable-old-support error-key value="{{ $data ? old('kabupaten_kota',$data->kabupaten_kota) : old('kabupaten_kota') }}"/>
            {{-- provinsi --}}
            <x-adminlte-input name="provinsi" label="Provinsi" placeholder="Tulis Provinsi (contoh: Islam)" id="provinsi"
                    fgroup-class="col-md-6" disable-feedback enable-old-support error-key value="{{ $data ? old('provinsi',$data->provinsi) : old('provinsi') }}"/>
            {{-- kewarganegaraan --}}
            <x-adminlte-input name="kewarganegaraan" label="Kewarganegaraan" placeholder="Tulis Kewarganegaraan (contoh: Indonesia)" id="kewarganegaraan"
                    fgroup-class="col-md-6" disable-feedback enable-old-support error-key value="{{ $data ? old('kewarganegaraan',$data->kewarganegaraan) : old('kewarganegaraan') }}"/>
        </div>
        <div class="row">
        {{-- anak_keberapa --}}
        <x-adminlte-input type="number" name="anak_keberapa" label="Anak Keberapa" placeholder="Tulis Anak Keberapa" id="anak_keberapa (contoh: 1)" min="0"
                fgroup-class="col-md-6" disable-feedback enable-old-support error-key value="{{ $data ? old('anak_keberapa',$data->anak_keberapa) : old('anak_keberapa') }}"/>
        {{-- jumlah_saudara_kandung --}}
        <x-adminlte-input type="number" name="jumlah_saudara_kandung" label="Jumlah Saudara Kandung" placeholder="Tulis Jumlah Saudara Kandung (contoh: 2)" id="jumlah_saudara_kandung" min="0"
                fgroup-class="col-md-6" disable-feedback enable-old-support error-key value="{{ $data ? old('jumlah_saudara_kandung',$data->jumlah_saudara_kandung) : old('jumlah_saudara_kandung') }}"/>
        {{-- jumlah_saudara_tiri --}}
        <x-adminlte-input type="number" name="jumlah_saudara_tiri" label="Jumlah Saudara Tiri" placeholder="Tulis Jumlah Saudara Tiri (contoh: 0)" id="jumlah_saudara_tiri" min="0"
                fgroup-class="col-md-6" disable-feedback enable-old-support error-key value="{{ $data ? old('jumlah_saudara_tiri',$data->jumlah_saudara_tiri) : old('jumlah_saudara_tiri') }}"/>
        {{-- jumlah_saudara_angkat --}}
        <x-adminlte-input type="number" name="jumlah_saudara_angkat" label="Jumlah Saudara Angkat" placeholder="Tulis Jumlah Saudara Angkat (contoh: 0)" id="jumlah_saudara_angkat" min="0"
                fgroup-class="col-md-6" disable-feedback enable-old-support error-key value="{{ $data ? old('jumlah_saudara_angkat',$data->jumlah_saudara_angkat) : old('jumlah_saudara_angkat') }}"/>
        {{-- status_orangtua --}}
        <x-adminlte-input name="status_orangtua" label="Status Orangtua" placeholder="Tulis Status Orangtua (contoh: Lengkap)" id="status_orangtua"
                fgroup-class="col-md-6" disable-feedback enable-old-support error-key value="{{ $data ? old('status_orangtua',$data->status_orangtua) : old('status_orangtua') }}"/>
        {{-- bahasa_seharihari --}}
        <x-adminlte-input name="bahasa_seharihari" label="Bahasa Seharihari" placeholder="Tulis Bahasa Seharihari (contoh: Indonesia)" id="bahasa_seharihari"
                fgroup-class="col-md-6" disable-feedback enable-old-support error-key value="{{ $data ? old('bahasa_seharihari',$data->bahasa_seharihari) : old('bahasa_seharihari') }}"/>
        {{-- golongan_darah --}}
        <x-adminlte-input name="golongan_darah" label="Golongan Darah" placeholder="Tulis Golongan Darah (contoh: O)" id="golongan_darah"
                fgroup-class="col-md-6" disable-feedback enable-old-support error-key value="{{ $data ? old('golongan_darah',$data->golongan_darah) : old('golongan_darah') }}"/>
        {{-- riwayat_penyakit --}}
        <x-adminlte-input name="riwayat_penyakit" label="Riwayat Penyakit" placeholder="Tulis Riwayat Penyakit (contoh: Tidak ada)" id="riwayat_penyakit"
                fgroup-class="col-md-6" disable-feedback enable-old-support error-key value="{{ $data ? old('riwayat_penyakit',$data->riwayat_penyakit) : old('riwayat_penyakit') }}"/>
        {{-- riwayat_imunisasi --}}
        <x-adminlte-input name="riwayat_imunisasi" label="Riwayat Imunisasi" placeholder="Tulis Riwayat Imunisasi (contoh: Lengkap)" id="riwayat_imunisasi"
                fgroup-class="col-md-6" disable-feedback enable-old-support error-key value="{{ $data ? old('riwayat_imunisasi',$data->riwayat_imunisasi) : old('riwayat_imunisasi') }}"/>
        {{-- ciri_khusus --}}
        <x-adminlte-input name="ciri_khusus" label="Ciri Khusus" placeholder="Tulis Ciri Khusus (contoh: Tinggi badan 150cm)" id="ciri_khusus"
                fgroup-class="col-md-6" disable-feedback enable-old-support error-key value="{{ $data ? old('ciri_khusus',$data->ciri_khusus) : old('ciri_khusus') }}"/>
        {{-- cita_cita --}}
        <x-adminlte-input name="cita_cita" label="Cita Cita" placeholder="Tulis Cita Cita (contoh: Dokter)" id="cita_cita"
                fgroup-class="col-md-6" disable-feedback enable-old-support error-key value="{{ $data ? old('cita_cita',$data->cita_cita) : old('cita_cita') }}"/>
        </div>
    </form>

    <a href="{{ route('siswa.main') }}" class="btn btn-default btn-flat">Kembali</a>

    <x-adminlte-button form="formData" class="btn-flat" type="submit" label="Submit" theme="success" icon="fas fa-lg fa-save" id="submitButton"/>

    @if ($data)
    <a href="{{ route('siswa_dispensasi.detail',['siswa_id' => $data->id,'from'=>'siswa']) }}" class="btn btn-default btn-flat">Dispensasi</a>
    @endif
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
        $('#nik').keyup(function () {
            validationNoKTP()
        })
        function validationNoKTP(){
            $('#nik').val($('#nik').val().replace(/[^\d]/g, ''))

            let noKtp = $('#nik').val()

            if(noKtp.length === 16){
                $('#nik-error').html('')
                return true
            }else{
                $('#nik-error').html('no ktp tidak boleh kurang dari 16 digit')
                return false
            }
        }

        $('#no_telepon').keyup(function () {
            $(this).val($(this).val().replace(/[^\d]/g, ''))
        })
    </script>
    <script>
        $('#formData').submit(function (e) {
            e.preventDefault();
            if(!validationNoKTP()){
                return Swal.fire('Peringatan','no ktp tidak sesuai','warning')
            }
            const data = $('#formData').serialize()
            $.post("{{ route('siswa.store') }}", data)
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
                        location.href = "{{ route('siswa.main') }}"
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
