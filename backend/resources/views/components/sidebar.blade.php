<aside class="hidden lg:flex flex-col h-screen sticky top-0 px-4 py-6 bg-surface-container-low dark:bg-inverse-surface border-r border-outline-variant dark:border-outline w-72 shrink-0">
    <div class="mb-8 px-4 flex items-center gap-3">
        <span class="font-headline-sm text-headline-sm font-bold text-primary">EduTrack Pro</span>
    </div>
    <div class="flex items-center gap-3 px-4 mb-8">
        <div class="w-10 h-10 rounded-full bg-primary-fixed-dim flex items-center justify-center overflow-hidden">
            <img
                alt="Instructor Profile"
                class="w-full h-full object-cover"
                src="https://lh3.googleusercontent.com/aida-public/AB6AXuDguXXdwD06PAMtanJa1A9ZVx7KSr7sf_SNUqRTmvjlGgLo7idJ4FMV4ms3Qs6CXxEpqD4TSuL2mvXJAp7tZuDdDyiIsobB8GeFmVyulTsd3onc1EXo8nqAKzXESnzE2Zq-UX_-HRzvXoDBra7RCQ9htVWnlATpMGQFg50HFa0bbwk1qUjVWxml3FCm9iaT8mlj3zFKTe3YJ2-uPYcnatbRP6L9Z-aMc5tWd6bGcr-ntS6d_Qs-84QuDK8_J8qnU_pFa5KzjQeVjFU"
            />
        </div>
        <div>
            <p class="font-label-md text-label-md font-bold text-on-surface">Academic Admin</p>
            <p class="font-body-sm text-body-sm text-on-surface-variant">Department of Education</p>
        </div>
    </div>
    <nav class="flex flex-col gap-1 flex-1">
        <a class="flex items-center gap-3 px-4 py-3 bg-primary-container dark:bg-primary text-on-primary-container dark:text-on-primary font-bold rounded-full transition-all duration-200 ease-in-out" href="{{ route('dashboard') }}">
            <span class="material-symbols-outlined">dashboard</span>
            <span class="font-body-md text-body-md">Dashboard</span>
        </a>
        <a class="flex items-center gap-3 px-4 py-3 text-on-surface-variant dark:text-surface-variant hover:bg-surface-container-high transition-all duration-200 ease-in-out" href="#">
            <span class="material-symbols-outlined">group</span>
            <span class="font-body-md text-body-md">Student List</span>
        </a>
        <a class="flex items-center gap-3 px-4 py-3 text-on-surface-variant dark:text-surface-variant hover:bg-surface-container-high transition-all duration-200 ease-in-out" href="#">
            <span class="material-symbols-outlined">star</span>
            <span class="font-body-md text-body-md">Milestones</span>
        </a>
        <a class="flex items-center gap-3 px-4 py-3 text-on-surface-variant dark:text-surface-variant hover:bg-surface-container-high transition-all duration-200 ease-in-out" href="#">
            <span class="material-symbols-outlined">add_task</span>
            <span class="font-body-md text-body-md">Submit Task</span>
        </a>
        <a class="flex items-center gap-3 px-4 py-3 text-on-surface-variant dark:text-surface-variant hover:bg-surface-container-high transition-all duration-200 ease-in-out" href="{{ route('settings') }}">
            <span class="material-symbols-outlined">settings</span>
            <span class="font-body-md text-body-md">Settings</span>
        </a>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="w-full flex items-center gap-3 px-4 py-3 text-on-surface-variant dark:text-surface-variant hover:bg-surface-container-high transition-all duration-200 ease-in-out">
                <span class="material-symbols-outlined">exit_to_app</span>
                <span class="font-body-md text-body-md">Logout</span>
            </button>
        </form>
    </nav>
    <div class="px-4 mt-auto">
        <p class="font-label-sm text-label-sm text-outline">v1.0.4</p>
    </div>
</aside>
