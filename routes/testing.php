<?php

use Illuminate\Support\Facades\Route;

Route::post('/__testing/setup-replicado', function (\Illuminate\Http\Request $request) {
    /** @var \Tests\Fakes\FakeReplicadoService $fake */
    $fake = app(\App\Services\ReplicadoService::class);

    foreach ($request->input('pessoas', []) as $codpes => $data) {
        $fake->setPessoa((int) $codpes, $data);
    }
    foreach ($request->input('vinculos', []) as $key => $vinculos) {
        [$codpes, $codund] = explode('_', $key);
        $fake->setVinculos((int) $codpes, (int) $codund, $vinculos);
    }

    return response()->json(['ok' => true]);
});
