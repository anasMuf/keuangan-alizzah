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
        <button class="btn btn-primary mb-3" onclick="syncPosPemasukanTagihanSiswa({{ $data->id }}, '{{ $data->nama_pos_pemasukan }}')">Update Nominal di Tagihan</button>
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
    <div class="col-12">
        @if($data->pembayaran == 'bulanan')
        <x-adminlte-card>
            {{-- filter kelas --}}
            <div class="row mb-3">
                <div class="col-md-4">
                    <label for="filterJenjang">Filter Jenjang:</label>
                    <select class="form-control" id="filterJenjang">
                        <option value="">Semua Jenjang</option>
                        @foreach($jenjangs as $id => $nama)
                            <option value="{{ $id }}">{{ $nama }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="filterKelas">Filter Kelas:</label>
                    <select class="form-control" id="filterKelas">
                        <option value="">Semua Kelas</option>
                        @foreach($kelas as $id => $nama)
                            <option value="{{ $id }}">{{ $nama }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <button class="btn btn-primary" onclick="loadTagihanSiswa()">
                        <i class="fas fa-filter"></i> Filter
                    </button>
                    <button class="btn btn-secondary ml-2" onclick="resetFilter()">
                        <i class="fas fa-undo"></i> Reset
                    </button>
                </div>
            </div>

            {{-- Loading Indicator --}}
            <div id="loadingIndicator" class="text-center" style="display: none;">
                <i class="fas fa-spinner fa-spin"></i> Memuat data...
            </div>

            {{-- Table --}}
            <table class="table table-bordered" id="listTagihanSiswa">
                <tbody>
                    <tr>
                        <td class="text-center text-muted">
                            <i class="fas fa-info-circle"></i> Klik tombol Filter untuk memuat data tagihan siswa
                        </td>
                    </tr>
                </tbody>
            </table>
        </x-adminlte-card>
        @endif
    </div>
    <div class="modal-item-detail-jenjang"></div>
    <!-- Modal Edit Nominal -->
    <div class="modal-edit-nominal">
        <div class="modal fade" id="editNominalModal" tabindex="-1" role="dialog" aria-labelledby="editNominalModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editNominalModalLabel">
                            <i class="fas fa-edit"></i> Edit Nominal Tagihan
                        </h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label><strong>Informasi Siswa:</strong></label>
                            <div class="card card-outline card-info">
                                <div class="card-body">
                                    <p class="mb-2"><strong>Nama Siswa:</strong> <span class="student-name text-primary"></span></p>
                                    <p class="mb-2"><strong>Bulan:</strong> <span class="month-name text-info"></span></p>
                                    {{-- <p class="mb-0"><strong>Status Saat Ini:</strong>
                                        <span class="current-status badge"></span>
                                    </p> --}}
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="editNominal"><strong>Nominal Baru:</strong></label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">Rp</span>
                                </div>
                                <input type="number" class="form-control" id="editNominal" min="0" step="500" placeholder="Masukkan nominal baru">
                            </div>
                            <small class="form-text text-muted">Minimal Rp 0, kelipatan Rp 500</small>
                        </div>

                        <input type="hidden" id="tagihanId">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">
                            <i class="fas fa-times"></i> Batal
                        </button>
                        <button type="button" class="btn btn-primary" id="btnUpdateNominal" onclick="updateNominalTagihan()">
                            <i class="fas fa-save"></i> Update
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
@stop

{{-- Push extra CSS --}}

@push('css')
    {{-- Add here extra stylesheets --}}
    <link rel="stylesheet" href="{{ asset('dist/css/style.css') }}">

    <style>
        #listTagihanSiswa {
            font-size: 0.85rem;
        }

        #listTagihanSiswa th {
            background-color: #f8f9fa;
            font-weight: 600;
            text-align: center;
            vertical-align: middle;
            min-width: 80px;
        }

        #listTagihanSiswa td {
            vertical-align: middle;
        }

        #listTagihanSiswa .badge {
            font-size: 0.7rem;
            margin-bottom: 2px;
        }

        .table-responsive {
            max-height: 500px;
            overflow-y: auto;
        }

        /* Fixed first columns */
        #listTagihanSiswa th:nth-child(1),
        #listTagihanSiswa th:nth-child(2),
        #listTagihanSiswa th:nth-child(3),
        #listTagihanSiswa td:nth-child(1),
        #listTagihanSiswa td:nth-child(2),
        #listTagihanSiswa td:nth-child(3) {
            position: sticky;
            left: 0;
            background-color: white;
            z-index: 1;
        }

        #listTagihanSiswa th:nth-child(2),
        #listTagihanSiswa td:nth-child(2) {
            left: 30px;
        }

        #listTagihanSiswa th:nth-child(3),
        #listTagihanSiswa td:nth-child(3) {
            left: 180px;
        }
        .nominal-clickable:hover {
            color: #0056b3 !important;
            font-weight: bold;
        }

        #editNominalModal .modal-header {
            background-color: #f8f9fa;
            border-bottom: 1px solid #dee2e6;
        }

        #editNominalModal .card {
            margin-bottom: 15px;
        }

        #editNominalModal .input-group-text {
            background-color: #e9ecef;
            border-color: #ced4da;
        }

        #editNominalModal .current-status {
            font-size: 0.8rem;
        }

        /* Status badges in modal */
        .current-status.badge {
            padding: 0.375rem 0.75rem;
        }

        .current-status:contains('lunas') {
            background-color: #28a745 !important;
        }

        .current-status:contains('cicil') {
            background-color: #ffc107 !important;
        }

        .current-status:contains('belum') {
            background-color: #dc3545 !important;
        }
    </style>
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

        function loadTagihanSiswa() {
            const posId = {{ $data ? $data->id : 0 }};
            const jenjangId = $('#filterJenjang').val();
            const kelasId = $('#filterKelas').val();

            if (!posId) {
                Swal.fire('Peringatan', 'Data pos pemasukan tidak ditemukan', 'warning');
                return;
            }

            $('#loadingIndicator').show();
            $('#listTagihanSiswa tbody').html('<tr><td class="text-center"><i class="fas fa-spinner fa-spin"></i> Memuat data...</td></tr>');

            $.ajax({
                url: `/tagihan-siswa/pos-pemasukan/${posId}`,
                type: 'GET',
                data: {
                    jenjang_id: jenjangId,
                    kelas_id: kelasId
                },
                success: function(response) {
                    $('#loadingIndicator').hide();

                    if (response.success) {
                        initializeTagihanSiswaTable(response.data);
                    } else {
                        Swal.fire('Error', 'Gagal memuat data', 'error');
                    }
                },
                error: function(xhr, status, error) {
                    $('#loadingIndicator').hide();
                    console.error(xhr.responseText);
                    Swal.fire('Error', 'Terjadi kesalahan saat memuat data', 'error');
                }
            });
        }

        function resetFilter() {
            $('#filterJenjang').val('');
            $('#filterKelas').val('');
            $('#listTagihanSiswa tbody').html('<tr><td class="text-center text-muted"><i class="fas fa-info-circle"></i> Klik tombol Filter untuk memuat data tagihan siswa</td></tr>');
        }

        function initializeTagihanSiswaTable(tagihanData = null) {
            // Define months from July to June
            const months = [
                'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember',
                'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni'
            ];

            // Create table header
            let tableHeader = '<thead><tr><th>No</th><th>Nama Siswa</th><th>Kelas</th>';
            months.forEach(month => {
                tableHeader += `<th>${month}</th>`;
            });
            tableHeader += '</tr></thead>';

            // Use provided data or empty array
            const dataToProcess = tagihanData || [];

            if (dataToProcess.length === 0) {
                $('#listTagihanSiswa').html(tableHeader + '<tbody><tr><td colspan="15" class="text-center text-muted">Tidak ada data tagihan siswa</td></tr></tbody>');
                return;
            }

            // Group data by student
            const studentData = {};
            dataToProcess.forEach(item => {
                const studentId = item.siswa_kelas.siswa.id;
                const studentName = item.siswa_kelas.siswa.nama_lengkap;
                const className = item.siswa_kelas.kelas?.nama_kelas || '';
                const monthName = item.bulan.nama_bulan;

                if (!studentData[studentId]) {
                    studentData[studentId] = {
                        name: studentName,
                        class: className,
                        months: {}
                    };
                }

                studentData[studentId].months[monthName] = {
                    nominal: item.nominal,
                    status: item.status,
                    id: item.id
                };
            });

            // Create table body
            let tableBody = '<tbody>';
            let no = 1;

            Object.keys(studentData).forEach(studentId => {
                const student = studentData[studentId];
                tableBody += `<tr>
                    <td>${no++}</td>
                    <td>${student.name}</td>
                    <td>${student.class}</td>`;

                months.forEach(month => {
                    const monthData = student.months[month];
                    if (monthData) {
                        const nominal = monthData.nominal ? `Rp ${new Intl.NumberFormat('id-ID').format(monthData.nominal)}` : '-';

                        tableBody += `<td>
                            <div class="text-center">
                                <small class="nominal-clickable" style="cursor: pointer; color: #007bff; text-decoration: underline;"
                                       onclick="showEditNominalModal(${monthData.id}, '${student.name}', '${month}', ${monthData.nominal}, '${monthData.status}')"
                                       title="Klik untuk edit nominal">
                                    ${nominal}
                                </small>
                            </div>
                        </td>`;
                    } else {
                        tableBody += `<td class="text-center">
                            <small class="badge badge-secondary">-</small>
                        </td>`;
                    }
                });

                tableBody += '</tr>';
            });

            tableBody += '</tbody>';

            // Set table content
            $('#listTagihanSiswa').html(tableHeader + tableBody);

            // Add responsive wrapper and styling
            if (!$('#listTagihanSiswa').parent().hasClass('table-responsive')) {
                $('#listTagihanSiswa').wrap('<div class="table-responsive"></div>');
            }
            $('#listTagihanSiswa').addClass('table table-bordered table-striped table-sm');
        }

        function showEditNominalModal(tagihanId, studentName, month, currentNominal, status) {
            // Set modal data
            $('#editNominalModal').find('.student-name').text(studentName);
            $('#editNominalModal').find('.month-name').text(month);
            $('#editNominalModal').find('.current-status').text(status);
            $('#editNominalModal').find('#editNominal').val(currentNominal);
            $('#editNominalModal').find('#tagihanId').val(tagihanId);

            // Show modal
            $('#editNominalModal').modal('show');
        }

        function updateNominalTagihan() {
            const tagihanId = $('#tagihanId').val();
            const nominal = $('#editNominal').val();

            if (!nominal || nominal < 0) {
                Swal.fire('Peringatan', 'Nominal harus diisi dan tidak boleh kurang dari 0', 'warning');
                return;
            }

            $.ajax({
                type: "PUT",
                url: "/tagihan-siswa/update-nominal/" + tagihanId,
                data: {
                    _token: "{{ csrf_token() }}",
                    nominal: nominal
                },
                dataType: "json",
                beforeSend: function() {
                    $('#btnUpdateNominal').prop('disabled', true).text('Updating...');
                },
                success: function (response) {
                    $('#btnUpdateNominal').prop('disabled', false).text('Update');

                    if(response.success){
                        $('#editNominalModal').modal('hide');
                        Swal.fire('Sukses', response.message, 'success').then(() => {
                            // Reload current filter instead of full page reload
                            loadTagihanSiswa();
                        });
                    } else {
                        Swal.fire('Peringatan', response.message, 'warning');
                    }
                },
                error: function (xhr, status, error) {
                    $('#btnUpdateNominal').prop('disabled', false).text('Update');
                    console.error(xhr.responseText);
                    Swal.fire('Terjadi Kesalahan', 'Gagal mengupdate nominal tagihan', 'error');
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
