@extends('layouts.app')

{{-- Customize layout sections --}}

@section('subtitle', 'Pengeluaran')
@section('content_header_title', 'Home')
@section('content_header_subtitle', 'Pengeluaran')
@section('plugins.Select2', true)

{{-- Content body: main page content --}}

@section('content_body')
<div class="container-fluid">
    <div class="row">
        <!-- Panel Pos Pengeluaran -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Pos Pengeluaran</h3>
                </div>
                <div class="card-body">
                    <!-- Container Pos -->
                    <div class="row" id="pos-container">
                        @foreach($pos as $item_pos_pemasukan)
                        <div class="col-12">
                            <h5>{{ $item_pos_pemasukan->nama_pos_pemasukan }}</h5>
                            <div class="row">
                                @foreach ($item_pos_pemasukan->pos_pengeluaran as $item_pos_pengeluaran)
                                <div class="col-md-6 mb-3">
                                    <div class="card pos-item" data-id="{{ $item_pos_pengeluaran->id }}" style="cursor: pointer;">
                                        <div class="card-body">
                                            <h6 class="card-title">{{ $item_pos_pengeluaran->nama_pos_pengeluaran }}</h6>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- Panel Form Input -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Input Pengeluaran</h3>
                </div>
                <div class="card-body">
                    <form id="formData">
                        @csrf
                        <input type="hidden" id="item-transaksi" name="item_transaksi">

                        <div class="form-group">
                            <label for="tanggal">Tanggal</label>
                            <input type="date" class="form-control" id="tanggal" name="tanggal" value="{{ old('tanggal', date('Y-m-d')) }}" required>
                        </div>

                        <div class="item-transaction" id="item-transaction-container"></div>

                        <!-- Total Nominal -->
                        <div class="row mt-4">
                            <div class="col-md-12">
                                <table class="table">
                                    <tr>
                                        <th>Total Nominal</th>
                                        <td class="text-right" id="total-nominal">Rp 0</td>
                                    </tr>
                                </table>
                            </div>
                        </div>

                        <div class="text-right mt-3">
                            <a href="{{ route('pengeluaran.main') }}" class="btn btn-default btn-flat">Kembali</a>
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

        /* Style untuk pos item yang dipilih */
        .pos-item.selected .card {
            border: 3px solid #007bff !important;
            box-shadow: 0 0 10px rgba(0, 123, 255, 0.3);
            transform: translateY(-2px);
            transition: all 0.3s ease;
        }

        /* Style untuk item transaksi */
        .item-transaksi .card {
            border: 1px solid #ddd;
            margin-bottom: 10px;
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
let selectedItems = [];

$(function() {
    // Handle pos selection
    handleSelectPos();

    // Form Submit Handler
    handleFormSubmit();

    // Event handlers for item management
    $(document).on('input', '.nominal-input', handleNominalInput);
    $(document).on('input', '.keterangan-input', handleKeteranganInput);
    $(document).on('click', '.hapus-item', handleHapusItem);
});

// Handle pos selection
function handleSelectPos() {
    $('.pos-item').click(function() {
        const id = $(this).data('id');
        const name = $(this).find('.card-title').text();

        // Check if item already exists
        const exists = selectedItems.some(item => item.pos_pengeluaran_id === id);
        if (exists) {
            Swal.fire('Peringatan', 'Pos pengeluaran ini sudah dipilih', 'warning');
            return;
        }

        // Add item to selection
        addItemToSelection(id, name);

        // Add visual selection
        $(this).addClass('selected');

        // Render items
        renderSelectedItems();
    });
}

// Add item to selection
function addItemToSelection(id, name) {
    selectedItems.push({
        pos_pengeluaran_id: id,
        nama_pos: name,
        keterangan: '',
        nominal: 0
    });
}

// Handle nominal input
function handleNominalInput() {
    const index = $(this).data('index');
    const value = parseRupiah($(this).val());

    selectedItems[index].nominal = value;
    $(this).val(formatRupiah(value));

    updateTotal();
}

// Handle keterangan input
function handleKeteranganInput() {
    const index = $(this).data('index');
    const value = $(this).val();

    selectedItems[index].keterangan = value;
}

// Handle hapus item
function handleHapusItem() {
    const index = $(this).data('index');
    const posId = selectedItems[index].pos_pengeluaran_id;

    // Remove from array
    selectedItems.splice(index, 1);

    // Remove visual selection
    $(`.pos-item[data-id="${posId}"]`).removeClass('selected');

    // Re-render
    renderSelectedItems();
}

// Render selected items
function renderSelectedItems() {
    let html = '';

    if (selectedItems.length === 0) {
        html = '<p class="text-center text-muted">Belum ada pos pengeluaran yang dipilih</p>';
    } else {
        selectedItems.forEach((item, index) => {
            html += `
            <div class="card mb-3 item-transaksi" data-index="${index}">
                <div class="card-header">
                    <h6 class="card-title mb-0">${item.nama_pos}</h6>
                </div>
                <div class="card-body">
                    <div class="form-group mb-2">
                        <label>Keterangan:</label>
                        <textarea class="form-control keterangan-input"
                                  data-index="${index}"
                                  rows="2"
                                  placeholder="Masukkan keterangan">${item.keterangan}</textarea>
                    </div>
                    <div class="form-group mb-2">
                        <label>Nominal:</label>
                        <input type="text"
                               class="form-control text-right nominal-input"
                               data-index="${index}"
                               value="${formatRupiah(item.nominal)}"
                               placeholder="0">
                    </div>
                    <button class="btn btn-sm btn-danger hapus-item" data-index="${index}">
                        <i class="fas fa-trash"></i> Hapus
                    </button>
                </div>
            </div>`;
        });
    }

    $('#item-transaction-container').html(html);
    $('#item-transaksi').val(JSON.stringify(selectedItems));
    updateTotal();
}

// Update total
function updateTotal() {
    const total = selectedItems.reduce((sum, item) => sum + (item.nominal || 0), 0);
    $('#total-nominal').text(`Rp ${formatRupiah(total)}`);
}

// Form Submit Handler
function handleFormSubmit() {
    $('#formData').submit(function(e) {
        e.preventDefault();

        if (!validateForm()) {
            return;
        }

        const formData = {
            tanggal: $('#tanggal').val(),
            items:  JSON.stringify(selectedItems)
        };

        $.ajax({
            url: "{{ route('pengeluaran.store') }}",
            type: 'POST',
            data: formData,
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
    if (selectedItems.length === 0) {
        Swal.fire('Peringatan', 'Silakan pilih minimal satu pos pengeluaran', 'warning');
        return false;
    }

    for (let i = 0; i < selectedItems.length; i++) {
        const item = selectedItems[i];

        if (!item.keterangan.trim()) {
            Swal.fire('Peringatan', `Keterangan untuk ${item.nama_pos} harus diisi`, 'warning');
            return false;
        }

        if (!item.nominal || item.nominal <= 0) {
            Swal.fire('Peringatan', `Nominal untuk ${item.nama_pos} harus diisi dan lebih dari 0`, 'warning');
            return false;
        }
    }

    return true;
}

// Handle Submit Success
function handleSubmitSuccess(response) {
    if (response.success) {
        Swal.fire({
            title: 'Sukses',
            text: 'Data pengeluaran berhasil disimpan',
            icon: 'success',
            timer: 1500
        }).then(() => {
            window.location.href = "{{ route('pengeluaran.main') }}";
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
    let message = 'Terjadi kesalahan saat menyimpan data';

    if (xhr.responseJSON && xhr.responseJSON.message) {
        message = xhr.responseJSON.message;
    }

    Swal.fire('Error', message, 'error');
    console.error(xhr.responseText);
}

// Helper function to format currency
function formatRupiah(number) {
    const num = parseInt(number);
    return !isNaN(num) ?
        num.toLocaleString('id-ID', {
            minimumFractionDigits: 0,
            maximumFractionDigits: 0
        }) : '0';
}

// Helper function to parse currency string to number
function parseRupiah(rupiahString) {
    if (!rupiahString) return 0;
    return parseInt(rupiahString.replace(/[^0-9]/g, '')) || 0;
}
</script>
@endpush
