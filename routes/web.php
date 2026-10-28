<?php

use App\Http\Controllers\Admin\InsightController as AdminInsightController;
use App\Http\Controllers\Admin\InsightPreviewController;
use App\Http\Controllers\InsightController;
use App\Http\Controllers\ProfileController;
use App\Models\Insight;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return view('home', [
        'latest_insights' => Insight::published()->latest('published_at')->limit(3)->get(),
    ]);
})->name('home');

Route::get('/insights', [InsightController::class, 'index'])->name('insights.index');
Route::get('/insights/{insight:slug}', [InsightController::class, 'show'])->name('insights.show');
Route::view('/projects', 'projects')->name('projects');
Route::view('/about', 'about')->name('about');

Route::prefix('admin')->middleware(['auth', 'can:admin'])->group(function () {
    Route::get('/', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');

    Route::post('/insights/preview', InsightPreviewController::class)->name('admin.insights.preview');
    Route::resource('insights', AdminInsightController::class)->except('show')->names('admin.insights');
});

require __DIR__.'/auth.php';
