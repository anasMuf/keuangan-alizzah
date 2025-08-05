@extends('layouts.app')

{{-- Customize layout sections --}}

@section('subtitle', 'Ekstrakulikuler Siswa')
@section('content_header_title', 'Home')
@section('content_header_subtitle', 'Ekstrakulikuler Siswa')
@section('plugins.Datatables', true)
@section('plugins.Select2', true)

{{-- Content body: main page content --}}

@section('content_body')

<x-adminlte-card title="Data Ekstrakulikuler Siswa">

    @if (session('message'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <strong>Sukses! </strong>{{ session('message') }}
        </div>
    @endif

    @if (session('warning'))
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
            <strong>Peringatan! </strong>{{ session('warning') }}
        </div>
    @endif
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="action mb-3 row">
        <div class="col-md-6 action-list d-flex align-items-center">
            @if($siswaKelas)
            <a href="{{ route('siswa_ekstrakulikuler.form',['siswa_id' => $siswaKelas->siswa_id]) }}" class="btn btn-primary">Tambah Ekstrakulikuler</a>
            @endif
        </div>
        <div class="col-md-6 filter">
            <form action="{{ route('siswa_ekstrakulikuler.main') }}" method="get" class="d-flex justify-content-around" id="filter">
                <div class="form-group">
                    <label for="siswa_id" class="mr-2">Siswa:</label>
                    <select name="siswa_id" id="siswa_id" class="form-control select2">
                        <option value="">.:: Pilih Siswa :..</option>
                        @foreach($siswa_kelas as $sk)
                            <option value="{{ $sk->siswa->id }}" {{ request('siswa_id') == $sk->siswa->id ? 'selected' : '' }}>{{ $sk->siswa->nama_lengkap }}</option>
                        @endforeach
                    </select>
                </div>
            </form>
        </div>
    </div>
    <div class="detail-siswa"></div>
        @if($siswaKelas)
            <h5>Detail Siswa</h5>
            <p><strong>Nama:</strong> {{ $siswaKelas->siswa->nama_lengkap }}</p>
            <p><strong>Kelas:</strong> {{ $siswaKelas->kelas->nama_kelas }}</p>
            <p><strong>Tahun Ajaran:</strong> {{ $siswaKelas->tahun_ajaran->nama_tahun_ajaran }}</p>
        @endif
    </div>
    <x-adminlte-datatable id="table1" :heads="$heads" :config="$config">
        @foreach($config['data'] as $row)
            <tr>
                @foreach($row as $cell)
                    <td>{!! $cell !!}</td>
                @endforeach
            </tr>
        @endforeach
    </x-adminlte-datatable>
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
        $('.select2').select2({
            theme: 'bootstrap4',
            placeholder: 'Pilih Siswa',
            // width: '100%',
            allowClear: true
        });
        $('#siswa_id').change(() => $('#filter').submit())

        function deleteData(id,nama){
            Swal.fire({
                title: "Data akan dihapus!",
                text: "Apakah Anda yakin data "+nama+" dihapus?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Tidak',
                confirmButtonColor: "#d33",
            }).then(function(result) {
                if (result.isConfirmed) {

                    $.ajax({
                        type: "delete",
                        url: "/siswa-ekstrakulikuler/delete/"+id,
                        data: {
                            "_token": "{{ csrf_token() }}"
                        },
                        dataType: "json",
                        success: function(response) {
                            if (response.success) {
                                let timer = 1500
                                Swal.fire({
                                    title: "Sukses",
                                    text: response.message,
                                    icon: "success",
                                    showConfirmButton: false,
                                    timer: timer
                                });
                                setTimeout(() => {
                                    location.href = "/siswa-ekstrakulikuler?siswa_id="+$('#siswa_id').val();
                                }, timer);
                            }else{
                                Swal.fire('Peringatan', response.message, 'warning')
                            }
                        },
                        error: function(xhr, status, error) {
                            Swal.fire('Terjadi Kesalahan', error, 'error')
                        }
                    });
                }
            });
        }

    </script>
@endpush
