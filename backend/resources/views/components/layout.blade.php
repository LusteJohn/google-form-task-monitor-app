@php
	$title = $title ?? 'EduTrack Pro';
	$pageTitle = $pageTitle ?? 'Overview Dashboard';
@endphp

<!DOCTYPE html>
<html class="light" lang="en">
	<head>
		<meta charset="utf-8" />
		<meta content="width=device-width, initial-scale=1.0" name="viewport" />
		<title>{{ $title }}</title>
		<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
		<link href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@400;600;700&family=Source+Sans+3:wght@400;600&family=Inter:wght@400;500;600&display=swap" rel="stylesheet" />
		<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet" />
		<script id="tailwind-config">
			tailwind.config = {
				darkMode: "class",
				theme: {
					extend: {
						colors: {
							"tertiary-fixed-dim": "#ffb77d",
							"inverse-surface": "#213145",
							"outline": "#757684",
							"tertiary-fixed": "#ffdcc3",
							"secondary-container": "#82f5c1",
							"on-primary": "#ffffff",
							"surface-container-highest": "#d3e4fe",
							"on-tertiary": "#ffffff",
							"error-container": "#ffdad6",
							"on-surface": "#0b1c30",
							"secondary-fixed-dim": "#68dba9",
							"primary-fixed-dim": "#b8c4ff",
							"on-secondary-container": "#00714e",
							"on-tertiary-fixed": "#2f1500",
							"surface-container": "#e5eeff",
							"outline-variant": "#c4c5d5",
							"primary-container": "#1e40af",
							"surface": "#f8f9ff",
							"surface-tint": "#3755c3",
							"secondary": "#006c4a",
							"surface-container-lowest": "#ffffff",
							"surface-bright": "#f8f9ff",
							"on-primary-fixed-variant": "#173bab",
							"on-tertiary-container": "#ffa85d",
							"on-secondary-fixed": "#002114",
							"on-error": "#ffffff",
							"on-error-container": "#93000a",
							"on-secondary": "#ffffff",
							"on-primary-container": "#a8b8ff",
							"on-secondary-fixed-variant": "#005137",
							"tertiary": "#532a00",
							"on-tertiary-fixed-variant": "#6e3900",
							"primary": "#00288e",
							"on-surface-variant": "#444653",
							"surface-container-low": "#eff4ff",
							"inverse-on-surface": "#eaf1ff",
							"background": "#f8f9ff",
							"on-primary-fixed": "#001453",
							"inverse-primary": "#b8c4ff",
							"surface-variant": "#d3e4fe",
							"surface-container-high": "#dce9ff",
							"secondary-fixed": "#85f8c4",
							"on-background": "#0b1c30",
							"tertiary-container": "#743d00",
							"primary-fixed": "#dde1ff",
							"surface-dim": "#cbdbf5",
							"error": "#ba1a1a",
						},
						borderRadius: {
							DEFAULT: "0.125rem",
							lg: "0.25rem",
							xl: "0.5rem",
							full: "0.75rem",
						},
						spacing: {
							base: "4px",
							gutter: "24px",
							"margin-desktop": "32px",
							"margin-mobile": "16px",
							"container-max": "1440px",
						},
						fontFamily: {
							"body-sm": ["Source Sans 3"],
							"headline-sm": ["Public Sans"],
							"body-lg": ["Source Sans 3"],
							"body-md": ["Source Sans 3"],
							"label-sm": ["Inter"],
							"headline-lg": ["Public Sans"],
							"headline-md": ["Public Sans"],
							"headline-lg-mobile": ["Public Sans"],
							"label-md": ["Inter"],
						},
						fontSize: {
							"body-sm": ["14px", { lineHeight: "20px", fontWeight: "400" }],
							"headline-sm": ["20px", { lineHeight: "28px", fontWeight: "600" }],
							"body-lg": ["18px", { lineHeight: "28px", fontWeight: "400" }],
							"body-md": ["16px", { lineHeight: "24px", fontWeight: "400" }],
							"label-sm": ["12px", { lineHeight: "16px", letterSpacing: "0.04em", fontWeight: "600" }],
							"headline-lg": ["32px", { lineHeight: "40px", fontWeight: "700" }],
							"headline-md": ["24px", { lineHeight: "32px", fontWeight: "600" }],
							"headline-lg-mobile": ["24px", { lineHeight: "32px", fontWeight: "700" }],
							"label-md": ["14px", { lineHeight: "20px", letterSpacing: "0.02em", fontWeight: "500" }],
						},
					},
				},
			};
		</script>
		<style>
			.material-symbols-outlined {
				font-variation-settings: "FILL" 0, "wght" 400, "GRAD" 0, "opsz" 24;
				display: inline-block;
				line-height: 1;
				text-transform: none;
				letter-spacing: normal;
				word-wrap: normal;
				white-space: nowrap;
				direction: ltr;
			}
			body {
				font-family: "Source Sans 3", sans-serif;
				min-height: max(884px, 100dvh);
			}
		</style>
	</head>
	<body class="bg-background text-on-background min-h-screen flex flex-col md:flex-row">
		@include('components.sidebar')
		<div class="flex-1 flex flex-col min-w-0">
			<header class="flex justify-between items-center w-full px-margin-mobile md:px-margin-desktop h-16 bg-surface dark:bg-inverse-surface border-b border-outline-variant dark:border-outline z-30 sticky top-0">
				<div class="flex items-center gap-4 lg:hidden">
					<span class="font-headline-md text-headline-md font-bold text-primary dark:text-primary-fixed-dim">EduTrack Pro</span>
				</div>
				<div class="hidden lg:block">
					<h1 class="font-headline-sm text-headline-sm text-primary dark:text-inverse-primary">{{ $pageTitle }}</h1>
				</div>
				<div class="flex items-center gap-4">
					<button class="w-10 h-10 flex items-center justify-center rounded-full hover:bg-surface-container dark:hover:bg-inverse-surface transition-colors duration-200">
						<span class="material-symbols-outlined text-on-surface-variant">notifications</span>
					</button>
					<div class="w-8 h-8 rounded-full overflow-hidden bg-surface-container-highest border border-outline-variant">
						<img
							alt="User Profile"
							class="w-full h-full object-cover"
							src="https://lh3.googleusercontent.com/aida-public/AB6AXuBIlG0vfUOMaYYe6pItiOHFQ4HBFfPvlWf7F6c0IHUK7jrS4yntlSCQaCZRzNThCFXg-JlN0q5PP1IF9Apnx1yhlT3pj0AgRxK3KeqI4nXnvFGRhiMYFFlByndlD_mTNcQmYEXOQfPB5QgdPkrwfnNZlrCG3WF1-FjeRmWBT1WSIqlklPlptZ-fD4sipBO0RQKIgzdNpTu6ySpUQ3EKrjLaKJtVDUEjj3c_QAB5u1LoBpOkup392AT-RWCTkdVY75UkU8lLyvi7JH4"
						/>
					</div>
				</div>
			</header>

			<main class="flex-1 p-margin-mobile md:p-margin-desktop max-w-container-max mx-auto w-full pb-24 lg:pb-margin-desktop">
				@yield('content')
			</main>
		</div>

		<nav class="fixed bottom-0 left-0 w-full z-50 flex lg:hidden justify-around items-center px-2 py-3 bg-surface dark:bg-inverse-surface border-t border-outline-variant dark:border-outline shadow-lg rounded-t-xl">
			<a class="flex flex-col items-center justify-center bg-secondary-container dark:bg-on-secondary-fixed-variant text-on-secondary-container dark:text-secondary-fixed rounded-full px-4 py-1 scale-95 active:scale-90 transition-transform" href="#">
				<span class="material-symbols-outlined">home</span>
				<span class="font-label-sm text-label-sm">Home</span>
			</a>
			<a class="flex flex-col items-center justify-center text-on-surface-variant dark:text-surface-variant scale-95 active:scale-90 transition-transform" href="#">
				<span class="material-symbols-outlined">person_search</span>
				<span class="font-label-sm text-label-sm">Students</span>
			</a>
			<a class="flex flex-col items-center justify-center text-on-surface-variant dark:text-surface-variant scale-95 active:scale-90 transition-transform" href="#">
				<span class="material-symbols-outlined">analytics</span>
				<span class="font-label-sm text-label-sm">Details</span>
			</a>
			<a class="flex flex-col items-center justify-center text-on-surface-variant dark:text-surface-variant scale-95 active:scale-90 transition-transform" href="#">
				<span class="material-symbols-outlined">edit_note</span>
				<span class="font-label-sm text-label-sm">Log</span>
			</a>
		</nav>
		<button class="fixed bottom-24 right-6 lg:bottom-10 lg:right-10 w-14 h-14 bg-primary text-on-primary rounded-full shadow-xl flex items-center justify-center transition-transform hover:scale-105 active:scale-95 z-40">
			<span class="material-symbols-outlined text-3xl">add</span>
		</button>
	</body>
</html>
