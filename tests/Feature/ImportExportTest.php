<?php

namespace Tests\Feature;

use App\Models\CltLayer;
use App\Models\CltLayup;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class ImportExportTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Supplier $supplier;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->supplier = Supplier::factory()->create(['name' => 'Export Supplier']);
    }

    public function test_export_returns_correct_json_structure(): void
    {
        $layup = CltLayup::factory()->create([
            'supplier_id' => $this->supplier->id,
            'name' => 'Layup A',
        ]);
        CltLayer::factory()->create([
            'layup_id' => $layup->id,
            'layer_order' => 1,
            'thickness' => 20.00,
            'width' => 100.00,
            'angle' => 0,
        ]);

        $response = $this->actingAs($this->user)->get(route('suppliers.export', $this->supplier));

        $response->assertStatus(200);
        $response->assertHeader('content-type', 'application/json');

        $content = $response->streamedContent();
        $data = json_decode($content, true);

        $this->assertArrayHasKey('supplier', $data);
        $this->assertEquals('Export Supplier', $data['supplier']['name']);
        $this->assertCount(1, $data['supplier']['layups']);
        $this->assertEquals('Layup A', $data['supplier']['layups'][0]['name']);
        $this->assertCount(1, $data['supplier']['layups'][0]['layers']);
        $this->assertEquals(1, $data['supplier']['layups'][0]['layers'][0]['layer_order']);
    }

    public function test_import_with_no_conflicts_creates_records(): void
    {
        $importData = [
            'supplier' => [
                'name' => $this->supplier->name,
                'layups' => [
                    [
                        'name' => 'New Layup',
                        'layers' => [
                            ['layer_order' => 1, 'thickness' => 25.00, 'width' => 120.00, 'angle' => 90],
                        ],
                    ],
                ],
            ],
        ];

        $file = UploadedFile::fake()->createWithContent(
            'import.json',
            json_encode($importData)
        );

        $response = $this->actingAs($this->user)->post(
            route('suppliers.import', $this->supplier),
            ['file' => $file, 'strategy' => 'overwrite']
        );

        $response->assertRedirect(route('suppliers.show', $this->supplier));
        $this->assertDatabaseHas('clt_layups', [
            'supplier_id' => $this->supplier->id,
            'name' => 'New Layup',
        ]);
        $this->assertDatabaseHas('clt_layers', ['layer_order' => 1, 'thickness' => 25.00]);
    }

    public function test_import_with_overwrite_strategy_updates_existing(): void
    {
        $layup = CltLayup::factory()->create([
            'supplier_id' => $this->supplier->id,
            'name' => 'Existing Layup',
        ]);
        $layer = CltLayer::factory()->create([
            'layup_id' => $layup->id,
            'layer_order' => 1,
            'thickness' => 20.00,
            'width' => 100.00,
            'angle' => 0,
        ]);

        $importData = [
            'supplier' => [
                'name' => $this->supplier->name,
                'layups' => [
                    [
                        'name' => 'Existing Layup',
                        'layers' => [
                            ['layer_order' => 1, 'thickness' => 30.00, 'width' => 150.00, 'angle' => 90],
                        ],
                    ],
                ],
            ],
        ];

        $file = UploadedFile::fake()->createWithContent(
            'import.json',
            json_encode($importData)
        );

        $response = $this->actingAs($this->user)->post(
            route('suppliers.import', $this->supplier),
            ['file' => $file, 'strategy' => 'overwrite']
        );

        $response->assertRedirect(route('suppliers.show', $this->supplier));
        $layer->refresh();
        $this->assertEquals('30.00', $layer->thickness);
        $this->assertEquals('150.00', $layer->width);
        $this->assertEquals('90.00', $layer->angle);
    }

    public function test_import_with_skip_strategy_keeps_existing(): void
    {
        $layup = CltLayup::factory()->create([
            'supplier_id' => $this->supplier->id,
            'name' => 'Existing Layup',
        ]);
        CltLayer::factory()->create([
            'layup_id' => $layup->id,
            'layer_order' => 1,
            'thickness' => 20.00,
            'width' => 100.00,
            'angle' => 0,
        ]);

        $importData = [
            'supplier' => [
                'name' => $this->supplier->name,
                'layups' => [
                    [
                        'name' => 'Existing Layup',
                        'layers' => [
                            ['layer_order' => 1, 'thickness' => 30.00, 'width' => 150.00, 'angle' => 90],
                        ],
                    ],
                ],
            ],
        ];

        $file = UploadedFile::fake()->createWithContent(
            'import.json',
            json_encode($importData)
        );

        $response = $this->actingAs($this->user)->post(
            route('suppliers.import', $this->supplier),
            ['file' => $file, 'strategy' => 'skip']
        );

        $response->assertRedirect(route('suppliers.show', $this->supplier));
        $this->assertDatabaseHas('clt_layers', [
            'layup_id' => $layup->id,
            'layer_order' => 1,
            'thickness' => 20.00,
        ]);
    }

    public function test_import_with_duplicate_strategy_creates_new_layup(): void
    {
        $layup = CltLayup::factory()->create([
            'supplier_id' => $this->supplier->id,
            'name' => 'Existing Layup',
        ]);
        CltLayer::factory()->create([
            'layup_id' => $layup->id,
            'layer_order' => 1,
            'thickness' => 20.00,
            'width' => 100.00,
            'angle' => 0,
        ]);

        $importData = [
            'supplier' => [
                'name' => $this->supplier->name,
                'layups' => [
                    [
                        'name' => 'Existing Layup',
                        'layers' => [
                            ['layer_order' => 1, 'thickness' => 30.00, 'width' => 150.00, 'angle' => 90],
                        ],
                    ],
                ],
            ],
        ];

        $file = UploadedFile::fake()->createWithContent(
            'import.json',
            json_encode($importData)
        );

        $response = $this->actingAs($this->user)->post(
            route('suppliers.import', $this->supplier),
            ['file' => $file, 'strategy' => 'duplicate']
        );

        $response->assertRedirect(route('suppliers.show', $this->supplier));
        $this->assertDatabaseHas('clt_layups', ['name' => 'Existing Layup (imported)']);
    }

    public function test_import_with_reject_strategy_aborts_on_conflicts(): void
    {
        $layup = CltLayup::factory()->create([
            'supplier_id' => $this->supplier->id,
            'name' => 'Existing Layup',
        ]);
        CltLayer::factory()->create([
            'layup_id' => $layup->id,
            'layer_order' => 1,
            'thickness' => 20.00,
            'width' => 100.00,
            'angle' => 0,
        ]);

        $importData = [
            'supplier' => [
                'name' => $this->supplier->name,
                'layups' => [
                    [
                        'name' => 'Existing Layup',
                        'layers' => [
                            ['layer_order' => 1, 'thickness' => 30.00, 'width' => 150.00, 'angle' => 90],
                        ],
                    ],
                ],
            ],
        ];

        $file = UploadedFile::fake()->createWithContent(
            'import.json',
            json_encode($importData)
        );

        $response = $this->actingAs($this->user)->post(
            route('suppliers.import', $this->supplier),
            ['file' => $file, 'strategy' => 'reject']
        );

        $response->assertSessionHasErrors('import');
        // Existing data should remain unchanged
        $this->assertDatabaseHas('clt_layers', ['thickness' => 20.00]);
    }

    public function test_import_form_page_is_displayed(): void
    {
        $response = $this->actingAs($this->user)->get(route('suppliers.import.form', $this->supplier));
        $response->assertStatus(200);
        $response->assertSee('Import Data');
    }

    public function test_import_rejects_invalid_json(): void
    {
        $file = UploadedFile::fake()->createWithContent('bad.json', 'not json');

        $response = $this->actingAs($this->user)->post(
            route('suppliers.import', $this->supplier),
            ['file' => $file, 'strategy' => 'overwrite']
        );

        $response->assertSessionHasErrors('file');
    }
}
