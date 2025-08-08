<?php

use App\Http\Controllers\AuthController;
use Dom\Comment;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PhoneController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\TagController;

Route::get('/', function () {
    return view('welcome');
});

// Route::get('url',function () {
//     return 'isi response';
// })

Route::prefix('admin')->middleware('auth')->group(function () {
Route::get('/blogs', [BlogController::class,'index'])->name('blogs.index');
Route::get('/blogs/create', [BlogController::class, 'create'])->name('blog.create');
Route::post('/blogs/store', [BlogController::class, 'store'])->name('blog.store');
Route::get('/blogs/{id}/detail', [BlogController::class, 'show'])->name('blog.show');
Route::get('blogs/{id}/edit', [BlogController::class, 'edit'])->name('blog.edit');
Route::patch('blogs/{id}/update', [BlogController::class, 'update'])->name('blog.update');
Route::delete('/blogs/{id}/delete', [BlogController::class, 'delete'])->name('blog.delete');
Route::get('blogs/trash', [BlogController::class, 'trash'])->name('blog.trash');
Route::get('/blogs/{id}/restore', [BlogController::class, 'restore'])->name('blog.restore');

Route::middleware('admin')->group(function() {
    Route::get('/phones', [PhoneController::class, 'index'])->name('phone.index');
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::post('/comment/{id}', [CommentController::class, 'store'])->name('comment.store');
    Route::get('/comment', [CommentController::class, 'index'])->name('comment.index');
    Route::delete('/comment/{id}', [CommentController::class, 'destroy'])->name('comment.destroy');

    Route::get('/tags', [TagController::class, 'index'])->name('tags.index');
});




Route::get('/logout', [AuthController::class, 'logout'])->name('logout');
});

Route::middleware('guest')->group(function() {
    Route::get('/login', [AuthController::class, 'login'])->name('login');
    Route::post('/login', [AuthController::class, 'authenticate'])->name('authenticate');
});



Route::get('/blogs', [BlogController::class, 'homepage'])->name('blogs.homepage');
Route::get('/blogs/{id}', [BlogController::class, 'detail'])->name('blog.detail');



// Route::get('/artikel', function() {
//     return 'ini adalah halaman artikel';
// });

// Route::get('/blog', function() {
//     return view('blog');
// });

// Route::get('/blog', function() {
//     return view('blog', ['data' => 'Blog 1', 'title' => 'Belajar Laravel 11']);
// });

// Route::get('/blog', function() {
//     $data = 'Blog 1';
//     $title = 'Belajar Laravel 11';
//     return view('blog', ['data' => $data, 'title' => $title]);
// });

// Route::get('/hitung', function() {
//     $a = 4;
//     $b = 6;
//     return 'Hasil : ' . ($a + $b);
// });

// route::view('/tentang', 'about');
// route::view('/blog', 'blog',['data' => 'Blog 1', 'title' => 'Belajar Laravel 11']);

// Route::get('/produk/1', function() {
//     return 'Ini halaman informasi detail produk id 1';
// });

// Route::get('/produk/{id}', function($id) {
//     return 'Ini halaman informasi detail produk id : ' . $id;
// });

// Route::get('/user/{nama?}', function($nama = 'Tamu') {
//     return "Halo, Selamat Datang, $nama !";
// });

// Route::get('/profile', function () {
//     return 'Ini adalah halaman profile';
// })->name('prf');

// Route::get('/ke-profile', function () {
//     return redirect()->route('prf');
// });

// Route::redirect('/beranda', '/hitung');

// route::get('/blog', [BlogController::class, 'index']);

// route::prefix('admin')->group(function() {
//     route::get('/dashboard', function() {
//         return 'Admin Dashboard';
//     })->name('admin.dashboard');
//     route::get('/profile', function() {
//         return 'Ini Halaman Profile';
//     })->name('admin.profile');
// });

// Route::resource('/posts', PostController::class);