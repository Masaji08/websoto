<x-guest-layout>
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <div>
            <label for="email" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">Email</label>
            <input id="email" type="email" name="email" :value="old('email')" required autofocus autocomplete="username"
                class="w-full border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-xl px-4 py-2.5 text-sm focus:border-[#FF8C42] focus:ring-2 focus:ring-[#FF8C42]/20 outline-none transition-all"
                placeholder="contoh@email.com">
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="mt-4">
            <label for="password" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">Password</label>
            <input id="password" type="password" name="password" required autocomplete="current-password"
                class="w-full border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-xl px-4 py-2.5 text-sm focus:border-[#FF8C42] focus:ring-2 focus:ring-[#FF8C42]/20 outline-none transition-all"
                placeholder="password harus diisi">
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="flex items-center justify-between mt-5">
            <label for="remember_me" class="inline-flex items-center gap-2 cursor-pointer">
                <input id="remember_me" type="checkbox" name="remember"
                    class="rounded-lg border-gray-300 dark:border-gray-600 text-[#FF8C42] focus:ring-[#FF8C42] dark:bg-gray-700">
                <span class="text-sm text-gray-600 dark:text-gray-400">Ingat saya</span>
            </label>
        </div>

        <div class="mt-6">
            <button type="submit"
                class="w-full bg-gradient-to-r from-[#FF8C42] to-[#6D4C41] text-white font-bold py-3 rounded-xl text-sm hover:from-[#e67e3a] hover:to-[#5a3d34] transition-all shadow-lg shadow-[#FF8C42]/20 active:scale-[0.98]">
                Masuk
            </button>
        </div>
    </form>
</x-guest-layout>
