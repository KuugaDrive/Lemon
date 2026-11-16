<?php

namespace Tests\Feature;

use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class MasterCrudTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $guru;
    protected User $guruAdmin;
    protected Kelas $kelasA;
    protected Kelas $kelasB;

    protected function setUp(): void
    {
        parent::setUp();

        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        Role::firstOrCreate(['name' => 'admin']);
        Role::firstOrCreate(['name' => 'guru']);

        $this->kelasA = Kelas::create(['nama_kelas' => 'Kelas Usman (A)', 'keterangan' => 'Kelas A']);
        $this->kelasB = Kelas::create(['nama_kelas' => 'Kelas Ali (B)', 'keterangan' => 'Kelas B']);

        $this->admin = User::factory()->create([
            'nip' => 'admin_test',
            'password' => bcrypt('password'),
        ]);
        $this->admin->assignRole('admin');

        $this->guru = User::factory()->create([
            'nip' => 'guru_test',
            'password' => bcrypt('password'),
        ]);
        $this->guru->assignRole('guru');
        $this->guru->kelas()->attach($this->kelasA->id);

        $this->guruAdmin = User::factory()->create([
            'nip' => 'guruadmin_test',
            'password' => bcrypt('password'),
        ]);
        $this->guruAdmin->assignRole(['guru', 'admin']);
        $this->guruAdmin->kelas()->attach($this->kelasB->id);
    }

    public function test_admin_can_crud_guru(): void
    {
        // 1. Index
        $response = $this->actingAs($this->admin)->get(route('master.guru.index'));
        $response->assertOk();
        $response->assertSee($this->guru->name);

        // 2. Create page
        $response = $this->actingAs($this->admin)->get(route('master.guru.create'));
        $response->assertOk();

        // 3. Store
        $storeResponse = $this->actingAs($this->admin)->post(route('master.guru.store'), [
            'nip' => '19951234',
            'name' => 'Ustadz Baru',
            'email' => 'baru@lemon.test',
            'no_hp' => '081234567899',
            'alamat' => 'Jakarta Timur',
            'password' => 'secret123',
            'roles' => ['guru'],
            'kelas_ids' => [$this->kelasA->id],
        ]);
        $storeResponse->assertRedirect(route('master.guru.index'));
        $storeResponse->assertSessionHas('success');

        $newGuru = User::where('nip', '19951234')->first();
        $this->assertNotNull($newGuru);
        $this->assertTrue($newGuru->hasRole('guru'));
        $this->assertTrue($newGuru->kelas->contains($this->kelasA->id));

        // 4. Edit page
        $response = $this->actingAs($this->admin)->get(route('master.guru.edit', $newGuru));
        $response->assertOk();
        $response->assertSee($newGuru->name);

        // 5. Update
        $updateResponse = $this->actingAs($this->admin)->put(route('master.guru.update', $newGuru), [
            'nip' => '19951234',
            'name' => 'Ustadz Baru Update',
            'email' => 'baru_edit@lemon.test',
            'no_hp' => '081234567888',
            'alamat' => 'Jakarta Barat',
            'roles' => ['guru', 'admin'],
            'kelas_ids' => [$this->kelasB->id],
        ]);
        $updateResponse->assertRedirect(route('master.guru.index'));
        $newGuru->refresh();
        $this->assertEquals('Ustadz Baru Update', $newGuru->name);
        $this->assertTrue($newGuru->hasRole('admin'));
        $this->assertTrue($newGuru->kelas->contains($this->kelasB->id));

        // 6. Delete
        $deleteResponse = $this->actingAs($this->admin)->delete(route('master.guru.destroy', $newGuru));
        $deleteResponse->assertRedirect(route('master.guru.index'));
        $deleteResponse->assertSessionHas('success');
        $this->assertNull(User::find($newGuru->id));
    }

    public function test_admin_cannot_delete_themselves(): void
    {
        $response = $this->actingAs($this->admin)->delete(route('master.guru.destroy', $this->admin));
        $response->assertRedirect(route('master.guru.index'));
        $response->assertSessionHas('error');
        $this->assertNotNull(User::find($this->admin->id));
    }

    public function test_admin_cannot_remove_admin_role_from_themselves(): void
    {
        $response = $this->actingAs($this->admin)->put(route('master.guru.update', $this->admin), [
            'nip' => $this->admin->nip,
            'name' => $this->admin->name,
            'roles' => ['guru'], // trying to strip admin role
        ]);
        $response->assertSessionHasErrors('roles');
        $this->admin->refresh();
        $this->assertTrue($this->admin->hasRole('admin'));
    }

    public function test_nip_duplicate_is_rejected(): void
    {
        $response = $this->actingAs($this->admin)->post(route('master.guru.store'), [
            'nip' => $this->guru->nip, // Duplicate NIP
            'name' => 'Duplikat',
            'password' => 'secret123',
            'roles' => ['guru'],
        ]);
        $response->assertSessionHasErrors('nip');
    }

    public function test_admin_can_crud_kelas(): void
    {
        // 1. Index
        $response = $this->actingAs($this->admin)->get(route('master.kelas.index'));
        $response->assertOk();
        $response->assertSee($this->kelasA->nama_kelas);

        // 2. Create
        $response = $this->actingAs($this->admin)->get(route('master.kelas.create'));
        $response->assertOk();

        // 3. Store
        $storeResponse = $this->actingAs($this->admin)->post(route('master.kelas.store'), [
            'nama_kelas' => 'Kelas Baru C',
            'keterangan' => 'Keterangan Kelas C',
        ]);
        $storeResponse->assertRedirect(route('master.kelas.index'));
        $kelasC = Kelas::where('nama_kelas', 'Kelas Baru C')->first();
        $this->assertNotNull($kelasC);

        // 4. Show
        $response = $this->actingAs($this->admin)->get(route('master.kelas.show', $kelasC));
        $response->assertOk();

        // 5. Edit & Update
        $response = $this->actingAs($this->admin)->get(route('master.kelas.edit', $kelasC));
        $response->assertOk();

        $updateResponse = $this->actingAs($this->admin)->put(route('master.kelas.update', $kelasC), [
            'nama_kelas' => 'Kelas Baru C Diubah',
            'keterangan' => 'Keterangan Baru',
        ]);
        $updateResponse->assertRedirect(route('master.kelas.index'));
        $kelasC->refresh();
        $this->assertEquals('Kelas Baru C Diubah', $kelasC->nama_kelas);

        // 6. Delete empty class
        $deleteResponse = $this->actingAs($this->admin)->delete(route('master.kelas.destroy', $kelasC));
        $deleteResponse->assertRedirect(route('master.kelas.index'));
        $this->assertNull(Kelas::find($kelasC->id));
    }

    public function test_kelas_with_students_cannot_be_deleted(): void
    {
        Siswa::create([
            'nis' => '999901',
            'nama_siswa' => 'Santri Kelas A',
            'nama_orang_tua' => 'Orang Tua',
            'kelas_id' => $this->kelasA->id,
        ]);

        $response = $this->actingAs($this->admin)->delete(route('master.kelas.destroy', $this->kelasA));
        $response->assertRedirect(route('master.kelas.index'));
        $response->assertSessionHas('error');
        $this->assertNotNull(Kelas::find($this->kelasA->id));
    }

    public function test_admin_can_crud_siswa(): void
    {
        // 1. Index
        $response = $this->actingAs($this->admin)->get(route('master.siswa.index'));
        $response->assertOk();

        // 2. Create
        $response = $this->actingAs($this->admin)->get(route('master.siswa.create'));
        $response->assertOk();

        // 3. Store
        $storeResponse = $this->actingAs($this->admin)->post(route('master.siswa.store'), [
            'nis' => '20268888',
            'nama_siswa' => 'Santri Cerdas',
            'nama_orang_tua' => 'Bapak Cerdas',
            'no_hp_ortu' => '081299998888',
            'alamat' => 'Jl. Merdeka No. 1',
            'kelas_id' => $this->kelasA->id,
        ]);
        $storeResponse->assertRedirect(route('master.siswa.index'));
        $siswa = Siswa::where('nis', '20268888')->first();
        $this->assertNotNull($siswa);

        // 4. Show
        $response = $this->actingAs($this->admin)->get(route('master.siswa.show', $siswa));
        $response->assertOk();
        $response->assertSee('Santri Cerdas');

        // 5. Edit & Update
        $response = $this->actingAs($this->admin)->get(route('master.siswa.edit', $siswa));
        $response->assertOk();

        $updateResponse = $this->actingAs($this->admin)->put(route('master.siswa.update', $siswa), [
            'nis' => '20268888',
            'nama_siswa' => 'Santri Cerdas Diperbarui',
            'nama_orang_tua' => 'Bapak Cerdas',
            'no_hp_ortu' => '081299998888',
            'alamat' => 'Jl. Merdeka No. 2',
            'kelas_id' => $this->kelasB->id,
        ]);
        $updateResponse->assertRedirect(route('master.siswa.index'));
        $siswa->refresh();
        $this->assertEquals('Santri Cerdas Diperbarui', $siswa->nama_siswa);
        $this->assertEquals($this->kelasB->id, $siswa->kelas_id);

        // 6. Delete
        $deleteResponse = $this->actingAs($this->admin)->delete(route('master.siswa.destroy', $siswa));
        $deleteResponse->assertRedirect(route('master.siswa.index'));
        $this->assertNull(Siswa::find($siswa->id));
    }

    public function test_nis_duplicate_is_rejected(): void
    {
        Siswa::create([
            'nis' => '20261111',
            'nama_siswa' => 'Santri 1',
            'nama_orang_tua' => 'Ortu 1',
            'kelas_id' => $this->kelasA->id,
        ]);

        $response = $this->actingAs($this->admin)->post(route('master.siswa.store'), [
            'nis' => '20261111', // duplicate NIS
            'nama_siswa' => 'Santri Duplikat',
            'nama_orang_tua' => 'Ortu 2',
            'kelas_id' => $this->kelasB->id,
        ]);
        $response->assertSessionHasErrors('nis');
    }

    public function test_guru_non_admin_gets_403_on_master_guru_and_master_kelas(): void
    {
        // Master Guru
        $this->actingAs($this->guru)->get(route('master.guru.index'))->assertForbidden();
        $this->actingAs($this->guru)->get(route('master.guru.create'))->assertForbidden();
        $this->actingAs($this->guru)->post(route('master.guru.store'), [])->assertForbidden();

        // Master Kelas
        $this->actingAs($this->guru)->get(route('master.kelas.index'))->assertForbidden();
        $this->actingAs($this->guru)->get(route('master.kelas.create'))->assertForbidden();
        $this->actingAs($this->guru)->post(route('master.kelas.store'), [])->assertForbidden();
    }

    public function test_guru_non_admin_cannot_create_update_delete_siswa(): void
    {
        $siswaA = Siswa::create([
            'nis' => '20262222',
            'nama_siswa' => 'Santri Di Kelas A',
            'nama_orang_tua' => 'Ortu A',
            'kelas_id' => $this->kelasA->id,
        ]);

        // Create disallowed
        $this->actingAs($this->guru)->get(route('master.siswa.create'))->assertForbidden();
        $this->actingAs($this->guru)->post(route('master.siswa.store'), [
            'nis' => '20263333',
            'nama_siswa' => 'Ilegal',
            'nama_orang_tua' => 'Ortu',
            'kelas_id' => $this->kelasA->id,
        ])->assertForbidden();

        // Edit & Update disallowed
        $this->actingAs($this->guru)->get(route('master.siswa.edit', $siswaA))->assertForbidden();
        $this->actingAs($this->guru)->put(route('master.siswa.update', $siswaA), [
            'nis' => $siswaA->nis,
            'nama_siswa' => 'Ganti Nama',
            'nama_orang_tua' => 'Ortu A',
            'kelas_id' => $this->kelasA->id,
        ])->assertForbidden();

        // Delete disallowed
        $this->actingAs($this->guru)->delete(route('master.siswa.destroy', $siswaA))->assertForbidden();
    }

    public function test_guru_can_only_view_students_in_assigned_classes(): void
    {
        $siswaA = Siswa::create([
            'nis' => '20264444',
            'nama_siswa' => 'Santri Bintang A',
            'nama_orang_tua' => 'Ortu A',
            'kelas_id' => $this->kelasA->id,
        ]);

        $siswaB = Siswa::create([
            'nis' => '20265555',
            'nama_siswa' => 'Santri Bintang B',
            'nama_orang_tua' => 'Ortu B',
            'kelas_id' => $this->kelasB->id,
        ]);

        // Guru mengampu Kelas A
        $response = $this->actingAs($this->guru)->get(route('master.siswa.index'));
        $response->assertOk();
        $response->assertSee('Santri Bintang A');
        $response->assertDontSee('Santri Bintang B');

        // Buka detail siswa kelas A (sukses 200)
        $this->actingAs($this->guru)->get(route('master.siswa.show', $siswaA))->assertOk();

        // Buka detail siswa kelas B (ditolak 403)
        $this->actingAs($this->guru)->get(route('master.siswa.show', $siswaB))->assertForbidden();
    }

    public function test_guru_merangkap_admin_can_access_master_and_crud_siswa(): void
    {
        // Can access master guru
        $this->actingAs($this->guruAdmin)->get(route('master.guru.index'))->assertOk();

        // Can access master kelas
        $this->actingAs($this->guruAdmin)->get(route('master.kelas.index'))->assertOk();

        // Can access siswa create
        $this->actingAs($this->guruAdmin)->get(route('master.siswa.create'))->assertOk();

        // Can view any siswa
        $siswaA = Siswa::create([
            'nis' => '20266666',
            'nama_siswa' => 'Santri Sembarang',
            'nama_orang_tua' => 'Ortu',
            'kelas_id' => $this->kelasA->id,
        ]);
        $this->actingAs($this->guruAdmin)->get(route('master.siswa.show', $siswaA))->assertOk();
    }
}
