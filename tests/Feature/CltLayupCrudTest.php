<?php

namespace Tests\Feature;

use App\Models\CltLayer;
use App\Models\CltLayup;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CltLayupCrudTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Supplier $supplier;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->supplier = Supplier::factory()->create();
    }

    public function test_layup_index_page_is_displayed(): void
    {
        $response = $this->actingAs($this->user)->get(route('suppliers.layups.index', $this->supplier));
        $response->assertStatus(200);
    }

    public function test_layup_can_be_created(): void
    {
        $response = $this->actingAs($this->user)->post(
            route('suppliers.layups.store', $this->supplier),
            ['name' => 'Test Layup']
        );

        $response->assertRedirect(route('suppliers.layups.index', $this->supplier));
        $this->assertDatabaseHas('clt_layups', [
            'name' => 'Test Layup',
            'supplier_id' => $this->supplier->id,
        ]);
    }

    public function test_layup_creation_requires_name(): void
    {
        $response = $this->actingAs($this->user)->post(
            route('suppliers.layups.store', $this->supplier),
            ['name' => '']
        );

        $response->assertSessionHasErrors('name');
    }

    public function test_layup_can_be_updated(): void
    {
        $layup = CltLayup::factory()->create([
            'supplier_id' => $this->supplier->id,
            'name' => 'Old Layup',
        ]);

        $response = $this->actingAs($this->user)->put(
            route('suppliers.layups.update', [$this->supplier, $layup]),
            ['name' => 'Updated Layup']
        );

        $response->assertRedirect(route('suppliers.layups.index', $this->supplier));
        $this->assertDatabaseHas('clt_layups', ['id' => $layup->id, 'name' => 'Updated Layup']);
    }

    public function test_layup_can_be_deleted(): void
    {
        $layup = CltLayup::factory()->create(['supplier_id' => $this->supplier->id]);

        $response = $this->actingAs($this->user)->delete(
            route('suppliers.layups.destroy', [$this->supplier, $layup])
        );

        $response->assertRedirect(route('suppliers.layups.index', $this->supplier));
        $this->assertDatabaseMissing('clt_layups', ['id' => $layup->id]);
    }

    public function test_layup_deletion_cascades_to_layers(): void
    {
        $layup = CltLayup::factory()->create(['supplier_id' => $this->supplier->id]);
        $layer = CltLayer::factory()->create(['layup_id' => $layup->id]);

        $this->actingAs($this->user)->delete(
            route('suppliers.layups.destroy', [$this->supplier, $layup])
        );

        $this->assertDatabaseMissing('clt_layups', ['id' => $layup->id]);
        $this->assertDatabaseMissing('clt_layers', ['id' => $layer->id]);
    }
}
