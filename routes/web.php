<?php

use App\Http\Controllers\VoteController;

Route::get('/', [VoteController::class, 'display'])->name('vote.display');
Route::get('/admin-sinode-xyz', [VoteController::class, 'admin'])->name('vote.admin');

// Group Manajemen CRUD
Route::get('/admin-sinode-xyz/manage', [VoteController::class, 'manage'])->name('vote.manage');
Route::post('/admin-sinode-xyz/manage/candidate', [VoteController::class, 'storeCandidate'])->name('candidate.store');
Route::post('/admin-sinode-xyz/manage/position', [VoteController::class, 'storePosition'])->name('position.store');
Route::delete('/admin-sinode-xyz/manage/position/{id}', [VoteController::class, 'destroyPosition'])->name('position.destroy');
// Route untuk menghapus kandidat secara spesifik berdasarkan ID
Route::delete('/admin-sinode-xyz/manage/candidate/{id}', [VoteController::class, 'destroyCandidate'])->name('candidate.destroy');
Route::get('/admin-sinode-xyz/manage/position', function () {
    return redirect()->route('vote.manage');
});
Route::get('/admin-sinode-xyz/manage/candidate', function () {
    return redirect()->route('vote.manage');
});