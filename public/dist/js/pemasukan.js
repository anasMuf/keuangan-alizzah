// Global variables
let selectedSiswaKelas = null;
let itemTransaksi = {
    bulanan: [],
    non_bulanan: [],
    lainnya: []
};

$(function() {
    // Format currency functions
    function formatRupiah(angka) {
        if (angka === null || angka === undefined || angka === '') return '0';
        const number = parseFloat(angka.toString().replace(/[^0-9.-]/g, ''));
        if (isNaN(number)) return '0';
        return new Intl.NumberFormat('id-ID').format(number);
    }

    function parseRupiah(rupiah) {
        if (!rupiah) return 0;
        const cleaned = rupiah.toString().replace(/[^0-9]/g, '');
        return parseFloat(cleaned) || 0;
    }

    // Pencarian Siswa
    $('#cari-siswa').on('input', function() {
        const query = $(this).val();
        if (query.length >= 2) {
            $.get('/referensi/siswa', { q: query }, function(res) {
                let html = '';
                res.forEach(siswaKelas => {
                    html += `
                    <a href="#" class="list-group-item list-group-item-action siswa-item"
                       data-id="${siswaKelas.id}">
                        <div class="d-flex w-100">
                            <h5 class="mb-1">${siswaKelas.siswa.nama_lengkap}</h5>
                        </div>
                        <p class="mb-1">${siswaKelas.kelas.nama_kelas}</p>
                        <small>${siswaKelas.kelas.jenjang.nama_jenjang}</small>
                    </a>`;
                });
                $('.siswa-results .list-group').html(html);
            });
        } else {
            $('.siswa-results .list-group').html('<p class="text-center">Siswa Belum Dipilih</p>');
        }
    });

    // Pilih Siswa
    $(document).on('click', '.siswa-item', function(e) {
        e.preventDefault();
        selectedSiswaKelas = $(this).data('id');

        // Reset selections
        resetSelection();

        // Load tagihan siswa
        loadTagihanSiswa(selectedSiswaKelas);

        // Load pos pemasukan lainnya
        loadPosLainnya();
    });

    // Load Tagihan Siswa
    function loadTagihanSiswa(siswaKelasId) {
        $.get('/pemasukan/tagihan-siswa/' + siswaKelasId, function(response) {
            // Render tagihan bulanan
            renderTagihanBulanan(response.tagihan.bulanan);

            // Render tagihan non bulanan
            renderTagihanNonBulanan(response.tagihan.non_bulan);
        });
    }

    // Render Tagihan Bulanan
    function renderTagihanBulanan(tagihanBulanan) {
        let html = '';
        tagihanBulanan.forEach(tagihan => {
            html += `
            <div class="col-md-3 mb-3">
                <div class="card tagihan-bulanan"
                     data-id="${tagihan.id}"
                     data-bulan="${tagihan.bulan_id}"
                     data-nominal="${tagihan.nominal}">
                    <div class="card-body">
                        <h5>${tagihan.bulan.nama_bulan}</h5>
                        <p>Nominal: Rp ${formatRupiah(tagihan.nominal)}</p>
                        <button class="btn btn-sm btn-primary pilih-tagihan">
                            Pilih
                        </button>
                    </div>
                </div>
            </div>`;
        });
        $('#tagihan-bulanan-container').html(html);
    }

    // Pilih Tagihan (Bulanan/Non Bulanan)
    $(document).on('click', '.pilih-tagihan', function() {
        const card = $(this).closest('.card');
        const id = card.data('id');
        const nominal = card.data('nominal');

        if(card.hasClass('tagihan-bulanan')) {
            const bulan = card.data('bulan');
            addItemTransaksi('bulanan', {
                id: id,
                bulan: bulan,
                nominal: nominal,
                dibayar: 0
            });
        } else {
            addItemTransaksi('non_bulanan', {
                id: id,
                nominal: nominal,
                dibayar: 0
            });
        }

        renderItemTransaksi();
    });

    // Add Item Transaksi
    function addItemTransaksi(type, item) {
        itemTransaksi[type].push(item);
    }

    // Render Item Transaksi
    function renderItemTransaksi() {
        let html = '';

        // Render semua tipe transaksi
        Object.keys(itemTransaksi).forEach(type => {
            itemTransaksi[type].forEach((item, index) => {
                html += `
                <div class="card mb-3 item-transaksi" data-type="${type}" data-index="${index}">
                    <div class="card-body">
                        <h5>${getItemTitle(type, item)}</h5>
                        <p>Total: Rp ${formatRupiah(item.nominal)}</p>
                        <div class="form-group">
                            <label>Nominal Bayar</label>
                            <input type="text" class="form-control nominal-bayar"
                                   value="${formatRupiah(item.dibayar)}"
                                   data-type="${type}"
                                   data-index="${index}">
                        </div>
                        <button class="btn btn-danger hapus-item">Hapus</button>
                    </div>
                </div>`;
            });
        });

        $('#item-transaksi-container').html(html);
        hitungTotal();
    }

    // Handle Input Nominal Bayar
    $(document).on('input', '.nominal-bayar', function() {
        const type = $(this).data('type');
        const index = $(this).data('index');
        const nominal = parseRupiah($(this).val());

        itemTransaksi[type][index].dibayar = nominal;
        $(this).val(formatRupiah(nominal));

        hitungTotal();
    });

    // Hitung Total
    function hitungTotal() {
        let totalTagihan = 0;
        let totalBayar = 0;

        Object.keys(itemTransaksi).forEach(type => {
            itemTransaksi[type].forEach(item => {
                totalTagihan += item.nominal;
                totalBayar += item.dibayar;
            });
        });

        $('#total-tagihan').text(`Rp ${formatRupiah(totalTagihan)}`);
        $('#total-bayar').text(`Rp ${formatRupiah(totalBayar)}`);
        $('#sisa').text(`Rp ${formatRupiah(totalTagihan - totalBayar)}`);
    }

    // Reset Selection
    function resetSelection() {
        selectedSiswaKelas = null;
        itemTransaksi = {
            bulanan: [],
            non_bulanan: [],
            lainnya: []
        };
        renderItemTransaksi();
    }

    // Helper Functions
    function getItemTitle(type, item) {
        switch(type) {
            case 'bulanan':
                return `Tagihan Bulan ${item.bulan}`;
            case 'non_bulanan':
                return `Tagihan Non Bulanan`;
            case 'lainnya':
                return `Pembayaran Lainnya`;
            default:
                return '';
        }
    }
});
