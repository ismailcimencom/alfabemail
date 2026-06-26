<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MailcowProxyController;

Route::get('/test', function () {
    return 'API working';
});

// Mailcow API Proxy — auth:sanctum + permission middleware
Route::prefix('mailcow')->middleware(['auth:sanctum'])->group(function () {
    Route::get('/status',              [MailcowProxyController::class, 'status']);
    Route::get('/mailboxes',           [MailcowProxyController::class, 'listMailboxes'])->middleware('permission:kota-sor');
    Route::get('/quota/{email}',       [MailcowProxyController::class, 'getQuota'])->middleware('permission:kota-sor');
    Route::post('/mailbox',            [MailcowProxyController::class, 'createMailbox'])->middleware('permission:mailbox-olustur');
    Route::delete('/mailbox/{email}',  [MailcowProxyController::class, 'deleteMailbox'])->middleware('permission:mailbox-sil');
});
