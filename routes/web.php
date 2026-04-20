<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BookController;
use Illuminate\Http\Request;
use App\Http\Controllers\AuthController;


Route::get('/', function () {
    return view('home');
});

//user-login
Route::get('/login', fn() => view('auth.login'));
Route::post('/login', [AuthController::class, 'login']);

Route::get('/register', fn() => view('auth.register'));
Route::post('/register', [AuthController::class, 'register']);

Route::post('/logout', function () {
    Auth::logout();
    return redirect('/');
});


//Homepage
Route::get('/koleksi', function () {
    return view('koleksi');
});

Route::get('/perpustakaan', function () {
    return view('perpustakaan');
});

Route::get('/komunitas', function () {
    return view('komunitas');
});

Route::get('/informasi', function () {
    return view('informasi');
});

Route::get('/admin', function () {
    return view('admin');
});

//Components
//Profile
Route::get('/profile', function () {
    return view('profile');
});

Route::post('/profile', function (Illuminate\Http\Request $request) {

    $request->validate([
        'name' => 'required',
        'email' => 'required|email',
        'profile_photo' => 'nullable|image|mimes:jpg,png,jpeg|max:2048'
    ]);

    $user = auth()->user();

    $data = [
        'name' => $request->name,
        'email' => $request->email,
    ];

    if ($request->hasFile('profile_photo')) {

        if ($user->profile_photo && file_exists(public_path('storage/' . $user->profile_photo))) {
            unlink(public_path('storage/' . $user->profile_photo));
        }

        $path = $request->file('profile_photo')->store('profiles', 'public');
        $data['profile_photo'] = $path;
    }

    $user->update($data);

    return back();
});

//Book Route
Route::get('/koleksi', [BookController::class, 'index']);
Route::get('/books/create', [BookController::class, 'create']);
Route::post('/books', [BookController::class, 'store']);

Route::post('/books/{id}/borrow', function ($id) {
    return "Borrow book " . $id;
})->name('books.borrow');

Route::get('/books/{id}', function ($id) {
    return "Detail book " . $id;
})->name('books.show');
