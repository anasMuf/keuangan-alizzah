@extends('layouts.app')

{{-- Customize layout sections --}}

@section('subtitle', 'Pemasukan')
@section('content_header_title', 'Home')
@section('content_header_subtitle', 'Pemasukan')
@section('plugins.Select2', true)

{{-- Content body: main page content --}}

@section('content_body')
<div class="container-fluid">
    <div class="row">
        <!-- Panel Pencarian Siswa -->
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <div class="form-group">
                        <label>Cari Siswa</label>
                        <div class="input-group">
                            <input type="text" class="form-control" id="cari-siswa" placeholder="Cari nama siswa...">
                            <div class="input-group-append">
                                <span class="input-group-text"><i class="fas fa-search"></i></span>
                            </div>
                        </div>
                    </div>
                    <div class="siswa-results mt-2">
                        <div class="list-group">
                            <p class="text-center">Siswa Belum Dipilih</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Detail Siswa -->
        <div class="col-md-9">
            <div class="card">
                <div class="card-body">
                    <h5 id="siswa-nama">Nama Siswa: <span class="text-muted">Belum Dipilih</span></h5>
                    <p id="siswa-kelas">Kelas: <span class="text-muted">Belum Dipilih</span></p>
                    <p id="siswa-jenjang">Jenjang: <span class="text-muted">Belum Dipilih</span></p>
                </div>
            </div>
        </div>

        <!-- Panel Tagihan -->
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Tagihan</h3>
                </div>
                <div class="card-body">
                    <!-- Container Tagihan Bulanan -->
                    <div class="row" id="tagihan-bulanan-container"></div>

                    <!-- Container Tagihan Non Bulanan -->
                    <div class="row" id="tagihan-non-bulanan-container"></div>
                </div>
            </div>
        </div>

        <!-- Panel Pos Lainnya -->
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <div class="form-group">
                        <label>Pos Pemasukan Insidential</label>
                        <div class="input-group">
                            <input type="text" class="form-control" id="cari-pos" placeholder="Cari pos pemasukan...">
                            <div class="input-group-append">
                                <span class="input-group-text"><i class="fas fa-search"></i></span>
                            </div>
                        </div>
                    </div>
                    <div class="pos-results mt-2">
                        <div class="list-group">
                            <!-- Results akan muncul di sini -->
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Panel Item Transaksi -->
        <div class="col-md-9" id="temTransaksi">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Item Transaksi</h3>
                </div>
                <div class="card-body">
                    <form id="formData">
                        @csrf
                        <input type="hidden" id="siswa-kelas-id" name="siswa_kelas_id">
                        <input type="hidden" id="item-transaksi" name="item_transaksi">
                        <div class="form-group">
                            <label for="tanggal">Tanggal</label>
                            <input type="date" name="tanggal" class="form-control" id="tanggal" value="{{ old('tanggal', date('Y-m-d')) }}" placeholder="Tanggal...">
                        </div>

                        <!-- Container Item Transaksi -->
                        <div id="item-transaksi-container"></div>

                        <!-- Total Pembayaran -->
                        <div class="row mt-4">
                            <div class="col-md-4 ml-auto">
                                <table class="table">
                                    <tr>
                                        <th>Total Tagihan</th>
                                        <td class="text-right" id="total-tagihan">Rp 0</td>
                                    </tr>
                                    <tr>
                                        <th>Total Bayar</th>
                                        <td class="text-right" id="total-bayar">Rp 0</td>
                                    </tr>
                                    <tr>
                                        <th>Sisa</th>
                                        <td class="text-right" id="sisa">Rp 0</td>
                                    </tr>
                                </table>
                            </div>
                        </div>

                        <div class="text-right mt-3">
                            <a href="{{ route('pemasukan.main') }}" class="btn btn-default btn-flat">Kembali</a>
                            <button type="submit" class="btn btn-success btn-flat">
                                <i class="fas fa-save"></i> Simpan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@stop

