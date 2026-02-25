<?php

namespace Tests\Feature;

use App\Models\CltLayer;
use App\Models\CltLayup;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SupplierApiTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    // ─── Supplier CRUD ───

    public function test_api_list_suppliers(): void
    {
        Supplier::factory()->count(3)->create();

        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson('/api/suppliers');

        $response->assertOk()
            ->assertJsonCount(3, 'data');
    }

    public function test_api_create_supplier(): void
    {
        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/suppliers', ['name' => 'API Supplier']);

        $response->assertCreated()
            ->assertJsonPath('data.name', 'API Supplier');
        $this->assertDatabaseHas('suppliers', ['name' => 'API Supplier']);
    }

    public function test_api_create_supplier_validation(): void
    {
        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/suppliers', ['name' => '']);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors('name');
    }

    public function test_api_show_supplier_with_nested_data(): void
    {
        $supplier = Supplier::factory()->create();
        $layup = CltLayup::factory()->create(['supplier_id' => $supplier->id]);
        CltLayer::factory()->create(['layup_id' => $layup->id]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson("/api/suppliers/{$supplier->id}");

        $response->assertOk()
            ->assertJsonPath('data.id', $supplier->id)
            ->assertJsonCount(1, 'data.layups')
            ->assertJsonCount(1, 'data.layups.0.layers');
    }

    public function test_api_update_supplier(): void
    {
        $supplier = Supplier::factory()->create(['name' => 'Old']);

        $response = $this->actingAs($this->user, 'sanctum')
            ->putJson("/api/suppliers/{$supplier->id}", ['name' => 'New']);

        $response->assertOk()
            ->assertJsonPath('data.name', 'New');
    }

    public function test_api_delete_supplier(): void
    {
        $supplier = Supplier::factory()->create();

        $response = $this->actingAs($this->user, 'sanctum')
            ->deleteJson("/api/suppliers/{$supplier->id}");

        $response->assertOk();
        $this->assertDatabaseMissing('suppliers', ['id' => $supplier->id]);
    }

    // ─── Layup CRUD ───

    public function test_api_list_layups(): void
    {
        $supplier = Supplier::factory()->create();
        CltLayup::factory()->count(2)->create(['supplier_id' => $supplier->id]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson("/api/suppliers/{$supplier->id}/layups");

        $response->assertOk()
            ->assertJsonCount(2, 'data');
    }

    public function test_api_create_layup(): void
    {
        $supplier = Supplier::factory()->create();

        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson("/api/suppliers/{$supplier->id}/layups", ['name' => 'API Layup']);

        $response->assertCreated()
            ->assertJsonPath('data.name', 'API Layup');
    }

    public function test_api_update_layup(): void
    {
        $supplier = Supplier::factory()->create();
        $layup = CltLayup::factory()->create(['supplier_id' => $supplier->id, 'name' => 'Old']);

        $response = $this->actingAs($this->user, 'sanctum')
            ->putJson("/api/suppliers/{$supplier->id}/layups/{$layup->id}", ['name' => 'New']);

        $response->assertOk()
            ->assertJsonPath('data.name', 'New');
    }

    public function test_api_delete_layup(): void
    {
        $supplier = Supplier::factory()->create();
        $layup = CltLayup::factory()->create(['supplier_id' => $supplier->id]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->deleteJson("/api/suppliers/{$supplier->id}/layups/{$layup->id}");

        $response->assertOk();
        $this->assertDatabaseMissing('clt_layups', ['id' => $layup->id]);
    }

    // ─── Layer CRUD ───

    public function test_api_list_layers(): void
    {
        $supplier = Supplier::factory()->create();
        $layup = CltLayup::factory()->create(['supplier_id' => $supplier->id]);
        CltLayer::factory()->count(3)->create(['layup_id' => $layup->id]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson("/api/suppliers/{$supplier->id}/layups/{$layup->id}/layers");

        $response->assertOk()
            ->assertJsonCount(3, 'data');
    }

    public function test_api_create_layer(): void
    {
        $supplier = Supplier::factory()->create();
        $layup = CltLayup::factory()->create(['supplier_id' => $supplier->id]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson("/api/suppliers/{$supplier->id}/layups/{$layup->id}/layers", [
                'layer_order' => 1,
                'thickness' => 25.50,
                'width' => 120.00,
                'angle' => 90,
            ]);

        $response->assertCreated()
            ->assertJsonPath('data.layer_order', 1);
        $this->assertEquals(25.50, $response->json('data.thickness'));
    }

    public function test_api_update_layer(): void
    {
        $supplier = Supplier::factory()->create();
        $layup = CltLayup::factory()->create(['supplier_id' => $supplier->id]);
        $layer = CltLayer::factory()->create([
            'layup_id' => $layup->id,
            'layer_order' => 1,
            'thickness' => 20,
            'width' => 100,
            'angle' => 0,
        ]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->putJson("/api/suppliers/{$supplier->id}/layups/{$layup->id}/layers/{$layer->id}", [
                'layer_order' => 2,
                'thickness' => 30,
                'width' => 150,
                'angle' => 90,
            ]);

        $response->assertOk()
            ->assertJsonPath('data.layer_order', 2);
        $this->assertEquals(30, $response->json('data.thickness'));
    }

    public function test_api_delete_layer(): void
    {
        $supplier = Supplier::factory()->create();
        $layup = CltLayup::factory()->create(['supplier_id' => $supplier->id]);
        $layer = CltLayer::factory()->create(['layup_id' => $layup->id]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->deleteJson("/api/suppliers/{$supplier->id}/layups/{$layup->id}/layers/{$layer->id}");

        $response->assertOk();
        $this->assertDatabaseMissing('clt_layers', ['id' => $layer->id]);
    }

    // ─── Export & Import ───

    public function test_api_export_supplier(): void
    {
        $supplier = Supplier::factory()->create(['name' => 'ExportMe']);
        $layup = CltLayup::factory()->create(['supplier_id' => $supplier->id, 'name' => 'L1']);
        CltLayer::factory()->create(['layup_id' => $layup->id, 'layer_order' => 1]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson("/api/suppliers/{$supplier->id}/export");

        $response->assertOk()
            ->assertJsonPath('supplier.name', 'ExportMe')
            ->assertJsonCount(1, 'supplier.layups');
    }

    public function test_api_import_with_overwrite(): void
    {
        $supplier = Supplier::factory()->create();
        $layup = CltLayup::factory()->create([
            'supplier_id' => $supplier->id,
            'name' => 'Existing',
        ]);
        CltLayer::factory()->create([
            'layup_id' => $layup->id,
            'layer_order' => 1,
            'thickness' => 20,
            'width' => 100,
            'angle' => 0,
        ]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson("/api/suppliers/{$supplier->id}/import", [
                'strategy' => 'overwrite',
                'data' => [
                    'supplier' => [
                        'name' => $supplier->name,
                        'layups' => [
                            [
                                'name' => 'Existing',
                                'layers' => [
                                    ['layer_order' => 1, 'thickness' => 30, 'width' => 150, 'angle' => 90],
                                ],
                            ],
                        ],
                    ],
                ],
            ]);

        $response->assertOk();
        $this->assertDatabaseHas('clt_layers', [
            'layup_id' => $layup->id,
            'layer_order' => 1,
            'thickness' => 30,
        ]);
    }

    public function test_api_import_with_reject(): void
    {
        $supplier = Supplier::factory()->create();
        $layup = CltLayup::factory()->create([
            'supplier_id' => $supplier->id,
            'name' => 'Existing',
        ]);
        CltLayer::factory()->create([
            'layup_id' => $layup->id,
            'layer_order' => 1,
            'thickness' => 20,
            'width' => 100,
            'angle' => 0,
        ]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson("/api/suppliers/{$supplier->id}/import", [
                'strategy' => 'reject',
                'data' => [
                    'supplier' => [
                        'name' => $supplier->name,
                        'layups' => [
                            [
                                'name' => 'Existing',
                                'layers' => [
                                    ['layer_order' => 1, 'thickness' => 30, 'width' => 150, 'angle' => 90],
                                ],
                            ],
                        ],
                    ],
                ],
            ]);

        $response->assertStatus(409);
        // Original data untouched
        $this->assertDatabaseHas('clt_layers', ['thickness' => 20]);
    }

    // ─── Auth Guard ───

    public function test_api_unauthenticated_returns_401(): void
    {
        $response = $this->getJson('/api/suppliers');
        $response->assertUnauthorized();
    }
}
