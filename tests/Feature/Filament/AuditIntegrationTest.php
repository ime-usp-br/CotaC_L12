<?php

namespace Tests\Feature\Filament;

use App\Filament\Resources\CotaEspecialResource\Pages\CreateCotaEspecial;
use App\Models\Consumidor;
use App\Models\CotaEspecial;
use App\Models\CotaRegular;
use App\Models\Produto;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use OwenIt\Auditing\Models\Audit;
use Tests\TestCase;
use Uspdev\Replicado\Pessoa;

class AuditIntegrationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\RoleSeeder::class);
        
        // Ensure auditing is enabled for tests
        config(['audit.console' => true]);
    }

    public function test_produto_changes_are_audited(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('Admin');
        $this->actingAs($admin);

        // 1. Create
        $produto = Produto::create([
            'nome' => 'Produto Teste Audit',
            'valor' => 100,
        ]);

        $this->assertDatabaseHas('audits', [
            'auditable_type' => Produto::class,
            'auditable_id' => $produto->id,
            'event' => 'created',
        ]);

        // 2. Update
        $produto->update(['valor' => 150]);

        $this->assertDatabaseHas('audits', [
            'auditable_type' => Produto::class,
            'auditable_id' => $produto->id,
            'event' => 'updated',
            'old_values' => json_encode(['valor' => 100]),
            'new_values' => json_encode(['valor' => 150]),
        ]);
    }

    public function test_cota_regular_changes_are_audited(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('Admin');
        $this->actingAs($admin);

        // 1. Create
        $cota = CotaRegular::create([
            'vinculo' => 'DOCENTE_TESTE',
            'valor' => 500,
        ]);

        $this->assertDatabaseHas('audits', [
            'auditable_type' => CotaRegular::class,
            'auditable_id' => $cota->id,
            'event' => 'created',
        ]);

        // 2. Update
        $cota->update(['valor' => 600]);

        $this->assertDatabaseHas('audits', [
            'auditable_type' => CotaRegular::class,
            'auditable_id' => $cota->id,
            'event' => 'updated',
            'old_values' => json_encode(['valor' => 500]),
            'new_values' => json_encode(['valor' => 600]),
        ]);
    }

    public function test_cota_especial_creation_with_auto_consumer_and_audit(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('Admin');
        $this->actingAs($admin);

        $codpes = 123456;
        
        // Mock Replicado
        $pessoaMock = \Mockery::mock('alias:' . Pessoa::class);
        $pessoaMock->shouldReceive('dump')
            ->with($codpes)
            ->andReturn(['nompesttd' => 'Fulano de Tal']);

        // Ensure consumer does not exist
        $this->assertNull(Consumidor::find($codpes));

        // Simulate creating CotaEspecial via Filament Form
        Livewire::test(CreateCotaEspecial::class)
            ->set('data.consumidor_codpes', $codpes)
            ->set('data.valor', 1000)
            ->call('create')
            ->assertHasNoErrors();

        // 1. Verify Consumidor was created
        $this->assertNotNull(Consumidor::find($codpes));
        $this->assertEquals('Fulano de Tal', Consumidor::find($codpes)->nome);

        // 2. Verify CotaEspecial was created
        $cota = CotaEspecial::where('consumidor_codpes', $codpes)->first();
        $this->assertNotNull($cota);
        $this->assertEquals(1000, $cota->valor);

        // 3. Verify Audit Log was created
        $this->assertDatabaseHas('audits', [
            'auditable_type' => CotaEspecial::class,
            'auditable_id' => $cota->id,
            'event' => 'created',
        ]);
    }

    public function test_consumidor_changes_are_audited(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('Admin');
        $this->actingAs($admin);

        // 1. Create
        $consumidor = Consumidor::create([
            'codpes' => 987654,
            'nome' => 'Consumidor Teste Audit',
        ]);

        $this->assertDatabaseHas('audits', [
            'auditable_type' => Consumidor::class,
            'auditable_id' => $consumidor->codpes,
            'event' => 'created',
        ]);

        // 2. Update
        $consumidor->update(['nome' => 'Consumidor Teste Audit Editado']);

        $this->assertDatabaseHas('audits', [
            'auditable_type' => Consumidor::class,
            'auditable_id' => $consumidor->codpes,
            'event' => 'updated',
            'old_values' => json_encode(['nome' => 'Consumidor Teste Audit']),
            'new_values' => json_encode(['nome' => 'Consumidor Teste Audit Editado']),
        ]);
    }
}
