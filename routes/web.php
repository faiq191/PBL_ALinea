<?php

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\BookController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DiscussionController;
use App\Http\Controllers\PerpustakaanController;
use App\Http\Controllers\LoanController;
use App\Http\Controllers\NotificationController;
use Illuminate\Support\Facades\Password;

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

Route::get('/forgot-password', function () {
    return view('auth.forgot-password');
})->middleware('guest')->name('password.request');
Route::post('/forgot-password', function (Request $request) {
    $request->validate(['email' => 'required|email']);
 
    $status = Password::sendResetLink(
        $request->only('email')
    );
 
    return $status === Password::ResetLinkSent
        ? back()->with(['status' => __($status)])
        : back()->withErrors(['email' => __($status)]);
})->middleware('guest')->name('password.email');
Route::get('/reset-password/{token}', function (string $token) {
    return view('auth.reset-password', ['token' => $token]);
})->middleware('guest')->name('password.reset');


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
            'profile_photo' => 'nullable|image|mimes:jpg,png,jpeg|max:2048',
            'profile_photo_url' => 'nullable|url'
        ]);

        $user = auth()->user();

        $data = [
            'name' => $request->name,
            'email' => $request->email,
        ];

        if ($request->hasFile('profile_photo')) {

            if ($user->profile_photo && !Str::startsWith($user->profile_photo, 'http') && file_exists(public_path('storage/' . $user->profile_photo))) {
                unlink(public_path('storage/' . $user->profile_photo));
            }

            $path = $request->file('profile_photo')->store('profiles', 'public');
            $data['profile_photo'] = $path;
        } elseif ($request->profile_photo_url) {

            if ($user->profile_photo && !Str::startsWith($user->profile_photo, 'http') && file_exists(public_path('storage/' . $user->profile_photo))) {
                unlink(public_path('storage/' . $user->profile_photo));
            }

            $data['profile_photo'] = $request->profile_photo_url;
        }

        $user->update($data);

        return back();
    });

    // Chat Routes
    Route::get('/chat/users', [\App\Http\Controllers\ChatController::class, 'getUsers']);
    Route::get('/chat/messages/{userId}', [\App\Http\Controllers\ChatController::class, 'getMessages']);
    Route::post('/chat/send', [\App\Http\Controllers\ChatController::class, 'sendMessage']);
    Route::post('/chat/read/{userId}', [\App\Http\Controllers\ChatController::class, 'markAsRead']);

});

//Main  Pages

