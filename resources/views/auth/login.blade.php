@extends('layout.app')

@section('title', 'Login')

@section('content')
<div class="flex justify-center items-center min-h-screen bg-gray-100">
  <div class="bg-white rounded-2xl shadow-lg p-8 w-full max-w-md">
    <div class="text-center mb-6">
      <h3 class="text-2xl font-bold text-green-700 flex justify-center items-center gap-2">
        <i class="fas fa-bicycle"></i> CampusCycle
      </h3>
      <p class="text-gray-500">Masuk ke akun Anda</p>
    </div>

    @if(session('error'))
      <div class="bg-red-100 text-red-700 p-3 rounded mb-4">
        <i class="fas fa-exclamation-circle mr-1"></i> {{ session('error') }}
      </div>
    @endif

    <form method="POST" action="{{ route('login') }}" class="space-y-4">
      @csrf
      <div>
        <label class="font-semibold block mb-1">Email</label>
        <input type="email" name="email" required class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-green-600" placeholder="nama@mail.unej.ac.id">
      </div>
      <div>
        <label class="font-semibold block mb-1">Password</label>
        <input type="password" name="password" required class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-green-600">
      </div>
      <button class="w-full bg-green-700 hover:bg-green-800 text-white font-semibold py-2 rounded-lg transition">
        <i class="fas fa-sign-in-alt mr-2"></i> Masuk
      </button>
    </form>

    <p class="text-center text-gray-600 mt-4">
      Belum punya akun?
      <a href="/register" class="text-green-700 font-semibold hover:underline">Daftar di sini</a>
    </p>
  </div>
</div>
@endsection
