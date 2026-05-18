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

Route::middleware(['auth', 'verified', 'role:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('users', [UserController::class, 'index'])->name('users');
        Route::get('roles', [\App\Http\Controllers\Admin\RoleController::class, 'index'])->name('roles');
        Route::get('roles/create', [\App\Http\Controllers\Admin\RoleController::class, 'create'])->name('roles.create');
        Route::post('roles', [\App\Http\Controllers\Admin\RoleController::class, 'store'])->name('roles.store');
        Route::get('roles/{role}/edit', [\App\Http\Controllers\Admin\RoleController::class, 'edit'])->name('roles.edit');
        Route::put('roles/{role}', [\App\Http\Controllers\Admin\RoleController::class, 'update'])->name('roles.update');
        Route::delete('roles/{role}', [\App\Http\Controllers\Admin\RoleController::class, 'destroy'])->name('roles.destroy');
    });

// Redirige /dashboard a /admin/dashboard por compatibilidad con enlaces antiguos.
Route::redirect('/dashboard', '/admin/dashboard');

// Configuracion de usuario
require __DIR__.'/settings.php';
