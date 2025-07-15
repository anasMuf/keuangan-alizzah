var selectedSiswaKelas = null;
var selectedPos = {wajib: [], insidential: []};
var selectedBulan = null;

$(function() {

    // Fungsi format rupiah
    function formatRupiah(angka) {
        if (angka === null || angka === undefined || angka === '') return '0';

        const number = parseFloat(angka.toString().replace(/[^0-9.-]/g, ''));
        if (isNaN(number)) return '0';

        return new Intl.NumberFormat('id-ID').format(number);
    }

    // Fungsi parse rupiah ke number
    function parseRupiah(rupiah) {
        if (!rupiah) return 0;
        // Hapus semua karakter kecuali angka
        const cleaned = rupiah.toString().replace(/[^0-9]/g, '');
        return parseFloat(cleaned) || 0;
    }

    // Format input nominal saat user mengetik
    $('#nominal-dibayar').on('input', function() {
        let value = $(this).val().replace(/[^0-9]/g, '');
        if (value) {
            const numericValue = parseFloat(value);
            $(this).data('numeric-value', numericValue);
            $(this).val(formatRupiah(numericValue));
        } else {
            $(this).data('numeric-value', 0);
            $(this).val('');
        }
        hitungTotalDanSisa();
    });

    // Event focus dan blur untuk handling format nominal
    $('#nominal-dibayar').on('focus', function() {
        const numericValue = $(this).data('numeric-value') || 0;
        if (numericValue > 0) {
            $(this).val(numericValue.toString());
        }
    });

    $('#nominal-dibayar').on('blur', function() {
        const value = $(this).val().replace(/[^0-9]/g, '');
        if (value) {
            const numericValue = parseFloat(value);
            $(this).data('numeric-value', numericValue);
            $(this).val(formatRupiah(numericValue));
        } else {
            $(this).data('numeric-value', 0);
            $(this).val('');
        }
        hitungTotalDanSisa();
    });

    // Pencarian Siswa
    $('#cari-siswa').on('input', function() {
        const query = $(this).val();
        if (query.length >= 2) {
            $.get('/referensi/siswa', { q: query }, function(res) {
                let html = '';
                res.forEach(siswaKelas => {
                    html += `
                    <a href="#" class="list-group-item list-group-item-action siswa-item" data-id="${siswaKelas.id}">
                        <div class="d-flex w-100">
                            <h5 class="mb-1">${siswaKelas.siswa.nama_lengkap}</h5>
                        </div>
                        <p class="mb-1">${siswaKelas.kelas.nama_kelas}</p>
                        <small data-id="${siswaKelas.kelas.jenjang.id}">${siswaKelas.kelas.jenjang.nama_jenjang}</small>
                    </a>`;
                });
                $('.siswa-results .list-group').html(html);
            });
        } else {
            $('.siswa-results .list-group').html('<p class="text-center">Siswa Belum Dipilih</p>');
        }
    });

    // Ketika siswa dipilih
    $(document).on('click', '.siswa-item', function(e) {
        e.preventDefault();
        selectedSiswaKelas = $(this).data('id');
        $('#siswa-kelas-id').val(selectedSiswaKelas);

        // Highlight siswa yang dipilih
        $('.siswa-item').removeClass('active');
        $(this).addClass('active');

        // Reset pilihan bulan dan pos
        resetSelection();

        // Load tagihan siswa
        loadTagihanSiswaKelas(selectedSiswaKelas);

        // Load pos pemasukan untuk bulan ini
        loadPosPemasukan();
    });

    // Handle klik bulan
    $(document).on('click', '.bulan-item', function() {
        const status = $(this).data('status');
        const bulanId = $(this).data('id');


        // Jika status lunas, sementara tidak bisa dipilih
        if (status === 'LUNAS') {
            // tampil form-lunas
            console.log('form-lunas');

            return;
        }
        // // Jika status belum lunas, sementara tidak bisa dipilih
        // if (status === 'belum_lunas') {
        //     // tampil form-belum-lunas
        //     console.log('form-belum-lunas');

        //     return;
        // }

        // Jika status belum bayar, bisa dipilih
        if (status === 'BELUM BAYAR' || status === 'BELUM LUNAS') {
            // tampil form-belum-bayar
            console.log('form-belum-bayar');
            // Reset pilihan pos ketika ganti bulan
            selectedPos = {wajib: [], insidential: []};
            // updateTablePos();

            selectedBulan = bulanId;
            $('#bulan-selected').val(bulanId);

            // Update style bulan yang dipilih
            $('.bulan-item').removeClass('selected');
            $(this).addClass('selected');

            return;
        }
    });

    // Pencarian pos pembayaran
    $('#cari-pos').on('input', function() {
        const query = $(this).val().toLowerCase();
        $('.pos-item').each(function() {
            const text = $(this).text().toLowerCase();
            if (text.includes(query)) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });
    });

    // Handle klik item pos pembayaran
    $(document).on('click', '.pos-item', function(e) {
        e.preventDefault();

        const posId = $(this).data('id');
        const nominal = parseFloat($(this).data('nominal'));
        const wajib = $(this).data('wajib');
        const namaPos = $(this).text().split(' (')[0].trim();

        if (wajib == 1) {
            // Pos wajib - cek apakah sudah ada
            const existsWajib = selectedPos.wajib.some(pos => pos.id === posId);

            if (!existsWajib) {
                selectedPos.wajib.push({
                    id: posId,
                    nama: namaPos,
                    nominal: nominal
                });

                updateTablePos();
                hitungTotalDanSisa();
            }
        } else {
            // Pos insidential - selalu bisa ditambah
            selectedPos.insidential.push({
                // id: null,
                // keterangan: '',
                // nominal: 0
                id: posId,
                nama: namaPos,
                nominal: nominal
            });

            updateTablePos();
            hitungTotalDanSisa();
        }
    });

    // Fungsi untuk menghapus pos wajib dari tabel
    $(document).on('click', '.remove-pos-wajib', function() {
        const posId = parseInt($(this).data('id'));

        // Hapus dari array selectedPos.wajib
        selectedPos.wajib = selectedPos.wajib.filter(pos => pos.id !== posId);

        // Update tampilan tabel
        updateTablePos();
        hitungTotalDanSisa();
    });

    // Fungsi untuk menghapus pos insidential dari tabel
    $(document).on('click', '.remove-pos-insidential', function() {
        const index = parseInt($(this).data('index'));

        // Hapus dari array selectedPos.insidential berdasarkan index
        selectedPos.insidential.splice(index, 1);

        // Update tampilan tabel
        updateTablePos();
        hitungTotalDanSisa();
    });

    // Handle perubahan input keterangan insidential
    $(document).on('change', 'input[name="insidential[keterangan]"]', function() {
        const index = $(this).closest('tr').find('.remove-pos-insidential').data('index');
        if (selectedPos.insidential[index]) {
            selectedPos.insidential[index].keterangan = $(this).val();
        }

        updateTablePos();
        hitungTotalDanSisa();
    });

    // Handle perubahan input nominal insidential
    $(document).on('change', 'input[name="insidential[nominal]"]', function() {
        const index = $(this).closest('tr').find('.remove-pos-insidential').data('index');
        const nominal = parseFloat($(this).val()) || 0;

        if (selectedPos.insidential[index]) {
            selectedPos.insidential[index].nominal = nominal;
        }

        updateTablePos();
        hitungTotalDanSisa();
    });

    // Reset pilihan
    function resetSelection() {
        selectedBulan = null;
        selectedPos = {wajib: [], insidential: []};
        $('.bulan-item').removeClass('selected');
        $('#bulan-selected').val('');
        $('#pos-selected').val('');
        $('#nominal-dibayar').val('').data('numeric-value', 0);
        $('#sisa').val('');
        updateTablePos();
        $('.pos-results .list-group').html('');
    }

    // Load status tagihan
    function loadTagihanSiswaKelas(siswaKelasId) {
        $.get('/pemasukan/tagihan-siswa/' + siswaKelasId, function(response) {
            console.log(response);

            let currentMonthNoOrder = response.current_month.no_urut;

            //bulanan
            updateUIStatusBulan(response.tagihan.bulanan, currentMonthNoOrder);
            updateCurrentMonthHighlight(currentMonthNoOrder);

            //non-bulan
            updateUIStatusNonBulanan(response.tagihan.non_bulan);
        });
    }

    // Update tampilan bulan berdasarkan status tagihan
    function updateUIStatusBulan(tagihanBulanan, currentMonthNoOrder) {

        $('.bulan-item').each(function(i,v) {
            const key = i + 1; // Mulai dari 1
            const noUrut = parseInt($(this).data('nourut'));
            const status = tagihanBulanan[key] ? tagihanBulanan[key].status || 'belum_bayar' : 'belum_bayar';

            let statusClass = '';

            if (status === 'lunas') {
                statusClass = 'LUNAS';
                $(this).css('cursor', 'not-allowed');
                $(this).attr('title', 'buka lewat menu utama');
            } else if (noUrut > currentMonthNoOrder) {
                statusClass = 'BELUM TEMPO';
                $(this).css('cursor', 'pointer');
            } else {
                if(status === 'belum_bayar'){
                    statusClass = 'BELUM BAYAR';
                    $(this).css('cursor', 'pointer');
                }else if(status === 'belum_lunas'){
                    statusClass = 'BELUM LUNAS';
                    $(this).css('cursor', 'not-allowed');
                    $(this).attr('title', 'buka lewat menu utama');
                }
            }

            $(this).attr('data-status', statusClass);
            $(this).find('.status-text').text(statusClass);
        });
    }

    // Highlight bulan berjalan
    function updateCurrentMonthHighlight(currentMonthNoOrder) {
        $('.bulan-item').each(function() {
            const noUrut = parseInt($(this).data('nourut'));
            if(noUrut === parseInt(currentMonthNoOrder)) {
                $(this).find('.card').addClass('border border-2 border-warning');
            }
        });
    }

    function updateUIStatusNonBulanan(tagihanNonBulanan) {
        console.log(tagihanNonBulanan);
        // Update UI untuk tagihan non-bulanan
        $.each(tagihanNonBulanan, function (index, non_bulan) {
            if(non_bulan){
                $.each(non_bulan, function (i, nb) {
                    $('.non-bulan').append(`
                        <div class="col-md-6 col-sm-6 col-6 mb-3 non-bulan-item"
                        id="${index}"
                        data-id="${nb.id}"
                        data-status="${nb.status}"
                        data-nominal="${nb.nominal}"
                        style="cursor: pointer;">
                            <div class="card">
                                <div class="card-body text-center">
                                    <h5>${nb.pos_pemasukan.nama_pos_pemasukan}</h5>
                                    <p>Status: ${nb.status}</p>
                                </div>
                            </div>
                        </div>
                    `);
                });
            }
        });
    }

    // Load pos pemasukan berdasarkan jenjang
    function loadPosPemasukan() {
        if (!selectedSiswaKelas) return;

        // Ambil jenjang dari siswa yang dipilih
        // const jenjangText = $('.siswa-item.active').find('small').text();
        // const jenjangId = (jenjangText.includes('KB') || jenjangText.includes('TK')) ? 1 : 2;

        $.get('/referensi/pos-pemasukan', {
            // jenjang_id: jenjangId,
            // siswa_kelas_id: selectedSiswaKelas,
            // bulan_id: selectedBulan
        }, function(res) {
            let html = '';
            res.forEach(pos => {
                html += `<div class="list-group-item pos-item"
                        data-id="${pos.id}"
                        data-nominal="${pos.nominal_valid}"
                        data-wajib="${pos.wajib}"
                        data-bulanan="${pos.pembayaran == 'bulanan' ? 1 : 0}">
                        ${pos.nama_pos_pemasukan} (Rp ${formatRupiah(pos.nominal_valid)})
                        </div>`;
            });
            $('.pos-results .list-group').html(html);

            // Auto select pos wajib dan bulanan
            autoPosItemWajib();
        });
    }

    // Auto select pos wajib bulanan
    function autoPosItemWajib() {
        $('.pos-item').each(function () {
            let wajib = $(this).data('wajib');
            let bulanan = $(this).data('bulanan');
            if (wajib == 1 && bulanan == 1) {
                $(this).trigger('click');
            }
        });
    }

    // Update Tabel Pos
    function updateTablePos() {
        let totalWajib = 0;
        let totalInsidential = 0;
        let html = '';

        if (selectedPos.wajib.length === 0 && selectedPos.insidential.length === 0) {
            html = '<tr><td colspan="3" class="text-center text-muted">Pilih pos pembayaran</td></tr>';
        } else {
            // Tampilkan pos wajib
            if (selectedPos.wajib.length > 0) {
                selectedPos.wajib.forEach(item => {
                    const nominal = parseFloat(item.nominal);
                    totalWajib += nominal;

                    html += `
                    <tr>
                        <td>${item.nama}</td>
                        <td class="text-right">Rp ${formatRupiah(nominal)}</td>
                        <td class="text-center">
                            <button type="button" class="btn btn-sm btn-danger remove-pos-wajib" data-id="${item.id}">
                                <i class="fas fa-times"></i>
                            </button>
                        </td>
                    </tr>`;
                });
            }

            // Tampilkan pos insidential
            if (selectedPos.insidential.length > 0) {
                if (selectedPos.wajib.length > 0) {
                    html += '<tr><td colspan="3" class="text-center text-muted font-weight-bold">Insidential</td></tr>';
                }

                // item insidential

                selectedPos.insidential.forEach((item, index) => {
                    const nominal = parseFloat(item.nominal) || 0;
                    totalInsidential += nominal;

                    // html += `
                    // <tr>
                    //     <td>
                    //         <input type="text" name="insidential[keterangan]" class="form-control"
                    //             value="${item.keterangan}" placeholder="Keterangan..." list="datalistPosPemasukanInsidential">
                    //     </td>
                    //     <td class="text-right">
                    //         <input type="number" name="insidential[nominal]" class="form-control text-right"
                    //             value="${item.nominal}" step="500" min="0" placeholder="0">
                    //     </td>
                    //     <td class="text-center">
                    //         <button type="button" class="btn btn-sm btn-danger remove-pos-insidential" data-index="${index}">
                    //             <i class="fas fa-times"></i>
                    //         </button>
                    //     </td>
                    // </tr>`;
                    html += `
                    <tr>
                        <td>${item.nama}</td>
                        <td class="text-right">Rp ${formatRupiah(nominal)}</td>
                        <td class="text-center">
                            <button type="button" class="btn btn-sm btn-danger remove-pos-insidential" data-id="${index}">
                                <i class="fas fa-times"></i>
                            </button>
                        </td>
                    </tr>`;
                });
            }

            // Tampilkan total
            let total = totalWajib + totalInsidential;
            if (total > 0) {
                html += `
                <tr class="table-info">
                    <th>Total</th>
                    <th class="text-right" id="total-tagihan" data-total="${total}">Rp ${formatRupiah(total)}</th>
                    <th></th>
                </tr>`;
            }
        }

        $('table tbody').html(html);

        // Update hidden input untuk pos yang dipilih
        $('#pos-selected').val(JSON.stringify(selectedPos));
    }

    // Fungsi hitung total dan sisa
    function hitungTotalDanSisa() {
        let totalWajib = 0;
        let totalInsidential = 0;

        // Hitung total pos wajib
        selectedPos.wajib.forEach(item => {
            totalWajib += parseFloat(item.nominal) || 0;
        });

        // Hitung total pos insidential
        selectedPos.insidential.forEach(item => {
            totalInsidential += parseFloat(item.nominal) || 0;
        });

        const total = totalWajib + totalInsidential;
        const nominalDibayar = $('#nominal-dibayar').data('numeric-value') || 0;
        const sisa = total - nominalDibayar;

        // Update tampilan sisa
        if (sisa > 0) {
            $('#sisa').val('Rp ' + formatRupiah(sisa))
                .removeClass('text-success text-warning')
                .addClass('text-danger');
        } else if (sisa < 0) {
            $('#sisa').val('Rp ' + formatRupiah(Math.abs(sisa)) + ' (Kelebihan)')
                .removeClass('text-danger text-success')
                .addClass('text-warning');
        } else {
            $('#sisa').val('Rp 0 (Lunas)')
                .removeClass('text-danger text-warning')
                .addClass('text-success');
        }
    }
});
