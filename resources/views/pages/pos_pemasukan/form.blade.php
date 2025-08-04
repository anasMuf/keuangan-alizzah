@extends('layouts.app')

{{-- Customize layout sections --}}

@section('subtitle', 'Pos Pemasukan')
@section('content_header_title', 'Home')
@section('content_header_subtitle', 'Pos Pemasukan')
@section('plugins.Select2', true)

{{-- Content body: main page content --}}

@section('content_body')
<div class="row">
    <div class="col-{{ $data && $data->is_nominal_varian ? '6' : '12' }}">
        <x-adminlte-card title="Form Pos Pemasukan">
            <form action="" method="post" id="formData">
                @csrf
                <input type="hidden" name="id_pos_pemasukan" id="id_pos_pemasukan" value="{{ $data ? old('id_pos_pemasukan',$data->id) : old('id_pos_pemasukan') }}">
                <div class="row">
                    <h5 class="col-12">Untuk Jenjang</h5>
                    <x-adminlte-select2 name="jenjang_id[]" id="jenjang_id" label="Jenjang" fgroup-class="col-md-6" :config="$config" multiple="multiple">
                        <x-adminlte-options :options="$jenjang"
                        :selected="$data ? old('jenjang_id',$jenjangSelected) : old('jenjang_id')"
                        />
                    </x-adminlte-select2>

                    <x-adminlte-select name="tahun_ajaran_id" label="Tahun Ajaran" fgroup-class="col-md-6">
                        <x-adminlte-options :options="$tahun_ajaran"
                        :selected="$data ? old('tahun_ajaran_id',$tahunAjaranSelected) : old('tahun_ajaran_id')" empty-option=".:: Pilih Tahun Ajaran ::."/>
                    </x-adminlte-select>
                </div>
                <div class="row">
                    <h5 class="col-12">Informasi Pos Pemasukan</h5>
                    <div class="col-12">
                        <div class="row mb-3">
                            <x-adminlte-select name="jenis" label="Jenis Pos Pemasukan" fgroup-class="col-md-12">
                                <x-adminlte-options :options="$jenis"
                                :selected="$data ? old('jenis',$jenisSelected) : old('jenis')" empty-option=".:: Pilih Jenis Pos Pemasukan ::."/>
                            </x-adminlte-select>

                            <div class="form-group col-md-12 d-flex align-items-center mb-2 pt-0">
                                <label for="optional">
                                    <input type="checkbox" name="optional" id="optional" {{ $data && $data->optional ? 'checked' : '' }} />
                                    extra/pasta/daycare?
                                </label>
                            </div>
                            <hr>

                            <x-adminlte-select name="pembayaran" label="Pembayaran" fgroup-class="col-md-12" id="pembayaran">
                                <x-adminlte-options :options="$pembayaran"
                                :selected="$data ? old('pembayaran',$pembayaranSelected) : old('pembayaran')" empty-option=".:: Pilih Pembayaran ::."/>
                            </x-adminlte-select>

                            <div class="form-group col-md-12 d-flex align-items-center mb-0 pt-0">
                                <label for="hari_aktif">
                                    <input type="checkbox" name="hari_aktif" id="hari_aktif" {{ $data && $data->hari_aktif ? 'checked' : '' }} disabled />
                                    nominal sesuai jumlah hari dalam sebulan?
                                </label>
                            </div>
                            <hr>
                        </div>
                    </div>

                    <x-adminlte-input name="nama_pos_pemasukan" label="Nama Pos Pemasukan" placeholder="Tulis Nama Pos Pemasukan" id="nama_pos_pemasukan"
                        fgroup-class="col-md-12" disable-feedback enable-old-support error-key value="{{ $data ? old('nama_pos_pemasukan',$data->nama_pos_pemasukan) : old('nama_pos_pemasukan') }}"/>

                    <div class="form-group col-md-12 d-flex align-items-center mb-0 pt-0">
                        <label for="tabungan">
                            <input type="checkbox" name="tabungan" id="tabungan" {{ $data && $data->tabungan ? 'checked' : '' }} />
                            tabungan?
                        </label>
                    </div>
                    <hr>

                    <div class="col-12">
                        <div class="row">
                            <div class="form-group col-md-12 d-flex align-items-center mb-0 pt-4">
                                <label for="nominal_perjenjang">
                                    <input type="checkbox" name="nominal_perjenjang" id="nominal_perjenjang" {{ $data && $data->is_nominal_varian ? 'checked' : '' }} />
                                    nominal perjenjang?
                                </label>
                            </div>

                            @if ($data && !$data->hari_aktif)
                            <x-adminlte-input type="number" min="0" step="500" name="nominal_valid" label="Nominal" placeholder="Tulis Nominal" id="nominal_valid"
                                fgroup-class="col-md-12" disable-feedback enable-old-support error-key value="{{ $data ? old('nominal_valid',number_format($data->nominal_valid,0,',','')) : old('nominal_valid') }}"/>
                            @else
                                <x-adminlte-input type="number" min="0" step="500" name="nominal_valid" label="Nominal" placeholder="Tulis Nominal" id="nominal_valid"
                                    fgroup-class="col-md-12" disable-feedback enable-old-support error-key value="{{ $data ? old('nominal_valid',number_format($data->nominal_valid,0,',','')) : old('nominal_valid') }}"/>
                            @endif
                        </div>
                    </div>
                </div>
            </form>

            <a href="{{ route('pos_pemasukan.main') }}" class="btn btn-default btn-flat">Kembali</a>

            <x-adminlte-button form="formData" class="btn-flat" type="submit" label="Submit" theme="success" icon="fas fa-lg fa-save" id="submitButton"/>
        </x-adminlte-card>

        @if ($data)
        <button class="btn btn-primary" onclick="syncPosPemasukanTagihanSiswa({{ $data->id }}, '{{ $data->nama_pos_pemasukan }}')">Update Nominal di Tagihan</button>
        @endif
    </div>
    @if($data && $data->is_nominal_varian)
    <div class="col-6">
        <x-adminlte-card title="Detail Pos Pemasukan" class="detail-pos-pemasukan" >
            <div class="jenjang-list">
                @foreach ($data->jenjang_pos_pemasukan as $itemJenjangPosPemasukan)
                    <div class="card jenjang-item">
                        <div class="card-body">
                            <strong>Jenjang:</strong> {{ $itemJenjangPosPemasukan->jenjang->nama_jenjang }} <button class="btn btn-sm btn-primary" onclick="addItemDetailJenjang({{ $data->id }},{{ $itemJenjangPosPemasukan->id }})">Tambah</button><br>
                            @foreach ($itemJenjangPosPemasukan->jenjang_pos_pemasukan_detail as $key => $itemJenjangPosPemasukanDetail)
                                @if($itemJenjangPosPemasukanDetail->jenis_kelamin)
                                    <div class="form-group item-is-selected" data-jenis-kelamin-select="jenis_kelamin_select_{{ $itemJenjangPosPemasukan->id }}">
                                        <label>Jenis Kelamin</label>
                                        <p>{{ $itemJenjangPosPemasukanDetail->jenis_kelamin }}</p>
                                    </div>
                                @endif
                                @if($itemJenjangPosPemasukanDetail->is_nominal_bulan && $itemJenjangPosPemasukanDetail->jenjang_pos_pemasukan_nominal)
                                    @foreach ($itemJenjangPosPemasukanDetail->jenjang_pos_pemasukan_nominal as $itemNominal)
                                        <table style="width: 100%; ">
                                            <tr>
                                                <td style="width: 70%">{{ $itemNominal->bulan->nama_bulan }}</td>
                                                <td style="width: 20%">Rp {{ number_format($itemNominal->nominal,0,',','.') }}</td>
                                                <td style="width: 10%"><button class="btn btn-sm btn-warning" onclick="editItemNominal({{ $itemNominal->id }})">edit</button></td>
                                            </tr>
                                        </table>
                                    @endforeach

                                @else
                                <div class="form-group">
                                    <label>Nominal</label>
                                    <p>Rp {{ number_format($itemJenjangPosPemasukanDetail->nominal,0,',','.') }}</p>
                                </div>
                                <hr>
                                @endif
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        </x-adminlte-card>
    </div>
    <div class="modal-item-detail-jenjang"></div>
    @endif
