<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('home');
});

Route::get('/login', function () {
    return view('login');
});

Route::get('/register', function () {
    return view('register');
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
