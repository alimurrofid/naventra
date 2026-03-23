<?php

use App\Models\User;
use Illuminate\Support\Facades\DB;
use function Pest\Laravel\actingAs;
use function Pest\Laravel\postJson;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\assertDatabaseCount;
use function Pest\Laravel\assertDatabaseMissing;

function createExampleTestUser() {
    $user = User::factory()->create();
    if (method_exists($user, 'givePermissionTo')) {
        $user->givePermissionTo('setup.master.example.create'); 
    }
    return $user;
}

it('successfully creates header with multiple bulk details', function () {
    $payload = [
        'code' => 'TEST-REQ-001',
        'description' => 'Test Pest Inserting Header-Detail',
        'transaction_date' => '2026-03-24',
        'details' => [
            ['item_name' => 'Produk A', 'qty' => 5, 'price' => 25000],
            ['item_name' => 'Produk B', 'qty' => 10, 'price' => 15000],
        ],
    ];

    actingAs(createExampleTestUser())
        ->postJson('/api/setup/master/examples', $payload)
        ->assertStatus(201)
        ->assertJsonPath('data.code', 'TEST-REQ-001');

    assertDatabaseHas('examples', ['code' => 'TEST-REQ-001']);
    assertDatabaseCount('example_details', 2);
});

it('rejects validation when detail array is empty', function () {
    $payload = [
        'code' => 'TEST-REQ-002',
        'description' => 'Validation test without detail',
        'transaction_date' => '2026-03-24',
        'details' => [], // <-- Empty detail
    ];

    actingAs(createExampleTestUser())
        ->postJson('/api/setup/master/examples', $payload)
        ->assertStatus(422)
        ->assertJsonValidationErrors(['details']);
});

it('triggers rollback transaction if detail bulk insertion fails', function () {
    // Memaksa query facade DB gagal secara simulasi ketika bulk insert (di transaction level DB)
    DB::shouldReceive('transaction')
        ->once()
        ->andThrow(new \Exception('Mocked DB Crash Simulation'));

    $payload = [
        'code' => 'TEST-REQ-FAIL',
        'description' => 'Failure test',
        'transaction_date' => '2026-03-24',
        'details' => [
            ['item_name' => 'Valid Item', 'qty' => 1, 'price' => 50000],
        ],
    ];

    try {
        actingAs(createExampleTestUser())->postJson('/api/setup/master/examples', $payload);
    } catch (\Exception $e) {
        expect($e->getMessage())->toBe('Mocked DB Crash Simulation');
    }

    // Header harus ter-rollback dan pastikan tidak terinsert
    assertDatabaseMissing('examples', ['code' => 'TEST-REQ-FAIL']);
});
