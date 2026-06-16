<?php

test('manifest file can be retrieved', function () {
    $response = $this->get('/manifest.json');
    $response->assertStatus(200);
    $response->assertHeader('Content-Type', 'application/json');

    $manifestData = json_decode($response->getContent(), true);
    expect($manifestData['name'])->toBe('GreenRun')
        ->and($manifestData['short_name'])->toBe('GreenRun')
        ->and($manifestData['display'])->toBe('standalone');
});

test('service worker file can be retrieved', function () {
    $response = $this->get('/sw.js');
    $response->assertStatus(200);
    expect($response->getContent())->toContain('green-mile-v1');
});

test('offline fallback page works correctly', function () {
    $response = $this->get(route('offline'));
    $response->assertStatus(200)
        ->assertSee('Anda sedang offline.')
        ->assertSee('Silakan periksa koneksi internet dan coba kembali.');
});

test('welcome page contains manifest link', function () {
    $response = $this->get('/');
    // Welcome page redirects to login/dashboard if authenticated, otherwise can load directly or redirect.
    // Let's assert redirect or success status, and then inspect redirect destination or success content.
    if ($response->isRedirection()) {
        $response = $this->followRedirects($response);
    }
    $response->assertSee('<link rel="manifest" href="/manifest.json">', false);
});