</div>
@stop

{{-- Push extra CSS --}}

@push('css')
    {{-- Add here extra stylesheets --}}
    <link rel="stylesheet" href="{{ asset('dist/css/style.css') }}">
@endpush

{{-- Push extra scripts --}}

@push('js')
    <script>
        $(document).ready(function () {
            $("#nominal_perjenjang").change(function () {
                toggleMasaAktif();
            });
            $("#pembayaran").change(function () {
                togglePembayaranBulanan($(this).val());
            });

            toggleMasaAktif();
            togglePembayaranBulanan();
        });

        function togglePembayaranBulanan(val) {
            if (val == 'bulanan') {
                $('#hari_aktif').prop('disabled', false);
            }else{
                $('#hari_aktif').prop('checked', false);
                $('#hari_aktif').prop('disabled', true);
            }
        }

        function toggleMasaAktif() {
            if ($('#nominal_perjenjang').is(':checked')) {
                $('#nominal_valid').prop('disabled', true);
                $('.detail-pos-pemasukan').show()
            } else {
                $('#nominal_valid').prop('disabled', false);
                $('.detail-pos-pemasukan').hide()
            }
        }

        function addItemDetailJenjang(pos_pemasukan_id,jenjang_pos_pemasukan_id) {
            $.get("{{ route('pos_pemasukan.formJenjangPosPemasukanDetail') }}", {
                pos_pemasukan_id: pos_pemasukan_id,
                jenjang_pos_pemasukan_id: jenjang_pos_pemasukan_id
            })
            .done(function (response) {
                if(response.success){
                    $('.modal-item-detail-jenjang').html(response.result);
                }else{
                    Swal.fire('Peringatan', response.message, 'warning')
                }
            })
            .fail(function(xhr,status,error){
                Swal.fire('Terjadi Kesalahan', error, 'error')
            });
        }

        function editItemNominal(jenjang_pos_pemasukan_nominal_id){
            $.get("{{ route('pos_pemasukan.formJenjangPosPemasukanNominal') }}", {
                jenjang_pos_pemasukan_nominal_id: jenjang_pos_pemasukan_nominal_id
            })
            .done(function (response) {
                if(response.success){
                    $('.modal-item-detail-jenjang').html(response.result);
                }else{
                    Swal.fire('Peringatan', response.message, 'warning')
                }
            })
            .fail(function(xhr,status,error){
                Swal.fire('Terjadi Kesalahan', error, 'error')
            });
        }

        function syncPosPemasukanTagihanSiswa(pos_pemasukan_id,nama_pos_pemasukan) {
            Swal.fire({
                title: "Data tagihan akan diupdate!",
                text: "Apakah Anda yakin data pos pemasukan "+nama_pos_pemasukan+" diupdate?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: 'Ya, Update!',
                cancelButtonText: 'Tidak',
            }).then(function(result) {
                if (result.isConfirmed) {
                    $.ajax({
                        type: "put",
                        url: "/pos_pemasukan/sync-tagihan?pos_pemasukan_id="+pos_pemasukan_id,
                        data: {
                            _token: "{{ csrf_token() }}"
                        },
                        dataType: "json",
                        success: function (response) {
                            if(response.success){
                                Swal.fire('Sukses', response.message, 'success')
                            }else{
                                Swal.fire('Peringatan', response.message, 'warning')
                            }
                        },
                        error: function (xhr, status, error) {
                            Swal.fire('Terjadi Kesalahan', error, 'error')
                        }
                    });
                }
            });
        }
    </script>
    <script>
        $('#formData').submit(function (e) {
            e.preventDefault();
            const data = $('#formData').serialize()
            $.post("{{ route('pos_pemasukan.store') }}", data)
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
                        location.href = "{{ route('pos_pemasukan.main') }}"
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
