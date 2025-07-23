<?php

namespace App\Providers;

use Illuminate\Support\Facades\Event;
use Illuminate\Auth\Events\Registered;
use JeroenNoten\LaravelAdminLte\Events\BuildingMenu;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {

        Event::listen(BuildingMenu::class, function (BuildingMenu $event) {
            // Add some items to the menu...
            $event->menu->add('DATA MASTER');
            $event->menu->add([
                'text' => 'Siswa',
                'route' => 'siswa.main',
                'active' => ['siswa/form', 'siswa/form*', 'regex:@^siswa/form/[0-9]+$@'],
            ]);
            $event->menu->add([
                'text' => 'Jenjang',
                'route' => 'jenjang.main',
                'active' => ['jenjang/form', 'jenjang/form*', 'regex:@^jenjang/form/[0-9]+$@'],
            ]);
            $event->menu->add([
                'text' => 'Kelas',
                'route' => 'kelas.main',
                'active' => ['kelas/form', 'kelas/form*', 'regex:@^kelas/form/[0-9]+$@'],
            ]);
            $event->menu->add([
                'text' => 'Tahun Ajaran',
                'route' => 'tahun_ajaran.main',
                'active' => ['tahun-ajaran/form', 'tahun-ajaran/form*', 'regex:@^tahun-ajaran/form/[0-9]+$@'],
            ]);
            $event->menu->add([
                'text' => 'Kategori Dispensasi',
                'route' => 'kategori_dispensasi.main',
                'active' => ['kategori-dispensasi/form', 'kategori-dispensasi/form*', 'regex:@^kategori-dispensasi/form/[0-9]+$@'],
            ]);
            $event->menu->add([
                'text' => 'Siswa Dispensasi',
                'route' => 'siswa_dispensasi.main',
                'active' => [
                    'siswa-dispensasi/form', 'siswa-dispensasi/form*', 'regex:@^siswa-dispensasi/form/[0-9]+$@',
                    'siswa-dispensasi/detail', 'siswa-dispensasi/detail*', 'regex:@^siswa-dispensasi/detail/[0-9]+$@'
                ],
            ]);
            $event->menu->add('KESISWAAN');
            $event->menu->add([
                'text' => 'Siswa Naik Kelas',
                'route' => 'siswa_naik_kelas.main',
                'active' => ['siswa-naik-kelas/form', 'siswa-naik-kelas/form*', 'regex:@^siswa-naik-kelas/form/[0-9]+$@'],
            ]);
            $event->menu->add([
                'text' => 'Siswa Pindah Kelas',
                'route' => 'siswa_pindah_kelas.main',
                'active' => ['siswa-pindah-kelas/form', 'siswa-pindah-kelas/form*', 'regex:@^siswa-pindah-kelas/form/[0-9]+$@'],
            ]);
            $event->menu->add([
                'text' => 'Mutasi Siswa',
                'route' => 'siswa_mutasi.main',
                'active' => ['siswa-mutasi/form', 'siswa-mutasi/form*', 'regex:@^siswa-mutasi/form/[0-9]+$@'],
            ]);
            $event->menu->add([
                'text' => 'Kelulusan Siswa',
                'route' => 'siswa_kelulusan.main',
                'active' => ['siswa-kelulusan/form', 'siswa-kelulusan/form*', 'regex:@^siswa-kelulusan/form/[0-9]+$@'],
            ]);
            $event->menu->add('POS');
            $event->menu->add([
                'text' => 'Pos Pemasukan',
                'route' => 'pos_pemasukan.main',
                'active' => ['pos_pemasukan/form', 'pos_pemasukan/form*', 'regex:@^pos_pemasukan/form/[0-9]+$@'],
            ]);
            $event->menu->add([
                'text' => 'Pos Pengeluaran',
                'route' => 'pos_pengeluaran.main',
                'active' => ['pos_pengeluaran/form', 'pos_pengeluaran/form*', 'regex:@^pos_pengeluaran/form/[0-9]+$@'],
            ]);
            $event->menu->add('TRANSAKSI');
            $event->menu->add([
                'text' => 'Pemasukan',
                'route' => 'pemasukan.main',
                'active' => ['pemasukan/form', 'pemasukan/form*', 'regex:@^pemasukan/form/[0-9]+$@'],
            ]);
            $event->menu->add([
                'text' => 'Tagihan Siswa',
                'route' => 'tagihan_siswa.main',
                'active' => ['tagihan-siswa/form', 'tagihan-siswa/form*', 'regex:@^tagihan-siswa/form/[0-9]+$@'],
            ]);
        });
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
