<?php

namespace Tests\Feature;

use App\Models\Alfabet;
use App\Models\Doa;
use App\Models\Hadist;
use App\Models\Iqro;
use App\Models\Surat;
use App\Models\User;
use Database\Seeders\AlfabetSeeder;
use Database\Seeders\DoaSeeder;
use Database\Seeders\HadistSeeder;
use Database\Seeders\IqroSeeder;
use Database\Seeders\SuratSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class KatalogTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $guru;
    protected User $guruAdmin;

    protected function setUp(): void
    {
        parent::setUp();

        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        Role::firstOrCreate(['name' => 'admin']);
        Role::firstOrCreate(['name' => 'guru']);

        $this->admin = User::factory()->create(['nip' => 'admin_katalog']);
        $this->admin->assignRole('admin');

        $this->guru = User::factory()->create(['nip' => 'guru_katalog']);
        $this->guru->assignRole('guru');

        $this->guruAdmin = User::factory()->create(['nip' => 'guruadmin_katalog']);
        $this->guruAdmin->assignRole(['guru', 'admin']);
    }

    public function test_seeders_are_accurate_and_idempotent(): void
    {
        $this->seed(IqroSeeder::class);
        $this->seed(SuratSeeder::class);
        $this->seed(AlfabetSeeder::class);
        $this->seed(DoaSeeder::class);
        $this->seed(HadistSeeder::class);

        $this->assertEquals(6, Iqro::count());
        $this->assertEquals(114, Surat::count());
        $this->assertEquals(6236, Surat::sum('jumlah_ayat'));
        $this->assertEquals(28, Alfabet::count());
        $this->assertEquals(10, Doa::count());
        $this->assertGreaterThanOrEqual(5, Hadist::count());

        // Jalankan ulang seeder: data tidak boleh terduplikasi
        $this->seed(IqroSeeder::class);
        $this->seed(SuratSeeder::class);
        $this->seed(AlfabetSeeder::class);
        $this->seed(DoaSeeder::class);
        $this->seed(HadistSeeder::class);

        $this->assertEquals(6, Iqro::count());
        $this->assertEquals(114, Surat::count());
        $this->assertEquals(6236, Surat::sum('jumlah_ayat'));
        $this->assertEquals(28, Alfabet::count());
        $this->assertEquals(10, Doa::count());
    }

    public function test_admin_can_crud_iqro(): void
    {
        $response = $this->actingAs($this->admin)->get(route('katalog.iqro.index'));
        $response->assertOk();

        // Store
        $storeResponse = $this->actingAs($this->admin)->post(route('katalog.iqro.store'), [
            'nama_iqro' => 'Iqro Spesial',
            'jumlah_halaman' => 35,
            'ket' => 'Edisi revisi',
        ]);
        $storeResponse->assertRedirect(route('katalog.iqro.index'));
        $iqro = Iqro::where('nama_iqro', 'Iqro Spesial')->first();
        $this->assertNotNull($iqro);

        // Update
        $this->actingAs($this->admin)->put(route('katalog.iqro.update', $iqro), [
            'nama_iqro' => 'Iqro Spesial Update',
            'jumlah_halaman' => 40,
        ])->assertRedirect(route('katalog.iqro.index'));
        $this->assertEquals(40, $iqro->fresh()->jumlah_halaman);

        // Delete
        $this->actingAs($this->admin)->delete(route('katalog.iqro.destroy', $iqro))
            ->assertRedirect(route('katalog.iqro.index'));
        $this->assertNull(Iqro::find($iqro->id));
    }

    public function test_admin_can_crud_surat(): void
    {
        $response = $this->actingAs($this->admin)->get(route('katalog.surat.index'));
        $response->assertOk();

        $storeResponse = $this->actingAs($this->admin)->post(route('katalog.surat.store'), [
            'nomor' => 99,
            'nama_surat' => 'Az-Zalzalah Test',
            'juz' => 30,
            'jumlah_ayat' => 8,
        ]);
        $storeResponse->assertRedirect(route('katalog.surat.index'));
        $surat = Surat::where('nomor', 99)->first();
        $this->assertNotNull($surat);

        // Update
        $this->actingAs($this->admin)->put(route('katalog.surat.update', $surat), [
            'nomor' => 99,
            'nama_surat' => 'Az-Zalzalah Edited',
            'juz' => 30,
            'jumlah_ayat' => 8,
        ])->assertRedirect(route('katalog.surat.index'));
        $this->assertEquals('Az-Zalzalah Edited', $surat->fresh()->nama_surat);

        // Delete
        $this->actingAs($this->admin)->delete(route('katalog.surat.destroy', $surat))
            ->assertRedirect(route('katalog.surat.index'));
        $this->assertNull(Surat::find($surat->id));
    }

    public function test_admin_can_crud_doa_hadist_alfabet(): void
    {
        // Doa
        $this->actingAs($this->admin)->post(route('katalog.doa.store'), [
            'nama_doa' => 'Doa Mau Tidur Test',
            'baca_latin' => 'Bismika...',
            'artinya' => 'Dengan nama-Mu...',
        ])->assertRedirect(route('katalog.doa.index'));
        $doa = Doa::where('nama_doa', 'Doa Mau Tidur Test')->first();
        $this->assertNotNull($doa);
        $this->actingAs($this->admin)->delete(route('katalog.doa.destroy', $doa));
        $this->assertNull(Doa::find($doa->id));

        // Hadist
        $this->actingAs($this->admin)->post(route('katalog.hadist.store'), [
            'nama_hadist' => 'Hadits Niat Test',
            'baca_latin' => 'Innamal a\'maalu...',
            'artinya' => 'Sesungguhnya amal...',
        ])->assertRedirect(route('katalog.hadist.index'));
        $hadist = Hadist::where('nama_hadist', 'Hadits Niat Test')->first();
        $this->assertNotNull($hadist);
        $this->actingAs($this->admin)->delete(route('katalog.hadist.destroy', $hadist));
        $this->assertNull(Hadist::find($hadist->id));

        // Alfabet
        $this->actingAs($this->admin)->post(route('katalog.alfabet.store'), [
            'nama_alfabet' => 'Alif Test',
            'ket' => 'ا',
        ])->assertRedirect(route('katalog.alfabet.index'));
        $alfabet = Alfabet::where('nama_alfabet', 'Alif Test')->first();
        $this->assertNotNull($alfabet);
        $this->actingAs($this->admin)->delete(route('katalog.alfabet.destroy', $alfabet));
        $this->assertNull(Alfabet::find($alfabet->id));
    }

    public function test_duplicate_validation_in_katalog(): void
    {
        Iqro::create(['nama_iqro' => 'Iqro Unik', 'jumlah_halaman' => 30]);
        $response = $this->actingAs($this->admin)->post(route('katalog.iqro.store'), [
            'nama_iqro' => 'Iqro Unik',
            'jumlah_halaman' => 30,
        ]);
        $response->assertSessionHasErrors('nama_iqro');

        Surat::create(['nomor' => 50, 'nama_surat' => 'Qaf', 'juz' => 26, 'jumlah_ayat' => 45]);
        $response = $this->actingAs($this->admin)->post(route('katalog.surat.store'), [
            'nomor' => 50,
            'nama_surat' => 'Qaf Duplikat',
            'juz' => 26,
            'jumlah_ayat' => 45,
        ]);
        $response->assertSessionHasErrors('nomor');

        Alfabet::create(['nama_alfabet' => 'Ba Unik']);
        $response = $this->actingAs($this->admin)->post(route('katalog.alfabet.store'), [
            'nama_alfabet' => 'Ba Unik',
        ]);
        $response->assertSessionHasErrors('nama_alfabet');
    }

    public function test_guru_non_admin_gets_403_on_all_katalog_routes(): void
    {
        $this->actingAs($this->guru)->get(route('katalog.iqro.index'))->assertForbidden();
        $this->actingAs($this->guru)->get(route('katalog.surat.index'))->assertForbidden();
        $this->actingAs($this->guru)->get(route('katalog.doa.index'))->assertForbidden();
        $this->actingAs($this->guru)->get(route('katalog.hadist.index'))->assertForbidden();
        $this->actingAs($this->guru)->get(route('katalog.alfabet.index'))->assertForbidden();
    }

    public function test_guru_merangkap_admin_can_access_katalog(): void
    {
        $this->actingAs($this->guruAdmin)->get(route('katalog.iqro.index'))->assertOk();
        $this->actingAs($this->guruAdmin)->get(route('katalog.surat.index'))->assertOk();
    }
}
