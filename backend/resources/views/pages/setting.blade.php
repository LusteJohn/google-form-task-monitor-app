@extends('components.layout', ['title' => 'Settings', 'pageTitle' => 'Account Settings'])

@section('content')
	<div class="max-w-2xl">
		<div class="bg-surface-container-lowest border border-outline-variant rounded-xl p-6">
			<h2 class="font-headline-sm text-headline-sm text-on-surface">Update Account</h2>
			<p class="mt-2 text-on-surface-variant font-body-sm text-body-sm">
				Update your email and password. Your current password is required to save changes.
			</p>

			@if (session('success'))
				<div class="mt-4 rounded-md bg-secondary-container text-on-secondary-container px-3 py-2 text-sm">
					{{ session('success') }}
				</div>
			@endif

			<form class="mt-6 space-y-4" method="POST" action="{{ route('settings.update') }}">
				@csrf

				<div>
					<label for="email" class="block text-sm font-medium text-on-surface">Email</label>
					<input
						id="email"
						name="email"
						type="email"
						value="{{ old('email', $user->email) }}"
						required
						class="mt-2 w-full rounded-lg bg-surface-container-low border border-outline-variant px-3 py-2 text-on-surface focus:outline-none focus:ring-2 focus:ring-primary"
					/>
					@error('email')
						<p class="mt-2 text-sm text-on-error-container">{{ $message }}</p>
					@enderror
				</div>

				<div>
					<label for="current_password" class="block text-sm font-medium text-on-surface">Current Password</label>
					<div class="relative mt-2">
						<input
							id="current_password"
							name="current_password"
							type="password"
							required
							class="w-full rounded-lg bg-surface-container-low border border-outline-variant px-3 py-2 pr-12 text-on-surface focus:outline-none focus:ring-2 focus:ring-primary"
						/>
						<button
							type="button"
							class="absolute inset-y-0 right-2 my-1 px-2 text-xs text-on-surface-variant hover:text-on-surface"
							data-toggle-password
							data-target="current_password"
						>
							Show
						</button>
					</div>
					@error('current_password')
						<p class="mt-2 text-sm text-on-error-container">{{ $message }}</p>
					@enderror
				</div>

				<div>
					<label for="password" class="block text-sm font-medium text-on-surface">New Password</label>
					<div class="relative mt-2">
						<input
							id="password"
							name="password"
							type="password"
							class="w-full rounded-lg bg-surface-container-low border border-outline-variant px-3 py-2 pr-12 text-on-surface focus:outline-none focus:ring-2 focus:ring-primary"
						/>
						<button
							type="button"
							class="absolute inset-y-0 right-2 my-1 px-2 text-xs text-on-surface-variant hover:text-on-surface"
							data-toggle-password
							data-target="password"
						>
							Show
						</button>
					</div>
					@error('password')
						<p class="mt-2 text-sm text-on-error-container">{{ $message }}</p>
					@enderror
				</div>

				<div>
					<label for="password_confirmation" class="block text-sm font-medium text-on-surface">Confirm New Password</label>
					<div class="relative mt-2">
						<input
							id="password_confirmation"
							name="password_confirmation"
							type="password"
							class="w-full rounded-lg bg-surface-container-low border border-outline-variant px-3 py-2 pr-12 text-on-surface focus:outline-none focus:ring-2 focus:ring-primary"
						/>
						<button
							type="button"
							class="absolute inset-y-0 right-2 my-1 px-2 text-xs text-on-surface-variant hover:text-on-surface"
							data-toggle-password
							data-target="password_confirmation"
						>
							Show
						</button>
					</div>
				</div>

				<button
					type="submit"
					class="w-full sm:w-auto rounded-lg bg-primary text-on-primary font-semibold px-5 py-2.5 hover:opacity-90 transition"
				>
					Save Changes
				</button>
			</form>
		</div>
	</div>
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
@endsection
