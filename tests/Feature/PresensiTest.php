<?php

namespace Tests\Feature;

use App\Enums\StatusPresensi;
use App\Models\Kelas;
use App\Models\Presensi;
use App\Models\Siswa;
use App\Models\User;
use App\Services\PresensiRekap;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class PresensiTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $guru1;
    protected User $guru2;
    protected Kelas $kelasA;
    protected Kelas $kelasB;
    protected Siswa $siswaA1;
    protected Siswa $siswaA2;
    protected Siswa $siswaB1;

    protected function setUp(): void
    {
        parent::setUp();

        Role::create(['name' => 'admin']);
        Role::create(['name' => 'guru']);

        $this->admin = User::factory()->create(['nip' => 'ADM001', 'name' => 'Admin Utama']);
        $this->admin->assignRole('admin');

        $this->guru1 = User::factory()->create(['nip' => 'GUR001', 'name' => 'Guru Kelas A']);
        $this->guru1->assignRole('guru');

        $this->guru2 = User::factory()->create(['nip' => 'GUR002', 'name' => 'Guru Kelas B']);
        $this->guru2->assignRole('guru');

        $this->kelasA = Kelas::create(['nama_kelas' => 'Kelas A (Abu Bakar)']);
        $this->kelasB = Kelas::create(['nama_kelas' => 'Kelas B (Umar bin Khattab)']);

        $this->guru1->kelas()->attach($this->kelasA->id);
        $this->guru2->kelas()->attach($this->kelasB->id);

        $this->siswaA1 = Siswa::create([
            'nis' => '1001',
            'nama_siswa' => 'Ahmad Santri',
            'alamat' => 'Jl. Mawar 1',
            'nama_orang_tua' => 'Bapak Ahmad',
            'no_hp_ortu' => '08123456789',
            'kelas_id' => $this->kelasA->id,
        ]);

        $this->siswaA2 = Siswa::create([
            'nis' => '1002',
            'nama_siswa' => 'Aisyah Santri',
            'alamat' => 'Jl. Mawar 2',
            'nama_orang_tua' => 'Bapak Aisyah',
            'no_hp_ortu' => '08123456788',
            'kelas_id' => $this->kelasA->id,
        ]);

        $this->siswaB1 = Siswa::create([
            'nis' => '2001',
            'nama_siswa' => 'Budi Santri',
            'alamat' => 'Jl. Melati 1',
            'nama_orang_tua' => 'Bapak Budi',
            'no_hp_ortu' => '08123456787',
            'kelas_id' => $this->kelasB->id,
        ]);
    }

    public function test_guru_dapat_melihat_halaman_input_presensi_hanya_untuk_kelas_miliknya(): void
    {
        $response = $this->actingAs($this->guru1)->get(route('presensi.index', ['kelas_id' => $this->kelasA->id]));
        $response->assertStatus(200);
        $response->assertSee('Ahmad Santri');
        $response->assertSee('Aisyah Santri');

        // Guru 1 mencoba akses Kelas B -> 403 Forbidden
        $responseB = $this->actingAs($this->guru1)->get(route('presensi.index', ['kelas_id' => $this->kelasB->id]));
        $responseB->assertStatus(403);
    }

    public function test_admin_dapat_melihat_dan_mengakses_semua_kelas_presensi(): void
    {
        $responseA = $this->actingAs($this->admin)->get(route('presensi.index', ['kelas_id' => $this->kelasA->id]));
        $responseA->assertStatus(200);
        $responseA->assertSee('Ahmad Santri');

        $responseB = $this->actingAs($this->admin)->get(route('presensi.index', ['kelas_id' => $this->kelasB->id]));
        $responseB->assertStatus(200);
        $responseB->assertSee('Budi Santri');
    }

    public function test_simpan_presensi_massal_berhasil_dan_upsert(): void
    {
        $today = now()->toDateString();

        $payload = [
            'kelas_id' => $this->kelasA->id,
            'tanggal' => $today,
            'presensi' => [
                [
                    'siswa_id' => $this->siswaA1->id,
                    'status' => 'hadir',
                    'ket' => 'Tepat waktu',
                ],
                [
                    'siswa_id' => $this->siswaA2->id,
                    'status' => 'sakit',
                    'ket' => 'Demam flu',
                ],
            ],
        ];

        $response = $this->actingAs($this->guru1)->post(route('presensi.store'), $payload);
        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('presensis', [
            'siswa_id' => $this->siswaA1->id,
            'status' => 'hadir',
            'guru_id' => $this->guru1->id,
            'ket' => 'Tepat waktu',
        ]);

        $this->assertDatabaseHas('presensis', [
            'siswa_id' => $this->siswaA2->id,
            'status' => 'sakit',
            'guru_id' => $this->guru1->id,
            'ket' => 'Demam flu',
        ]);

        // Ulangi kirim data tanggal sama (update/upsert, tidak boleh duplikat)
        $updatePayload = [
            'kelas_id' => $this->kelasA->id,
            'tanggal' => $today,
            'presensi' => [
                [
                    'siswa_id' => $this->siswaA1->id,
                    'status' => 'izin',
                    'ket' => 'Acara keluarga',
                ],
                [
                    'siswa_id' => $this->siswaA2->id,
                    'status' => 'hadir',
                    'ket' => null,
                ],
            ],
        ];

        $responseUpdate = $this->actingAs($this->guru1)->post(route('presensi.store'), $updatePayload);
        $responseUpdate->assertRedirect();

        $this->assertDatabaseCount('presensis', 2);
        $this->assertDatabaseHas('presensis', [
            'siswa_id' => $this->siswaA1->id,
            'status' => 'izin',
            'ket' => 'Acara keluarga',
        ]);
        $this->assertDatabaseHas('presensis', [
            'siswa_id' => $this->siswaA2->id,
            'status' => 'hadir',
        ]);
    }

    public function test_guru_tidak_bisa_input_presensi_siswa_kelas_lain(): void
    {
        $payload = [
            'kelas_id' => $this->kelasA->id,
            'tanggal' => now()->toDateString(),
            'presensi' => [
                [
                    'siswa_id' => $this->siswaB1->id, // Siswa kelas B dimasukkan di form kelas A
                    'status' => 'hadir',
                ],
            ],
        ];

        $response = $this->actingAs($this->guru1)->post(route('presensi.store'), $payload);
        $response->assertSessionHasErrors('presensi');
    }

    public function test_tanggal_presensi_masa_depan_ditolak(): void
    {
        $futureDate = now()->addDays(2)->toDateString();

        $payload = [
            'kelas_id' => $this->kelasA->id,
            'tanggal' => $futureDate,
            'presensi' => [
                [
                    'siswa_id' => $this->siswaA1->id,
                    'status' => 'hadir',
                ],
            ],
        ];

        $response = $this->actingAs($this->guru1)->post(route('presensi.store'), $payload);
        $response->assertSessionHasErrors('tanggal');
    }

    public function test_status_presensi_tidak_valid_ditolak(): void
    {
        $payload = [
            'kelas_id' => $this->kelasA->id,
            'tanggal' => now()->toDateString(),
            'presensi' => [
                [
                    'siswa_id' => $this->siswaA1->id,
                    'status' => 'bolus', // Status ngawur
                ],
            ],
        ];

        $response = $this->actingAs($this->guru1)->post(route('presensi.store'), $payload);
        $response->assertSessionHasErrors('presensi.0.status');
    }

    public function test_rekap_presensi_menghitung_statistik_dengan_benar(): void
    {
        $d1 = '2026-11-01';
        $d2 = '2026-11-02';
        $d3 = '2026-11-03';
        $d4 = '2026-11-04';

        Presensi::create(['siswa_id' => $this->siswaA1->id, 'guru_id' => $this->guru1->id, 'tanggal' => $d1, 'status' => StatusPresensi::Hadir]);
        Presensi::create(['siswa_id' => $this->siswaA1->id, 'guru_id' => $this->guru1->id, 'tanggal' => $d2, 'status' => StatusPresensi::Hadir]);
        Presensi::create(['siswa_id' => $this->siswaA1->id, 'guru_id' => $this->guru1->id, 'tanggal' => $d3, 'status' => StatusPresensi::Izin]);
        Presensi::create(['siswa_id' => $this->siswaA1->id, 'guru_id' => $this->guru1->id, 'tanggal' => $d4, 'status' => StatusPresensi::Sakit]);

        $rekap = PresensiRekap::hitung($this->kelasA->id, '2026-11-01', '2026-11-30', $this->guru1);
        $rowA1 = $rekap->firstWhere('siswa.id', $this->siswaA1->id);

        $this->assertNotNull($rowA1);
        $this->assertEquals(2, $rowA1->hadir);
        $this->assertEquals(1, $rowA1->izin);
        $this->assertEquals(1, $rowA1->sakit);
        $this->assertEquals(0, $rowA1->alfa);
        $this->assertEquals(4, $rowA1->total_hari);
        $this->assertEquals(50, $rowA1->persentase); // 2/4 = 50%
    }

    public function test_siswa_dengan_riwayat_presensi_tidak_dapat_dihapus(): void
    {
        Presensi::create([
            'siswa_id' => $this->siswaA1->id,
            'guru_id' => $this->guru1->id,
            'tanggal' => now()->toDateString(),
            'status' => StatusPresensi::Hadir,
        ]);

        $response = $this->actingAs($this->admin)->delete(route('master.siswa.destroy', $this->siswaA1));
        $response->assertRedirect(route('master.siswa.index'));
        $response->assertSessionHas('error');

        $this->assertDatabaseHas('siswas', ['id' => $this->siswaA1->id]);
    }
}
