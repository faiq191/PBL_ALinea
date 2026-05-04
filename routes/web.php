<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\BookController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DiscussionController;
use App\Http\Controllers\PerpustakaanController;


//Keter
Route::get('/', function () {
    return app(BookController::class)->home();
});
Route::get('/koleksi', [BookController::class, 'index'])->middleware('auth');

//Auth-Route

Route::get('/login', fn() => view('auth.login'))->name('login');
Route::post('/login', [AuthController::class, 'login']);

Route::get('/register', fn() => view('auth.register'))->name('register');
Route::post('/register', [AuthController::class, 'register']);

Route::post('/logout', function () {
    Auth::logout();
    return redirect('/');
});

//Login-User

Route::middleware('auth')->group(function () {

    // Koleksi (Books)
    Route::get('/koleksi', [BookController::class, 'index']);
    Route::get('/books/create', [BookController::class, 'create']);
    Route::post('/books', [BookController::class, 'store']);

    // Profile
    Route::get('/profile', function () {
        return view('profile');
    });

    Route::post('/profile', function (Request $request) {

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

});

//Main  Pages

// Komunitas page
Route::get('/komunitas', [DiscussionController::class, 'index']);

// Create
Route::get('/diskusi/create', [DiscussionController::class, 'create'])->middleware('auth');
Route::post('/diskusi', [DiscussionController::class, 'store'])->middleware('auth');
Route::post('/diskusi/{id}/comment', [DiscussionController::class, 'storeComment'])->middleware('auth');

// Detail
Route::get('/diskusi/{id}', [DiscussionController::class, 'show']);
Route::get('/perpustakaan', fn() => view('perpustakaan'));
Route::get('/informasi', fn() => view('informasi'));
Route::get('/admin', fn() => view('admin'));

//Routes for books

Route::post('/books/{id}/borrow', function ($id) {
    return "Borrow book " . $id;
})->name('books.borrow');

Route::get('/books/{id}', [BookController::class, 'show']);
Route::get('/books/{id}/edit', [BookController::class, 'edit'])->middleware('auth');
Route::put('/books/{id}', [BookController::class, 'update'])->middleware('auth');
Route::delete('/books/{id}', [BookController::class, 'destroy'])->middleware('auth');

//Route Perpustakaan

Route::get('/perpustakaan', [PerpustakaanController::class, 'index']);
