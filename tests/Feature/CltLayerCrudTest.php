<?php

namespace Tests\Feature;

use App\Models\CltLayer;
use App\Models\CltLayup;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CltLayerCrudTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Supplier $supplier;
    protected CltLayup $layup;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->supplier = Supplier::factory()->create();
        $this->layup = CltLayup::factory()->create(['supplier_id' => $this->supplier->id]);
    }

    public function test_layer_index_page_is_displayed(): void
    {
        $response = $this->actingAs($this->user)->get(
            route('suppliers.layups.layers.index', [$this->supplier, $this->layup])
        );
        $response->assertStatus(200);
    }

    public function test_layer_can_be_created(): void
    {
        $response = $this->actingAs($this->user)->post(
            route('suppliers.layups.layers.store', [$this->supplier, $this->layup]),
            [
                'layer_order' => 1,
                'thickness' => 20.50,
                'width' => 100.00,
                'angle' => 90,
            ]
        );

        $response->assertRedirect(
            route('suppliers.layups.layers.index', [$this->supplier, $this->layup])
        );
        $this->assertDatabaseHas('clt_layers', [
            'layup_id' => $this->layup->id,
            'layer_order' => 1,
            'thickness' => 20.50,
            'width' => 100.00,
            'angle' => 90,
        ]);
    }

    public function test_layer_creation_validates_required_fields(): void
    {
        $response = $this->actingAs($this->user)->post(
            route('suppliers.layups.layers.store', [$this->supplier, $this->layup]),
            []
        );

        $response->assertSessionHasErrors(['layer_order', 'thickness', 'width', 'angle']);
    }

    public function test_layer_creation_validates_numeric_fields(): void
    {
        $response = $this->actingAs($this->user)->post(
            route('suppliers.layups.layers.store', [$this->supplier, $this->layup]),
            [
                'layer_order' => 'abc',
                'thickness' => 'xyz',
                'width' => 'not-a-number',
                'angle' => 'invalid',
            ]
        );

        $response->assertSessionHasErrors(['layer_order', 'thickness', 'width', 'angle']);
    }

    public function test_layer_can_be_updated(): void
    {
        $layer = CltLayer::factory()->create([
            'layup_id' => $this->layup->id,
            'layer_order' => 1,
            'thickness' => 20.00,
            'width' => 100.00,
            'angle' => 0,
        ]);

        $response = $this->actingAs($this->user)->put(
            route('suppliers.layups.layers.update', [$this->supplier, $this->layup, $layer]),
            [
                'layer_order' => 2,
                'thickness' => 25.50,
                'width' => 150.00,
                'angle' => 90,
            ]
        );

        $response->assertRedirect(
            route('suppliers.layups.layers.index', [$this->supplier, $this->layup])
        );
        $this->assertDatabaseHas('clt_layers', [
            'id' => $layer->id,
            'layer_order' => 2,
            'thickness' => 25.50,
            'width' => 150.00,
            'angle' => 90,
        ]);
    }

    public function test_layer_can_be_deleted(): void
    {
        $layer = CltLayer::factory()->create(['layup_id' => $this->layup->id]);

        $response = $this->actingAs($this->user)->delete(
            route('suppliers.layups.layers.destroy', [$this->supplier, $this->layup, $layer])
        );

        $response->assertRedirect(
            route('suppliers.layups.layers.index', [$this->supplier, $this->layup])
        );
        $this->assertDatabaseMissing('clt_layers', ['id' => $layer->id]);
    }
}
