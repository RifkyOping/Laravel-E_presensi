<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-[#24417c] leading-tight">
            {{ __('Profil Saya') }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <!-- Menggunakan Grid 2 Kolom agar bersebelahan dan menghemat scroll -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                
                <!-- Kotak 1: Informasi Profil -->
                <div class="p-6 sm:p-8 bg-white shadow-lg shadow-[#24417c]/5 rounded-3xl border-2 border-[#24417c]/10 hover:border-[#24417c]/30 transition duration-300">
                    <div class="w-full">
                        @include('profile.partials.update-profile-information-form')
                    </div>
                </div>

                <!-- Kotak 2: Ubah Kata Sandi -->
                <div class="p-6 sm:p-8 bg-white shadow-lg shadow-[#24417c]/5 rounded-3xl border-2 border-[#24417c]/10 hover:border-[#24417c]/30 transition duration-300">
                    <div class="w-full">
                        @include('profile.partials.update-password-form')
                    </div>
                </div>



            </div>
            
        </div>
    </div>
</x-app-layout>