// Komunitas page
Route::get('/komunitas', [DiscussionController::class, 'index']);
Route::get('/diskusi', function () {
    return response()->view('errors.405', [], 405);
});

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
Route::get('/users/{id}', [\App\Http\Controllers\UserController::class, 'show'])->name('users.show');
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
    Route::post('/loans/{loan}/remind', [LoanController::class, 'remindUser']);
    Route::post('/loans/{loan}/confirm-return', [LoanController::class, 'confirmReturn']);
    Route::post('/loans/{loan}/reject-return', [LoanController::class, 'rejectReturn']);
    Route::get('/loans/my', [LoanController::class, 'myLoans']);
    Route::get('/loans/incoming', [LoanController::class, 'incomingRequests']);

    // Notifications
    Route::get('/notifications', [NotificationController::class, 'index']);
    Route::post('/notifications/{id}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllAsRead'])->name('notifications.readAll');
    Route::delete('/notifications/{id}', [NotificationController::class, 'destroy'])->name('notifications.destroy');

    // Temporary route to generate highly-accurate Vol 1 dummy books for testing
    Route::get('/generate-dummy-books', function (Request $request) {
        $user = auth()->user();
        
        // SECURITY CHECK: Hanya user dengan email berakhiran @admin.com yang boleh mengakses!
        if (!$user || !Str::endsWith($user->email, '@admin.com')) {
            abort(403, 'Akses ditolak. Fitur pembuat buku dummy ini hanya dapat diakses oleh akun Administrator (@admin.com).');
        }
        
        $userId = $user->id;
        
        // HANYA WIPE & RESET JIKA DIPAKSA DENGAN ?force=true
        if ($request->query('force') === 'true') {
            \Illuminate\Support\Facades\Schema::disableForeignKeyConstraints();
            \App\Models\Book::truncate();
            \Illuminate\Support\Facades\DB::table('book_genre')->truncate();
            \Illuminate\Support\Facades\Schema::enableForeignKeyConstraints();
        }
        
        // Ensure metadata types exist
        $novelType = \App\Models\Type::firstOrCreate(['name' => 'Novel']);
        $mangaType = \App\Models\Type::firstOrCreate(['name' => 'Manga']);
        $komikType = \App\Models\Type::firstOrCreate(['name' => 'Komik']);
        $bookType = \App\Models\Type::firstOrCreate(['name' => 'Buku Pelajaran']);
        
        // Ensure demographics exist
        $shounenDemo = \App\Models\Demographic::firstOrCreate(['name' => 'Shounen']);
        $seinenDemo = \App\Models\Demographic::firstOrCreate(['name' => 'Seinen']);
        $remajaDemo = \App\Models\Demographic::firstOrCreate(['name' => 'Remaja']);
        $dewasaDemo = \App\Models\Demographic::firstOrCreate(['name' => 'Dewasa']);
        $semuaDemo = \App\Models\Demographic::firstOrCreate(['name' => 'Semua Umur']);
        
        // Ensure genres exist
        $fantasiGenre = \App\Models\Genre::firstOrCreate(['name' => 'Fantasi']);
        $petualanganGenre = \App\Models\Genre::firstOrCreate(['name' => 'Petualangan']);
        $misteriGenre = \App\Models\Genre::firstOrCreate(['name' => 'Misteri']);
        $psikologiGenre = \App\Models\Genre::firstOrCreate(['name' => 'Psikologi']);
        $selfImpGenre = \App\Models\Genre::firstOrCreate(['name' => 'Self-Improvement']);
        $fiksiIlmiahGenre = \App\Models\Genre::firstOrCreate(['name' => 'Fiksi Ilmiah']);

        $templates = [
            [
                'title' => 'Harry Potter and the Sorcerer\'s Stone Vol. 1',
                'author' => 'J.K. Rowling',
                'description' => 'Harry Potter has no idea how famous he is. That\'s because he\'s being raised by his miserable aunt and uncle who are terrified of Harry\'s magical powers.',
                'image_url' => 'https://upload.wikimedia.org/wikipedia/en/6/6b/Harry_Potter_and_the_Philosopher%27s_Stone_Book_Cover.jpg',
                'type' => $novelType,
                'demographic' => $remajaDemo,
                'genres' => [$fantasiGenre, $petualanganGenre],
                'year' => 1997
            ],
            [
                'title' => 'One Piece Vol. 1',
                'author' => 'Eiichiro Oda',
                'description' => 'Join Monkey D. Luffy and his swashbuckling crew in their search for the ultimate treasure, the One Piece.',
                'image_url' => 'https://m.media-amazon.com/images/I/91NxYvUNf6L._AC_UF1000,1000_QL80_.jpg',
                'type' => $mangaType,
                'demographic' => $shounenDemo,
                'genres' => [$petualanganGenre, $fantasiGenre],
                'year' => 1997
            ],
            [
                'title' => 'Jujutsu Kaisen Vol. 1',
                'author' => 'Gege Akutami',
                'description' => 'Although Yuji Itadori looks like your average teenager, his immense physical strength is something to behold! Every sports club wants him to join.',
                'image_url' => 'https://cdn.gramedia.com/uploads/items/9786230022180_Jujutsukaisen_1.jpg',
                'type' => $mangaType,
                'demographic' => $shounenDemo,
                'genres' => [$fantasiGenre, $psikologiGenre],
                'year' => 2018
            ],
            [
                'title' => 'Bakemonogatari Vol. 1',
                'author' => 'NISIOISIN',
                'description' => 'There\'s a girl in my class named Senjougahara. She\'s beautiful, but she\'s also... weightless.',
                'image_url' => 'https://cdn.gramedia.com/uploads/items/9786230015526_cover_bakemonogatari_01.jpg',
                'type' => $novelType,
                'demographic' => $seinenDemo,
                'genres' => [$misteriGenre, $psikologiGenre],
                'year' => 2006
            ],
            [
                'title' => 'Kizumonogatari',
                'author' => 'NISIOISIN',
                'description' => 'Around midnight under a lonely street lamp in a provincial town, lies a white woman, a blonde, alone, robbed of all four limbs, yet undead.',
                'image_url' => 'https://static.wikia.nocookie.net/bakemonogatari1645/images/2/2f/Kizumonogatari_Cover_%28English%29.jpg',
                'type' => $novelType,
                'demographic' => $seinenDemo,
                'genres' => [$misteriGenre, $psikologiGenre],
                'year' => 2008
            ],
            [
                'title' => 'Nisemonogatari Vol. 1',
                'author' => 'NISIOISIN',
                'description' => 'Fake tales, fake monsters, fake feelings. The thrilling continuation of the Monogatari series.',
                'image_url' => 'https://m.media-amazon.com/images/I/81g0MA1YcRL._UF1000,1000_QL80_.jpg',
                'type' => $novelType,
                'demographic' => $seinenDemo,
                'genres' => [$misteriGenre, $psikologiGenre],
                'year' => 2008
            ],
            [
                'title' => 'Chainsaw Man Vol. 1',
                'author' => 'Tatsuki Fujimoto',
                'description' => 'Denji was a small-time devil hunter just trying to survive in a harsh world. After being killed on a job, he is revived by his pet devil-dog Pochita.',
                'image_url' => 'https://cdn.gramedia.com/uploads/picture_meta/2023/1/27/9pqwznsjkqernyd6xasdjq.jpg',
                'type' => $mangaType,
                'demographic' => $shounenDemo,
                'genres' => [$fantasiGenre, $psikologiGenre],
                'year' => 2018
            ],
            [
                'title' => 'Attack on Titan Vol. 1',
                'author' => 'Hajime Isayama',
                'description' => 'In this post-apocalyptic sci-fi story, humanity has been devastated by the bizarre, giant humanoids known as the Titans.',
                'image_url' => 'https://m.media-amazon.com/images/S/compressed.photo.goodreads.com/books/1769709110i/13154150.jpg',
                'type' => $mangaType,
                'demographic' => $seinenDemo,
                'genres' => [$petualanganGenre, $psikologiGenre],
                'year' => 2009
            ],
            [
                'title' => 'Demon Slayer: Kimetsu no Yaiba Vol. 1',
                'author' => 'Koyoharu Gotouge',
                'description' => 'In Taisho-era Japan, Tanjiro Kamado is a kindhearted boy who makes a living selling charcoal. But his peaceful life is shattered when a demon slaughters his family.',
                'image_url' => 'https://cdn.gramedia.com/uploads/items/9786230017193_cover_Demon_Slayer_01.jpg',
                'type' => $mangaType,
                'demographic' => $shounenDemo,
                'genres' => [$fantasiGenre, $petualanganGenre],
                'year' => 2016
            ],
            [
                'title' => 'Spy x Family Vol. 1',
                'author' => 'Tatsuya Endo',
                'description' => 'Master spy Twilight is the best at what he does when it comes to going undercover on dangerous missions in the name of a better world.',
                'image_url' => 'https://cdn.gramedia.com/uploads/items/9786230021312_Spy_x_Family_01.jpg',
                'type' => $mangaType,
                'demographic' => $shounenDemo,
                'genres' => [$petualanganGenre, $misteriGenre],
                'year' => 2019
            ],
            [
                'title' => 'Solo Leveling Vol. 1',
                'author' => 'Chugong',
                'description' => 'In a world where hunters must battle deadly monsters to protect mankind, Sung Jinwoo—the weakest hunter of all mankind—finds himself in a struggle for survival.',
                'image_url' => 'https://image.gramedia.net/rs:fit:0:0/plain/https://cdn.gramedia.com/uploads/picture_meta/2023/5/28/nqza8hk7weshv37aqst65q.jpg',
                'type' => $novelType,
                'demographic' => $semuaDemo,
                'genres' => [$petualanganGenre, $fantasiGenre],
                'year' => 2016
            ],
            [
                'title' => 'Atomic Habits',
                'author' => 'James Clear',
                'description' => 'No matter your goals, Atomic Habits offers a proven framework for improving—every day. James Clear, one of the world\'s leading experts on habit formation, reveals practical strategies.',
                'image_url' => 'https://cdn.gramedia.com/uploads/items/9786020633176_.Atomic_Habit.jpg',
                'type' => $bookType,
                'demographic' => $dewasaDemo,
                'genres' => [$selfImpGenre],
                'year' => 2018
            ],
            [
                'title' => 'The Hobbit',
                'author' => 'J.R.R. Tolkien',
                'description' => 'Bilbo Baggins is a hobbit who enjoys a comfortable, unambitious life, rarely traveling any farther than his pantry or cellar.',
                'image_url' => 'https://upload.wikimedia.org/wikipedia/id/6/6e/ECEI5544big.jpg',
                'type' => $novelType,
                'demographic' => $semuaDemo,
                'genres' => [$fantasiGenre, $petualanganGenre],
                'year' => 1937
            ],
            [
                'title' => 'The Lord of the Rings Vol. 1',
                'author' => 'J.R.R. Tolkien',
                'description' => 'The epic high-fantasy masterpiece where the Dark Lord Sauron seeks the One Ring to rule them all.',
                'image_url' => 'https://m.media-amazon.com/images/I/81nV6x2ey4L._AC_UF1000,1000_QL80_.jpg',
                'type' => $novelType,
                'demographic' => $dewasaDemo,
                'genres' => [$fantasiGenre, $petualanganGenre],
                'year' => 1954
            ],
            [
                'title' => 'Sapiens: A Brief History of Humankind',
                'author' => 'Yuval Noah Harari',
                'description' => '100,000 years ago, at least six human species inhabited the earth. Today there is just one. Us. Homo sapiens. How did our species succeed in the battle for dominance?',
                'image_url' => 'https://cdn.gramedia.com/uploads/items/591701404_sapiens.jpg',
                'type' => $bookType,
                'demographic' => $dewasaDemo,
                'genres' => [$selfImpGenre],
                'year' => 2011
            ],
            [
                'title' => 'Dr. STONE Vol. 1',
                'author' => 'Riichiro Inagaki',
                'description' => 'One fateful day, all of humanity was petrified by a blinding flash of light. After several millennia, high schooler Taiju awakens and finds himself lost in a world of statues. However, he\'s not alone! His science-loving friend Senku\'s been active for a few months and he\'s got a grand plan in mind—to kickstart civilization with the power of science!',
                'image_url' => 'https://cdn.gramedia.com/uploads/items/Dr_Stone_1_1.jpg',
                'type' => $mangaType,
                'demographic' => $shounenDemo,
                'genres' => [$fiksiIlmiahGenre, $petualanganGenre, $fantasiGenre],
                'year' => 2017
            ],
            [
                'title' => 'Naruto Vol. 1',
                'author' => 'Masashi Kishimoto',
                'description' => 'Naruto Uzumaki is a young ninja who seeks recognition from his peers and dreams of becoming the Hokage, the leader of his village.',
                'image_url' => 'https://upload.wikimedia.org/wikipedia/id/9/94/NarutoCoverTankobon1.jpg',
                'type' => $mangaType,
                'demographic' => $shounenDemo,
                'genres' => [$petualanganGenre, $fantasiGenre],
                'year' => 1999
            ],
            [
                'title' => 'Death Note Vol. 1',
                'author' => 'Tsugumi Ohba',
                'description' => 'A high school student discovers a supernatural notebook that grants him the ability to kill anyone whose name and face he knows.',
                'image_url' => 'https://upload.wikimedia.org/wikipedia/id/6/6f/Death_Note_Vol_1.jpg',
                'type' => $mangaType,
                'demographic' => $shounenDemo,
                'genres' => [$misteriGenre, $psikologiGenre],
                'year' => 2003
            ],
            [
                'title' => 'Tokyo Ghoul Vol. 1',
                'author' => 'Sui Ishida',
                'description' => 'Ghouls live among us, the same as normal people in every way—except their craving for human flesh.',
                'image_url' => 'https://m.media-amazon.com/images/I/81gv-D-LqhL._AC_UF1000,1000_QL80_.jpg',
                'type' => $mangaType,
                'demographic' => $seinenDemo,
                'genres' => [$psikologiGenre, $misteriGenre],
                'year' => 2011
            ]
        ];
        
        $booksCreated = 0;
        
        foreach ($templates as $tmpl) {
            // Get or create correct publication year record
            $yearRecord = \App\Models\Year::firstOrCreate(['year' => $tmpl['year']]);
            
            // Pencocokan cerdas: hanya buat jika judul belum ada
            $book = \App\Models\Book::firstOrCreate(
                ['title' => $tmpl['title']],
                [
                    'user_id' => $userId,
                    'type_id' => $tmpl['type']->id,
                    'demographic_id' => $tmpl['demographic']->id,
                    'year_id' => $yearRecord->id,
                    'author' => $tmpl['author'],
                    'description' => $tmpl['description'],
                    'image' => $tmpl['image_url'],
                ]
            );
            
            // Hanya kaitkan genre jika buku baru saja dibuat
            if ($book->wasRecentlyCreated) {
                $genreIds = collect($tmpl['genres'])->pluck('id')->toArray();
                $book->genres()->attach($genreIds);
                $booksCreated++;
            }
        }
        
        return "Sukses! Berhasil men-generate {$booksCreated} buku dummy berkualitas tinggi ke database tanpa menimpa data user lain.";
    });
});