@push('css')
    {{-- Add here extra stylesheets --}}
    <link rel="stylesheet" href="{{ asset('dist/css/style.css') }}">
    <style>
        .card .card-body h5 {
            font-size: 14px;
            font-weight: bold;
        }
        .card .card-body p {
            font-size: 12px;
            font-weight: bold;
        }
        .list-group-item small {
            color: #d3d3d3;
        }

        .siswa-results, .pos-results {
            max-height: 300px;
            overflow-y: auto;
        }
        .list-on {
            position: absolute;
            z-index: 100;
            width: 100%;
            left: 0;
        }
        .item-transaksi .card {
            border: 1px solid #ddd;
        }

        /* Style untuk bulan yang dipilih */
        .bulan-item.selected .card {
            border: 3px solid #007bff !important;
            box-shadow: 0 0 10px rgba(0, 123, 255, 0.3);
            transform: translateY(-2px);
            transition: all 0.3s ease;
        }

        /* Style untuk bulan lunas */
        .bulan-item[data-status="LUNAS"] .card {
            background: linear-gradient(45deg, #28a745, #20c997) !important;
            color: white;
        }

        /* Style untuk bulan belum lunas */
        .bulan-item[data-status="BELUM LUNAS"] .card {
            background: linear-gradient(45deg, #dcaa35, #e6fd14) !important;
            color: white;
        }

        /* Style untuk bulan belum bayar */
        .bulan-item[data-status="BELUM BAYAR"] .card {
            background: linear-gradient(45deg, #dc3545, #fd7e14) !important;
            color: white;
        }

        /* Style untuk bulan yang belum jatuh tempo */
        .bulan-item[data-status="belum tempo"] .card {
            background: #f8f9fa !important;
            color: #6c757d;
        }

        /* Hover effect */
        .bulan-item:hover .card {
            transform: translateY(-1px);
            transition: all 0.2s ease;
        }

        /* Disable pointer untuk bulan lunas */
        .bulan-item[data-status="lunas"] {
            cursor: not-allowed !important;
        }

        .pos-item:hover {
            background-color: #f8f9fa;
            cursor: pointer;
        }

        /* Style untuk field sisa */
        #sisa.text-success {
            color: #28a745 !important;
            font-weight: bold;
        }

        #sisa.text-danger {
            color: #dc3545 !important;
            font-weight: bold;
        }

    </style>
@endpush

{{-- Push extra scripts --}}

@push('js')
<script>
// Global variables
let selectedSiswaKelas = null;
let itemTransaksi = {
    bulanan: [],
    non_bulanan: [],
    lainnya: []
};
let selectedSiswaKelasJenjangId = null;
let selectedSiswaKelasJenisKelamin = null;

$(function() {
    // Initialize
    resetSelection();

    // Event Handlers
    initializeEventHandlers();

    // Form Submit Handler
    handleFormSubmit();
});

function initializeEventHandlers() {
    // Siswa Search
    $('#cari-siswa').on('input', handleSiswaSearch);
    $(document).on('click', '.siswa-item', handleSiswaSelect);

    // Transaksi Items
    $(document).on('click', '.pilih-tagihan', handleTagihanSelect);
    $(document).on('input', '.nominal-bayar', handleNominalBayarInput);
    $(document).on('click', '.hapus-item', handleHapusItem);
}

// Siswa Search Handler
function handleSiswaSearch() {
    const query = $(this).val();
    if (query.length >= 2) {
        $.get('/referensi/siswa', { q: query }, renderSiswaResults);
    } else {
        $('.siswa-results .list-group').html('<p class="text-center">Siswa Belum Dipilih</p>');
    }
}

// Render Functions
function renderSiswaResults(res) {
    let html = '';
    $('.siswa-results').addClass('list-on');
    $.each(res, function(index, siswaKelas) {
        html += `
        <a href="#" class="list-group-item list-group-item-action siswa-item"
           data-id="${siswaKelas.id}"
           data-siswakelas="${btoa(unescape(encodeURIComponent(JSON.stringify(siswaKelas))))}">
            <div class="d-flex w-100">
                <h5 class="mb-1">${siswaKelas.siswa.nama_lengkap} <span class="jenis-kelamin">${siswaKelas.siswa.jenis_kelamin}</span></h5>
            </div>
            <p class="mb-1">${siswaKelas.kelas.nama_kelas}</p>
            <small data-id="${siswaKelas.kelas.jenjang.id}">${siswaKelas.kelas.jenjang.nama_jenjang}</small>
        </a>`;
    });
    $('.siswa-results .list-group').html(html);
}

// Load Tagihan Siswa
function loadTagihanSiswa(siswaKelasId,siswaKelasJenisKelamin) {
    $.get(`/pemasukan/tagihan-siswa/${siswaKelasId}`, {
        jenis_kelamin: siswaKelasJenisKelamin,
        jenjang_id: selectedSiswaKelasJenjangId
    }, function(response) {
        // Handle non bulanan (sekali bayar)
        renderTagihanNonBulanan(response.tagihan.non_bulan);

        // Handle bulanan
        renderTagihanBulanan(response.tagihan.bulanan);
    });
}

// Render Tagihan Bulanan
function renderTagihanBulanan(tagihanBulanan) {
    let html = '';
    console.log(tagihanBulanan);

    // Loop through each month
    $.each(tagihanBulanan, function(bulanId, data) {
        let totalNominal = 0;
        let totalSisa = 0;
        let posNames = [];

        // Calculate total and collect pos names
        $.each(data.details, function(posName, detail) {
            totalNominal += parseFloat(detail.nominal);
            totalSisa += parseFloat(detail.sisa_pembayaran);
            posNames.push(posName);
        });

        // Get first detail object to access bulan info
        const firstDetail = Object.values(data.details)[0];

        html += `
        <div class="col-md-2 mb-3">
            <div class="bulan-item" data-status="${data.status}">
                <div class="card tagihan-bulanan"
                     data-bulan-id="${bulanId}"
                     data-details='${JSON.stringify(data.details)}'
                     data-nominal="${totalNominal}"
                     data-sisa="${totalSisa}">
                    <div class="card-body">
                        <h5 class="card-title">${firstDetail.bulan.nama_bulan}</h5><br>
                        <p class="mb-0">Pos: ${posNames.join(', ')}</p>
                        <p class="mb-0">Nominal: Rp ${formatRupiah(totalNominal)}</p>
                        ${data.status == 'belum_lunas' ? `
                            <p class="mb-0">Sisa: Rp ${formatRupiah(totalSisa)}</p>
                        ` : ''}
                        <p class="mb-2">Status: ${data.status}</p>
                        ${data.status !== 'lunas' ? `
                            <button class="btn btn-sm btn-primary pilih-tagihan">
                                Pilih
                            </button>
                        ` : ''}
                    </div>
                </div>
            </div>
        </div>`;
    });

    $('#tagihan-bulanan-container').html(html || '<div class="col-12"><p class="text-center">Tidak ada tagihan bulanan</p></div>');
}

// Render Tagihan Non Bulanan
function renderTagihanNonBulanan(tagihanNonBulanan) {
    let html = '';

    $.each(tagihanNonBulanan.sekali, function(index, tagihan) {
        // const status = getStatusTagihan(tagihan);

        html += `
        <div class="col-md-6 mb-3">
            <div class="card tagihan-non-bulanan"
                 data-id="${tagihan.id}"
                 data-pos-id="${tagihan.pos_pemasukan_id}"
                 data-nominal="${tagihan.nominal}"
                 data-sisa="${tagihan.sisa_pembayaran}">
                <div class="card-body">
                    <h5 class="card-title">${tagihan.pos_pemasukan.nama_pos_pemasukan}</h5><br>
                    <p class="mb-0">Nominal: Rp ${formatRupiah(tagihan.nominal)}</p>
                    ${tagihan.status == 'belum_lunas' ? `
                        <p class="mb-0">Sisa: Rp ${formatRupiah(tagihan.sisa_pembayaran)}</p>
                    ` : ''}
                    <p class="mb-2">Status: ${tagihan.status}</p>
                    ${tagihan.status !== 'lunas' ? `
                        <button class="btn btn-sm btn-primary pilih-tagihan">
                            Pilih
                        </button>
                    ` : ''}
                </div>
            </div>
        </div>`;
    });


    $.each(tagihanNonBulanan.tahunan, function(index, tagihan) {
        // const status = getStatusTagihan(tagihan);

        html += `
        <div class="col-md-6 mb-3">
            <div class="card tagihan-non-bulanan"
                 data-id="${tagihan.id}"
                 data-pos-id="${tagihan.pos_pemasukan_id}"
                 data-nominal="${tagihan.nominal}"
                 data-sisa="${tagihan.sisa_pembayaran}">
                <div class="card-body">
                    <h5 class="card-title">${tagihan.pos_pemasukan.nama_pos_pemasukan}</h5><br>
                    <p class="mb-0">Nominal: Rp ${formatRupiah(tagihan.nominal)}</p>
                    ${tagihan.status == 'belum_lunas' ? `
                        <p class="mb-0">Sisa: Rp ${formatRupiah(tagihan.sisa_pembayaran)}</p>
                    ` : ''}
                    <p class="mb-2">Status: ${tagihan.status}</p>
                    ${tagihan.status !== 'lunas' ? `
                        <button class="btn btn-sm btn-primary pilih-tagihan">
                            Pilih
                        </button>
                    ` : ''}
                </div>
            </div>
        </div>`;
    });

    $('#tagihan-non-bulanan-container').html(html || '<div class="col-12"><p class="text-center">Tidak ada tagihan non bulanan</p></div>');
}

// Helper function untuk menentukan status tagihan
function getStatusTagihan(tagihan) {


    if (tagihan.status === 'LUNAS') {
        return 'LUNAS';
    }

    if (tagihan.total_bayar > 0) {
        return 'BELUM LUNAS';
    }

    // Check jatuh tempo if needed
    const today = new Date();
    const jatuhTempo = new Date(tagihan.tanggal_jatuh_tempo);

    if (today < jatuhTempo) {
        return 'BELUM TEMPO';
    }

    return 'BELUM BAYAR';
}

// Reset Selection
function resetSelection() {
    selectedSiswaKelas = null;
    itemTransaksi = {
        bulanan: [],
        non_bulanan: [],
        lainnya: []
    };
    $('#siswa-kelas-id').val('');
    $('#item-transaksi').val('');
    $('#tagihan-bulanan-container').empty();
    $('#tagihan-non-bulanan-container').empty();
    $('#item-transaksi-container').empty();
    hitungTotal();
}

// Handle Siswa Select
function handleSiswaSelect(e) {
    e.preventDefault();
    // Reset selections
    resetSelection();

    selectedSiswaKelas = $(this).data('id');
    const siswaKelas = JSON.parse(decodeURIComponent(escape(atob($(this).attr('data-siswakelas')))));
    selectedSiswaKelasJenjangId = $(this).find('small').data('id');
    selectedSiswaKelasJenisKelamin = $(this).find('h5 > span').text();


    // Update siswa_kelas_id
    $('#siswa-kelas-id').val(selectedSiswaKelas);


    $('.siswa-results .list-group').html('<p class="text-center">Siswa Dipilih</p>');

    $('.siswa-results').removeClass('list-on');
    loadDetailSiswa(siswaKelas);

    // Load tagihan siswa
    loadTagihanSiswa(selectedSiswaKelas,selectedSiswaKelasJenisKelamin);

    // load pos lainnya
    loadPosLainnya(selectedSiswaKelasJenjangId);
}

function loadDetailSiswa(siswaKelas) {
    console.log(siswaKelas);

    $('#siswa-nama').html(`Nama Siswa: <span class="text-muted">${siswaKelas.siswa.nama_lengkap}</span>`);
    $('#siswa-kelas').html(`Kelas: <span class="text-muted">${siswaKelas.kelas.nama_kelas}</span>`);
    $('#siswa-jenjang').html(`Jenjang: <span class="text-muted">${siswaKelas.kelas.jenjang.nama_jenjang}</span>`);
}

// Handle Tagihan Select
function handleTagihanSelect() {
    location.href = "#temTransaksi"
    const card = $(this).closest('.card');

    if(card.hasClass('tagihan-bulanan')) {
        // Handle bulanan
        const details = card.data('details');
        const bulanId = card.data('bulan-id');
        const nominal = parseFloat(card.data('nominal'));
        const sisa = parseFloat(card.data('sisa'));

        // Add each pos in the month as separate item
        $.each(details, function(posName, detail) {
            if(parseFloat(detail.sisa_pembayaran) > 0 || detail.status != 'lunas'){
                addItemTransaksi('bulanan', {
                    id: detail.id,
                    bulan_id: bulanId,
                    pos_id: detail.pos_pemasukan_id,
                    pos_name: posName,
                    istabungan: detail.pos_pemasukan.tabungan,
                    saldo_tabungan_wajib: detail.pos_pemasukan.saldo_tabungan_wajib,
                    nominal: parseFloat(detail.nominal),
                    sisa: parseFloat(detail.sisa_pembayaran),
                    dibayar: parseFloat(detail.sisa_pembayaran) > 0 ? parseFloat(detail.sisa_pembayaran) : parseFloat(detail.nominal) // Default full payment
                });
            }
        });
    } else {
        // Handle non bulanan
        const id = card.data('id');
        const nominal = parseFloat(card.data('nominal'));
        const sisa = parseFloat(card.data('sisa'));
        const posId = card.data('pos-id');

        addItemTransaksi('non_bulanan', {
            id: id,
            pos_id: posId,
            nominal: nominal,
            sisa: sisa,
            dibayar: sisa > 0 ? sisa : nominal // Default full payment
        });
    }

    renderItemTransaksi();
}

// Add Item Transaksi
function addItemTransaksi(type, item) {
    // Check if item already exists
    const exists = itemTransaksi[type].some(existingItem => existingItem.id === item.id);
    if (exists) {
        Swal.fire('Peringatan', 'Item ini sudah ditambahkan', 'warning');
        return false;
    }

    // Add new item
    itemTransaksi[type].push(item);

    // Add selected class to card if bulanan
    if (type === 'bulanan') {
        $(`.bulan-item[data-bulan="${item.bulan_id}"]`).addClass('selected');
    }

    return true;
}

// Handle Nominal Bayar Input
function handleNominalBayarInput() {
    const type = $(this).data('type');
    const index = $(this).data('index');
    const nominal = parseRupiah($(this).val());

    // Update item
    itemTransaksi[type][index].dibayar = nominal;
    $(this).val(formatRupiah(nominal));

    // Update totals
    hitungTotal();
}

// Handle Hapus Item
function handleHapusItem() {
    const card = $(this).closest('.item-transaksi');
    const type = card.data('type');
    const index = card.data('index');

    // Remove item
    itemTransaksi[type].splice(index, 1);

    // Re-render
    renderItemTransaksi();

    loadPosLainnya(selectedSiswaKelasJenjangId)
}

// Form Submit Handler
function handleFormSubmit() {
    $('#formData').submit(function(e) {
        e.preventDefault();

        if (!validateForm()) {
            return;
        }

        const formData = {
            siswa_kelas_id: selectedSiswaKelas,
            tanggal: $('#tanggal').val(),
            item_transaksi: itemTransaksi
        };



        $.ajax({
            url: "{{ route('pemasukan.store') }}",
            type: 'POST',
            data: JSON.stringify(formData),
            contentType: 'application/json',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: handleSubmitSuccess,
            error: handleSubmitError
        });
    });
}

// Validation
function validateForm() {
    if (!selectedSiswaKelas) {
        Swal.fire('Peringatan', 'Silakan pilih siswa terlebih dahulu', 'warning');
        return false;
    }

    const totalItems = Object.values(itemTransaksi)
        .reduce((total, items) => total + items.length, 0);

    if (totalItems === 0) {
        Swal.fire('Peringatan', 'Silakan pilih minimal satu item pembayaran', 'warning');
        return false;
    }

    return true;
}

// Handle Submit Success
function handleSubmitSuccess(response) {
    if (response.success) {
        Swal.fire({
            title: 'Sukses',
            text: 'Data pembayaran berhasil disimpan',
            icon: 'success',
            timer: 1500
        }).then(() => {
            window.location.href = "{{ route('pemasukan.main') }}";
        });
    } else {
        let message = response.message;
        $.each(response.message_validation, function (i,msg) {
            message += msg[0]+', <br>';
        });
        Swal.fire('Peringatan', message, 'warning');
    }
}

// Handle Submit Error
function handleSubmitError(xhr, status, error) {
    Swal.fire('Error', 'Terjadi kesalahan saat menyimpan data', 'error');
    console.error(xhr.responseText);
}

// Calculate and display totals
function hitungTotal() {
    let totalTagihan = 0;
    let totalBayar = 0;

    // Calculate totals from all types of items
    $.each(['bulanan', 'non_bulanan', 'lainnya'], function(index, type) {
        $.each(itemTransaksi[type], function(i, item) {
            console.log(item);


            if (item.istabungan && !item.bulan_id) {
                totalTagihan += item.sisa > 0 ? parseFloat(item.sisa+item.dibayar) : parseFloat(item.nominal+item.dibayar) || 0;
            }else{
                totalTagihan += item.sisa > 0 ? parseFloat(item.sisa) : parseFloat(item.nominal) || 0;
            }

            totalBayar += parseFloat(item.dibayar) || 0;
        });
    });
    console.log('totalTagihan:', totalTagihan);
    console.log('totalBayar:', totalBayar);


    // Display formatted totals
    $('#total-tagihan').text(`Rp ${formatRupiah(totalTagihan)}`);
    $('#total-bayar').text(`Rp ${formatRupiah(totalBayar)}`);

    // Calculate and display remaining amount
    const sisa = totalTagihan - totalBayar;
    const sisaElement = $('#sisa');

    sisaElement.text(`Rp ${formatRupiah(Math.abs(sisa))}`);

    // Update sisa styling based on value
    if (sisa > 0) {
        sisaElement.removeClass('text-success').addClass('text-danger');
    } else if (sisa < 0) {
        sisaElement.removeClass('text-success text-danger')
            .addClass('text-warning')
            .text(`Rp ${formatRupiah(Math.abs(sisa))} (Kelebihan)`);
    } else {
        sisaElement.removeClass('text-danger').addClass('text-success');
    }
}

// Helper function to format currency
function formatRupiah(number) {
    const num = parseFloat(number);
    return !isNaN(num) ?
        num.toLocaleString('id-ID', {
            minimumFractionDigits: 0,
            maximumFractionDigits: 0
        }) : '0';
}

// Helper function to parse currency string to number
function parseRupiah(rupiahString) {
    if (!rupiahString) return 0;
    return parseFloat(rupiahString.replace(/[^0-9-]/g, '')) || 0;
}

// Render Item Transaksi
function renderItemTransaksi() {


    let html = '';

    // Render bulanan items
    $.each(itemTransaksi.bulanan, function(index, item) {
        html += `
        <div class="card mb-3 item-transaksi" data-type="bulanan" data-index="${index}">
            <div class="card-header">
                <h5 class="card-title mb-0">${item.pos_name} - ${getBulanName(item.bulan_id)}</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <p class="mb-1">Total Tagihan: Rp ${formatRupiah(item.nominal)}</p>
                        ${item.sisa > 0 && item.sisa !== item.nominal ? `
                        <p class="mb-0">Sisa: Rp ${formatRupiah(item.sisa)}</p>
                        ` : ''}
                        ${item.istabungan ? /*`
                        <p class="mb-0">Saldo Tabungan Wajib: Rp ${formatRupiah(item.saldo_tabungan_wajib)}</p>
                        `*/'' : ''}
                    </div>
                    <div class="col-md-6">
                        <div class="form-group mb-0">
                            <label>Nominal Bayar:</label>
                            <input type="text"
                                   class="form-control nominal-bayar text-right"
                                   value="${formatRupiah(item.dibayar)}"
                                   data-type="bulanan"
                                   data-index="${index}">
                        </div>
                    </div>
                </div>
                <button class="btn btn-sm btn-danger mt-2 hapus-item">
                    <i class="fas fa-trash"></i> Hapus
                </button>
            </div>
        </div>`;
    });

    // Render non bulanan items
    $.each(itemTransaksi.non_bulanan, function(index, item) {
        html += `
        <div class="card mb-3 item-transaksi" data-type="non_bulanan" data-index="${index}">
            <div class="card-header">
                <h5 class="card-title mb-0">${getPosName(item.pos_id)}</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <p class="mb-1">Total Tagihan: Rp ${formatRupiah(item.nominal)}</p>
                        ${item.sisa > 0 && item.sisa !== item.nominal ? `
                        <p class="mb-0">Sisa: Rp ${formatRupiah(item.sisa)}</p>
                        ` : ''}
                    </div>
                    <div class="col-md-6">
                        <div class="form-group mb-0">
                            <label>Nominal Bayar:</label>
                            <input type="text"
                                   class="form-control nominal-bayar text-right"
                                   value="${formatRupiah(item.dibayar)}"
                                   data-type="non_bulanan"
                                   data-index="${index}">
                        </div>
                    </div>
                </div>
                <button class="btn btn-sm btn-danger mt-2 hapus-item">
                    <i class="fas fa-trash"></i> Hapus
                </button>
            </div>
        </div>`;
    });

    // Render lainnya items if exists
    $.each(itemTransaksi.lainnya, function(index, item) {

        html += `
        <div class="card mb-3 item-transaksi" data-type="lainnya" data-index="${index}">
            <div class="card-header">
                <h5 class="card-title mb-0">${item.pos_name}</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <p class="mb-1">
                        ${!item.istabungan ? `
                        Total Tagihan: Rp ${formatRupiah(item.nominal)}
                        ` : 'Saldo Tabungan: Rp ' + formatRupiah(item.saldotabungan)}
                        </p>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group mb-0">
                            <label>Nominal Bayar:</label>
                            <input type="text"
                                   class="form-control nominal-bayar text-right"
                                   value="${formatRupiah(item.dibayar)}"
                                   data-type="lainnya"
                                   data-index="${index}">
                        </div>
                    </div>
                </div>
                <button class="btn btn-sm btn-danger mt-2 hapus-item">
                    <i class="fas fa-trash"></i> Hapus
                </button>
            </div>
        </div>`;
    });

    // Update container and hidden input
    $('#item-transaksi-container').html(html || '<p class="text-center">Belum ada item transaksi</p>');
    $('#item-transaksi').val(JSON.stringify(itemTransaksi));

    // Update totals
    hitungTotal();
}

// Helper function to get bulan name
function getBulanName(bulanId) {
    // Get bulan name from the card element
    const bulanCard = $(`.bulan-item .card[data-bulan-id="${bulanId}"]`);
    return bulanCard.find('.card-title').text() || 'Unknown';
}

// Helper function to get pos name
function getPosName(posId) {
    // Get pos name from the card element
    let posCard = $(`.tagihan-non-bulanan[data-pos-id="${posId}"]`);
    return posCard.find('.card-title').text() || 'Unknown';
}

// Load Pos Lainnya
function loadPosLainnya(selectedSiswaKelasJenjangId) {


    if (!selectedSiswaKelas) return;




    $.get('/referensi/pos-pemasukan', {
        jenjang_id: selectedSiswaKelasJenjangId,
        siswa_kelas_id: selectedSiswaKelas
    }, function(res) {


        let html = '';

        if (res.length > 0) {
            $.each(res, function(index, pos) {

                // Skip if item already in transaction
                const exists = itemTransaksi.lainnya.some(item => item.pos_id === pos.id);
                if (exists) return;

                html += `
                <div class="list-group-item pos-item"
                     data-id="${pos.id}"
                     data-nominal="${pos.nominal_valid}"
                     data-wajib="0"
                     data-istabungan="${pos.tabungan}"
                     data-saldotabungan="${pos.saldo_tabungan}"
                     data-pembayaran="${pos.pembayaran}">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-0">${pos.nama_pos_pemasukan}</h6>
                            <small class="text-muted">${pos.nominal_valid > 0 ? 'Rp '+formatRupiah(pos.nominal_valid) : 'Saldo: Rp '+formatRupiah(pos.saldo_tabungan) }</small>
                        </div>
                        <button class="btn btn-sm btn-primary pilih-pos-lainnya">
                            <i class="fas fa-plus"></i> Pilih
                        </button>
                    </div>
                </div>`;
            });
        }

        if (!html) {
            html = '<div class="list-group-item text-center text-muted">Tidak ada pos pemasukan tambahan</div>';
        }

        $('.pos-results .list-group').html(html);

        // Initialize click handler for pos lainnya
        initializePosLainnyaHandler();
    });
}

// Initialize Pos Lainnya Handler
function initializePosLainnyaHandler() {
    $(document).off('click', '.pilih-pos-lainnya').on('click', '.pilih-pos-lainnya', function(e) {
        e.preventDefault();
        const posItem = $(this).closest('.pos-item');
        const id = posItem.data('id');
        const nominal = parseFloat(posItem.data('nominal'));
        const posName = posItem.find('h6').text();
        const istabungan = posItem.data('istabungan');
        const saldotabungan = posItem.data('saldotabungan');



        // Add to lainnya array
        addItemTransaksi('lainnya', {
            id: id,
            pos_id: id,
            pos_name: posName,
            istabungan: istabungan,
            saldotabungan: saldotabungan,
            nominal: nominal,
            dibayar: nominal
        });

        // Remove item from list
        posItem.fadeOut(300, function() {
            $(this).remove();
            if ($('.pos-item').length === 0) {
                $('.pos-results .list-group').html(
                    '<div class="list-group-item text-center text-muted">Tidak ada pos pemasukan tambahan</div>'
                );
            }
        });

        // Render transaction items
        renderItemTransaksi();
    });

    // Add search functionality
    $('#cari-pos').off('input').on('input', function() {
        const query = $(this).val().toLowerCase();
        $('.pos-item').each(function() {
            const posName = $(this).find('h6').text().toLowerCase();
            $(this).toggle(posName.includes(query));
        });
    });
}
</script>
@endpush
