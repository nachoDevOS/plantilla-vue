<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Public\ContactController;
use Illuminate\Support\Facades\Route;

// Sitio publico

// Route::inertia muestra directamente una pagina Vue desde resources/js/pages.
// '/' carga resources/js/pages/Welcome.vue.
Route::inertia('/', 'public/Welcome')->name('home');

// '/about' carga resources/js/pages/public/About.vue.
Route::inertia('/about', 'public/About')->name('about');

// '/contact' carga resources/js/pages/public/Contact.vue.
Route::get('/contact', fn () => inertia('public/Contact'))->name('contact');

// El POST del formulario publico va al controlador Laravel.
Route::post('/contact', [ContactController::class, 'store'])->name('contact.store');

// Panel de administracion

Route::middleware(['auth', 'verified'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('users', [UserController::class, 'index'])->name('users');
    });

// Redirige /dashboard a /admin/dashboard por compatibilidad con Fortify.
Route::redirect('/dashboard', '/admin/dashboard');

// Configuracion de usuario
require __DIR__.'/settings.php';
