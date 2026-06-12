@extends('components.layout', ['title' => 'EduTrack Pro - Task List', 'pageTitle' => 'Student Task List'])

@section('content')
    <div class="max-w-2xl mx-auto space-y-6">
        <div class="flex items-center justify-between">
            <h2 class="font-headline-sm text-headline-sm text-on-surface">Google Sheet Management</h2>
            <button
                type="button"
                id="toggleSettings"
                class="w-10 h-10 flex items-center justify-center rounded-full hover:bg-surface-container transition-colors"
            >
                <span class="material-symbols-outlined text-on-surface-variant">settings</span>
            </button>
        </div>

        <div id="sheetSettings" class="hidden space-y-6">
            @if(session('success'))
            <div class="rounded-md bg-emerald-500/10 text-emerald-200 px-4 py-3 text-sm ring-1 ring-emerald-500/20">
                {{ session('success') }}
            </div>
        @endif

        @if(isset($syncError) && $syncError)
            <div class="rounded-md bg-amber-500/10 text-amber-200 px-4 py-3 text-sm ring-1 ring-amber-500/20">
                {{ $syncError }}
            </div>
        @endif

        @if($errors->any())
            <div class="rounded-md bg-rose-500/10 text-rose-200 px-4 py-3 text-sm ring-1 ring-rose-500/20">
                <ul class="list-disc list-inside">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if($linkedSheet)
            <div class="bg-surface-container-lowest border border-outline-variant rounded-xl p-6">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h2 class="font-headline-sm text-headline-sm text-on-surface">Linked Google Sheet</h2>
                        <p class="font-body-sm text-body-sm text-on-surface-variant mt-1">
                            ID: <span class="font-mono text-xs bg-surface-container-high px-2 py-0.5 rounded">{{ $linkedSheet->spreadsheet_id }}</span>
                        </p>
                        @if($linkedSheet->last_synced_at)
                            <p class="font-body-sm text-body-sm text-on-surface-variant mt-1">
                                Last synced: {{ $linkedSheet->last_synced_at->diffForHumans() }}
                            </p>
                        @endif
                    </div>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-secondary-container text-on-secondary-container">
                        Active
                    </span>
                </div>

                <form method="POST" action="{{ route('task.list.sync') }}" class="flex gap-3">
                    @csrf
                    <button
                        type="submit"
                        class="rounded-lg bg-primary text-on-primary font-semibold py-2.5 px-4 hover:bg-primary/90 transition"
                    >
                        Sync Students
                    </button>
                    <span class="self-center font-body-sm text-body-sm text-on-surface-variant">
                        {{ $students->count() }} student(s) imported
                    </span>
                </form>
            </div>
        @endif

        <div class="bg-surface-container-lowest border border-outline-variant rounded-xl p-6">
            <h2 class="font-headline-sm text-headline-sm text-on-surface mb-2">
                {{ $linkedSheet ? 'Update Linked Google Sheet' : 'Link Google Form / Sheet' }}
            </h2>
            <p class="font-body-sm text-body-sm text-on-surface-variant mb-6">
                {{ $linkedSheet ? 'Paste a new Google Form or Sheet URL to update the link. Then click "Sync Students" to refresh the student list.' : 'Paste a Google Form or Sheet URL to start importing students. The form responses must include at least <strong>Name</strong> and <strong>Email</strong> columns.' }}
            </p>

            <form method="POST" action="{{ route('task.list.link') }}" class="space-y-4">
                @csrf
                <div>
                    <label for="google_form_url" class="block text-sm font-medium text-on-surface mb-1">Google Form / Sheet URL</label>
                    <input
                        type="url"
                        id="google_form_url"
                        name="google_form_url"
                        value="{{ old('google_form_url') }}"
                        required
                        placeholder="https://docs.google.com/forms/d/... or https://docs.google.com/spreadsheets/d/..."
                        class="w-full rounded-lg bg-surface border border-outline-variant px-3 py-2 text-on-surface placeholder:text-outline focus:outline-none focus:ring-2 focus:ring-primary"
                    />
                    <p class="mt-2 text-xs text-on-surface-variant">
                        Make sure the linked Google Sheet is set to <strong>"Anyone with the link can view"</strong>.
                    </p>
                </div>

                <button
                    type="submit"
                    class="rounded-lg bg-primary text-on-primary font-semibold py-2.5 px-4 hover:bg-primary/90 transition"
                >
                    {{ $linkedSheet ? 'Update Link' : 'Link Sheet' }}
                </button>
            </form>
        </div>
        </div>

        <script>
            document.getElementById('toggleSettings').addEventListener('click', function () {
                document.getElementById('sheetSettings').classList.toggle('hidden');
            });
        </script>

        @if($students->count() > 0)
            <div class="bg-surface-container-lowest border border-outline-variant rounded-xl overflow-hidden">
                <div class="p-6 border-b border-outline-variant">
                    <h2 class="font-headline-sm text-headline-sm text-on-surface">Imported Students</h2>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full border-collapse">
                        <thead class="bg-surface-container-low/30">
                            <tr>
                                <th class="px-6 py-3 text-left font-label-sm text-label-sm uppercase text-outline tracking-widest">Name</th>
                                <th class="px-6 py-3 text-left font-label-sm text-label-sm uppercase text-outline tracking-widest">Email</th>
                                <th class="px-6 py-3 text-left font-label-sm text-label-sm uppercase text-outline tracking-widest">Phone</th>
                                <th class="px-6 py-3 text-left font-label-sm text-label-sm uppercase text-outline tracking-widest">Address</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-outline-variant">
                            @foreach($students as $student)
                                <tr class="hover:bg-surface-container-low transition-colors">
                                    <td class="px-6 py-4 font-body-md text-body-md text-on-surface">{{ $student->name }}</td>
                                    <td class="px-6 py-4 font-body-md text-body-md text-on-surface-variant">{{ $student->email }}</td>
                                    <td class="px-6 py-4 font-body-md text-body-md text-on-surface-variant">{{ $student->phone_number ?? '-' }}</td>
                                    <td class="px-6 py-4 font-body-md text-body-md text-on-surface-variant">{{ $student->address ?? '-' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    </div>
@endsection
