<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\BookController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DiscussionController;
use App\Http\Controllers\PerpustakaanController;
use App\Http\Controllers\LoanController;

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
    Route::get('/google-books/search', [BookController::class, 'searchGoogleBooks']);
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

// Create, Edit, Delete (Discussions & Comments)
Route::middleware('auth')->group(function () {
    Route::get('/diskusi/create', [DiscussionController::class, 'create']);
    Route::post('/diskusi', [DiscussionController::class, 'store']);
    Route::get('/diskusi/{id}/edit', [DiscussionController::class, 'edit']);
    Route::put('/diskusi/{id}', [DiscussionController::class, 'update']);
    Route::delete('/diskusi/{id}', [DiscussionController::class, 'destroy']);
    
    Route::post('/diskusi/{id}/comment', [DiscussionController::class, 'storeComment']);
    Route::put('/comments/{id}', [DiscussionController::class, 'updateComment']);
    Route::delete('/comments/{id}', [DiscussionController::class, 'destroyComment']);
});

// Detail
Route::get('/diskusi/{id}', [DiscussionController::class, 'show']);
Route::get('/perpustakaan', fn() => view('perpustakaan'));
Route::get('/informasi', fn() => view('informasi'));

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

// Pinjam
Route::middleware('auth')->group(function () {
    Route::post('/loans/{book}', [LoanController::class, 'store']);
    Route::patch('/loans/{loan}/status', [LoanController::class, 'updateStatus']);
    Route::patch('/loans/{loan}/return', [LoanController::class, 'returnBook']);
    Route::get('/loans/my', [LoanController::class, 'myLoans']);
    Route::get('/loans/incoming', [LoanController::class, 'incomingRequests']);
});