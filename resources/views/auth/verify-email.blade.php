<x-guest-layout>
    <div class="text-center mb-6">
        <h2 class="text-xl font-bold text-gray-900">Verify Email</h2>
        <p class="mt-1 text-sm text-gray-500">Thanks for signing up! Please verify your email address by clicking the
            link we sent you.</p>
    </div>

    @if (session('status') == 'verification-link-sent')
        <div class="mb-4 p-3 rounded-lg text-sm font-medium"
            style="background-color: #F0FDF4; border: 1px solid #BBF7D0; color: #166534;">
            A new verification link has been sent to your email address.
        </div>
    @endif

    <div class="flex items-center justify-between">
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <button type="submit" class="auth-btn" style="width: auto; padding: 0.5rem 1rem;">
                Resend Verification Email
            </button>
        </form>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="auth-link" style="background: none; border: none; cursor: pointer;">
                Log Out
            </button>
        </form>
    </div>
</x-guest-layout>
