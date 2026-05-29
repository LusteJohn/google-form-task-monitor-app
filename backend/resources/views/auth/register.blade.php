<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Register</title>
    </head>
    <body class="min-h-screen bg-slate-950 text-slate-100">
        <div class="min-h-screen flex items-center justify-center px-4 py-10">
            <div class="w-full max-w-md rounded-2xl bg-slate-900/70 ring-1 ring-slate-800 shadow-xl">
                <div class="px-6 py-8">
                    <h1 class="text-2xl font-semibold tracking-tight">Create account</h1>
                    <p class="mt-1 text-sm text-slate-400">Use your email and password to sign up.</p>

                    <form class="mt-6 space-y-4" method="POST" action="{{ route('register.submit') }}">
                        @csrf
                        <div>
                            <label for="email" class="block text-sm font-medium text-slate-200">Email</label>
                            <input
                                id="email"
                                name="email"
                                type="email"
                                value="{{ old('email') }}"
                                required
                                class="mt-2 w-full rounded-lg bg-slate-950/60 border border-slate-800 px-3 py-2 text-slate-100 placeholder:text-slate-500 focus:outline-none focus:ring-2 focus:ring-cyan-400"
                                placeholder="you@example.com"
                            />
                            @error('email')
                                <p class="mt-2 text-sm text-rose-300">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="password" class="block text-sm font-medium text-slate-200">Password</label>
                            <div class="relative mt-2">
                                <input
                                    id="password"
                                    name="password"
                                    type="password"
                                    required
                                    class="w-full rounded-lg bg-slate-950/60 border border-slate-800 px-3 py-2 pr-12 text-slate-100 placeholder:text-slate-500 focus:outline-none focus:ring-2 focus:ring-cyan-400"
                                    placeholder="Create a password"
                                />
                                <button
                                    type="button"
                                    class="absolute inset-y-0 right-2 my-1 px-2 text-xs text-slate-300 hover:text-slate-100"
                                    data-toggle-password
                                    data-target="password"
                                >
                                    Show
                                </button>
                            </div>
                            @error('password')
                                <p class="mt-2 text-sm text-rose-300">{{ $message }}</p>
                            @enderror
                        </div>

                        <button
                            type="submit"
                            class="w-full rounded-lg bg-cyan-400 text-slate-900 font-semibold py-2.5 hover:bg-cyan-300 transition"
                        >
                            Register
                        </button>
                    </form>

                    <a
                        href="{{ route('login') }}"
                        class="mt-6 inline-flex w-full items-center justify-center rounded-lg border border-slate-700 bg-slate-900/60 px-3 py-2 text-sm font-medium text-slate-100 hover:bg-slate-900 transition"
                    >
                        Back to login
                    </a>
                </div>
            </div>
        </div>
    </body>
    <script>
        document.querySelectorAll('[data-toggle-password]').forEach((button) => {
            button.addEventListener('click', () => {
                const targetId = button.getAttribute('data-target');
                const input = document.getElementById(targetId);
                if (!input) {
                    return;
                }
                const nextType = input.type === 'password' ? 'text' : 'password';
                input.type = nextType;
                button.textContent = nextType === 'password' ? 'Show' : 'Hide';
            });
        });
    </script>
</html>
