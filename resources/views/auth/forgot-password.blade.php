<x-guest-layout>
    <div class="text-center mb-6">
        <h2 class="text-xl font-bold text-gray-900">Forgot password?</h2>
        <p class="mt-1 text-sm text-gray-500">Enter your email and we'll send you a reset link.</p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}">
        @csrf

        <!-- Email Address -->
        <div class="mb-6">
            <label for="email" class="auth-label">Email Address</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                class="auth-input" placeholder="you@example.com">
            @error('email')
                <p class="auth-error">{{ $message }}</p>
            @enderror
        </div>

        <button type="submit" class="auth-btn">
            Send Reset Link
        </button>

        <p class="text-center mt-5 text-sm text-gray-500">
            Remember your password?
            <a href="{{ route('login') }}" class="auth-link">Sign in</a>
        </p>
    </form>
</x-guest-layout>
