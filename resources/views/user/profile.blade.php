@extends('layout.app')

@section('title', 'Edit Profil')

@section('content')
<div class="container mx-auto px-4 py-10">

    <div class="max-w-4xl mx-auto mb-6">
        <a href="{{ route('user.dashboard') }}" class="inline-flex items-center text-sm font-medium text-gray-500 hover:text-[var(--primary)] transition-colors group">
            <div class="w-8 h-8 rounded-full bg-white shadow-sm flex items-center justify-center mr-2 group-hover:bg-green-50 transition-colors">
                <i class="fa-solid fa-arrow-left text-xs"></i>
            </div>
            Kembali ke Dashboard
        </a>
    </div>

    <div class="max-w-4xl mx-auto bg-white rounded-3xl shadow-xl border border-gray-100">

        <form action="{{ route('user.profile.update') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="relative h-40 md:h-48">

                <div class="absolute inset-0 bg-gradient-to-br from-[var(--primary)] to-green-700 rounded-t-3xl overflow-hidden">
                    <svg class="absolute bottom-0 left-0 w-full h-auto text-white opacity-20" viewBox="0 0 1440 320" preserveAspectRatio="none">
                        <path fill="currentColor" fill-opacity="1" d="M0,160L48,176C96,192,192,224,288,224C384,224,480,192,576,181.3C672,171,768,181,864,197.3C960,213,1056,235,1152,229.3C1248,224,1344,192,1392,176L1440,160L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z"></path>
                    </svg>
                </div>

                <div class="absolute -bottom-12 left-8 md:left-10 z-20">
                    <div class="w-28 h-28 md:w-32 md:h-32 rounded-full border-[6px] border-white bg-white shadow-md flex items-center justify-center relative group">

                        <div class="w-full h-full rounded-full overflow-hidden bg-gray-100 flex items-center justify-center text-4xl font-bold text-[var(--primary)] relative">
                            @if($user->profile_picture)
                                <img src="{{ asset('storage/' . $user->profile_picture) }}"
                                     alt="Foto Profil"
                                     class="w-full h-full object-cover"
                                     id="avatar-preview">
                            @else
                                <span id="avatar-initial">{{ substr($user->name, 0, 1) }}</span>
                                <img src="" id="avatar-preview" class="w-full h-full object-cover hidden">
                            @endif
                        </div>

                        <div class="absolute bottom-1 right-1 w-9 h-9 bg-yellow-400 rounded-full border-2 border-white flex items-center justify-center cursor-pointer hover:bg-yellow-500 transition-all shadow-sm transform group-hover:scale-110"
                             onclick="document.getElementById('profile_picture').click()">
                            <i class="fa-solid fa-camera text-sm text-white drop-shadow-sm"></i>
                        </div>

                        <input type="file" name="profile_picture" id="profile_picture" class="hidden" accept="image/*" onchange="previewImage(event)">
                    </div>
                </div>
            </div>

            <div class="pt-16 px-8 pb-8 md:px-10 md:pb-10">

                <div class="flex justify-between items-start mb-8">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">{{ $user->name }}</h1>
                        <p class="text-sm text-gray-500 flex items-center mt-1">
                            <i class="fa-solid fa-envelope mr-2 text-gray-400"></i> {{ $user->email }}
                        </p>
                    </div>
                    <span class="bg-green-100 text-[var(--primary)] text-xs font-semibold px-3 py-1 rounded-full border border-green-200">
                        Mahasiswa
                    </span>
                </div>

                @if(session('success'))
                    <div class="p-4 mb-6 text-sm text-green-800 rounded-xl bg-green-50 border border-green-200 shadow-sm flex items-center animate-fade-in-down">
                        <i class="fa-solid fa-circle-check text-xl mr-3"></i>
                        <div>
                            <span class="font-bold">Berhasil!</span> {{ session('success') }}
                        </div>
                    </div>
                @endif

                @if($errors->any())
                    <div class="p-4 mb-6 text-sm text-red-800 rounded-xl bg-red-50 border border-red-200 shadow-sm">
                        <ul class="list-disc list-inside">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">

                    <div class="space-y-5">
                        <h3 class="text-sm font-bold text-gray-400 uppercase tracking-wider border-b pb-2 mb-4">Informasi Pribadi</h3>

                        <div>
                            <label class="block mb-2 text-sm font-medium text-gray-700">Nama Lengkap</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400">
                                    <i class="fa-regular fa-user"></i>
                                </div>
                                <input type="text" name="name" value="{{ old('name', $user->name) }}"
                                       class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-[var(--primary)] focus:border-[var(--primary)] block w-full pl-10 p-2.5 transition-all" required>
                            </div>
                        </div>

                        <div>
                            <label class="block mb-2 text-sm font-medium text-gray-700">Alamat Email</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400">
                                    <i class="fa-regular fa-envelope"></i>
                                </div>
                                <input type="email" name="email" value="{{ old('email', $user->email) }}"
                                       class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-[var(--primary)] focus:border-[var(--primary)] block w-full pl-10 p-2.5 transition-all" required>
                            </div>
                            <p class="mt-1 text-xs text-gray-500">*Gunakan email Unej (@mail.unej.ac.id)</p>
                        </div>
                    </div>

                    <div class="space-y-5 bg-gray-50 p-5 rounded-2xl border border-gray-100">
                        <div class="flex items-center justify-between border-b pb-2 mb-4 border-gray-200">
                            <h3 class="text-sm font-bold text-gray-400 uppercase tracking-wider">Keamanan</h3>
                            <i class="fa-solid fa-shield-halved text-gray-300"></i>
                        </div>

                        <p class="text-xs text-gray-500 italic mb-3">Kosongkan jika tidak ingin mengubah password.</p>

                        <div>
                            <label class="block mb-2 text-sm font-medium text-gray-700">Password Baru</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400">
                                    <i class="fa-solid fa-lock"></i>
                                </div>
                                <input type="password" name="password" placeholder="••••••••"
                                       class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-[var(--primary)] focus:border-[var(--primary)] block w-full pl-10 p-2.5 transition-all">
                            </div>
                        </div>

                        <div>
                            <label class="block mb-2 text-sm font-medium text-gray-700">Ulangi Password</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400">
                                    <i class="fa-solid fa-check-double"></i>
                                </div>
                                <input type="password" name="password_confirmation" placeholder="••••••••"
                                       class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-[var(--primary)] focus:border-[var(--primary)] block w-full pl-10 p-2.5 transition-all">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-8 pt-6 border-t border-gray-100 flex justify-end items-center gap-4">
                    <a href="{{ route('user.dashboard') }}" class="text-gray-500 bg-white hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-gray-200 rounded-lg border border-gray-200 text-sm font-medium px-5 py-2.5 hover:text-gray-900 transition-all">
                        Batal
                    </a>
                    <button type="submit" class="text-white bg-[var(--primary)] hover:bg-green-800 focus:ring-4 focus:ring-green-300 font-medium rounded-lg text-sm px-6 py-2.5 shadow-lg hover:shadow-xl transition-all transform hover:-translate-y-0.5">
                        <i class="fa-solid fa-floppy-disk mr-2"></i> Simpan Perubahan
                    </button>
                </div>

            </div>
        </form>
    </div>
</div>

<style>
    @keyframes fadeInDown {
        from { opacity: 0; transform: translateY(-10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .animate-fade-in-down {
        animation: fadeInDown 0.5s ease-out;
    }
</style>

<script>
    function previewImage(event) {
        const reader = new FileReader();
        reader.onload = function(){
            const preview = document.getElementById('avatar-preview');
            const initial = document.getElementById('avatar-initial');

            preview.src = reader.result;
            preview.classList.remove('hidden');

            if(initial) initial.classList.add('hidden');
        }
        reader.readAsDataURL(event.target.files[0]);
    }
</script>
@endsection
