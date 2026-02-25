<?php

namespace Tests\Feature;

use App\Models\CltLayer;
use App\Models\CltLayup;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SupplierCrudTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_supplier_index_page_is_displayed(): void
    {
        $response = $this->actingAs($this->user)->get(route('suppliers.index'));
        $response->assertStatus(200);
        $response->assertSee('Suppliers');
    }

    public function test_supplier_can_be_created(): void
    {
        $response = $this->actingAs($this->user)->post(route('suppliers.store'), [
            'name' => 'Test Supplier',
        ]);

        $response->assertRedirect(route('suppliers.index'));
        $this->assertDatabaseHas('suppliers', ['name' => 'Test Supplier']);
    }

    public function test_supplier_creation_requires_name(): void
    {
        $response = $this->actingAs($this->user)->post(route('suppliers.store'), [
            'name' => '',
        ]);

        $response->assertSessionHasErrors('name');
    }

    public function test_supplier_can_be_updated(): void
    {
        $supplier = Supplier::factory()->create(['name' => 'Old Name']);

        $response = $this->actingAs($this->user)->put(route('suppliers.update', $supplier), [
            'name' => 'New Name',
        ]);

        $response->assertRedirect(route('suppliers.index'));
        $this->assertDatabaseHas('suppliers', ['id' => $supplier->id, 'name' => 'New Name']);
    }

    public function test_supplier_can_be_deleted(): void
    {
        $supplier = Supplier::factory()->create();

        $response = $this->actingAs($this->user)->delete(route('suppliers.destroy', $supplier));

        $response->assertRedirect(route('suppliers.index'));
        $this->assertDatabaseMissing('suppliers', ['id' => $supplier->id]);
    }

    public function test_supplier_deletion_cascades_to_layups_and_layers(): void
    {
        $supplier = Supplier::factory()->create();
        $layup = CltLayup::factory()->create(['supplier_id' => $supplier->id]);
        $layer = CltLayer::factory()->create(['layup_id' => $layup->id]);

        $this->actingAs($this->user)->delete(route('suppliers.destroy', $supplier));

        $this->assertDatabaseMissing('suppliers', ['id' => $supplier->id]);
        $this->assertDatabaseMissing('clt_layups', ['id' => $layup->id]);
        $this->assertDatabaseMissing('clt_layers', ['id' => $layer->id]);
    }

    public function test_supplier_show_page_displays_layups_and_layers(): void
    {
        $supplier = Supplier::factory()->create();
        $layup = CltLayup::factory()->create(['supplier_id' => $supplier->id, 'name' => 'TestLayup']);
        CltLayer::factory()->create(['layup_id' => $layup->id, 'layer_order' => 1, 'thickness' => 20.00]);

        $response = $this->actingAs($this->user)->get(route('suppliers.show', $supplier));

        $response->assertStatus(200);
        $response->assertSee('TestLayup');
        $response->assertSee('20.00');
    }

    public function test_unauthenticated_user_cannot_access_suppliers(): void
    {
        $response = $this->get(route('suppliers.index'));
        $response->assertRedirect(route('login'));
    }
}
