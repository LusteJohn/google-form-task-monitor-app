@extends('components.layout', ['title' => 'EduTrack Pro - Dashboard', 'pageTitle' => 'Overview Dashboard'])

@section('content')
	<div class="grid grid-cols-1 md:grid-cols-3 gap-gutter mb-8">
		<div class="bg-surface-container-lowest border border-outline-variant rounded-xl p-6 flex flex-col gap-2">
			<div class="flex items-center justify-between">
				<span class="font-label-sm text-label-sm uppercase tracking-wider text-outline">Total Students</span>
				<span class="material-symbols-outlined text-primary">group</span>
			</div>
			<p class="font-headline-lg text-headline-lg text-on-surface">1,284</p>
			<div class="flex items-center gap-2 mt-2">
				<span class="bg-secondary-container text-on-secondary-container text-xs font-bold px-2 py-0.5 rounded-full">+4.2%</span>
				<span class="font-body-sm text-body-sm text-on-surface-variant">from last month</span>
			</div>
		</div>
		<div class="bg-surface-container-lowest border border-outline-variant rounded-xl p-6 flex flex-col gap-2">
			<div class="flex items-center justify-between">
				<span class="font-label-sm text-label-sm uppercase tracking-wider text-outline">Active Tasks</span>
				<span class="material-symbols-outlined text-primary">assignment</span>
			</div>
			<p class="font-headline-lg text-headline-lg text-on-surface">432</p>
			<div class="flex items-center gap-2 mt-2 text-on-surface-variant">
				<span class="font-body-sm text-body-sm">84 tasks due today</span>
			</div>
		</div>
		<div class="bg-surface-container-lowest border border-outline-variant rounded-xl p-6 flex flex-col gap-2">
			<div class="flex items-center justify-between">
				<span class="font-label-sm text-label-sm uppercase tracking-wider text-outline">Completion Rate</span>
				<span class="material-symbols-outlined text-primary">verified</span>
			</div>
			<p class="font-headline-lg text-headline-lg text-on-surface">91.4%</p>
			<div class="w-full bg-surface-container-high h-2 rounded-full mt-3 overflow-hidden">
				<div class="bg-secondary h-full rounded-full w-[91.4%]"></div>
			</div>
		</div>
	</div>

	<div class="grid grid-cols-1 lg:grid-cols-12 gap-gutter">
		<section class="lg:col-span-8 flex flex-col gap-6">
			<div class="bg-surface-container-lowest border border-outline-variant rounded-xl p-6">
				<div class="flex items-center justify-between mb-8">
					<h2 class="font-headline-sm text-headline-sm text-on-surface">Progress Trends</h2>
					<div class="flex gap-2">
						<button class="px-3 py-1.5 font-label-md text-label-md rounded-lg bg-surface-container-high text-on-surface">Weekly</button>
						<button class="px-3 py-1.5 font-label-md text-label-md rounded-lg text-on-surface-variant hover:bg-surface-container-low transition-colors">Monthly</button>
					</div>
				</div>
				<div class="h-64 flex items-end justify-between gap-4 px-2">
					<div class="w-full bg-surface-container-high relative flex items-end justify-center rounded-t-lg group" style="height: 40%">
						<div class="absolute bottom-full mb-2 bg-primary text-white text-xs py-1 px-2 rounded opacity-0 group-hover:opacity-100 transition-opacity">40%</div>
						<div class="w-full bg-primary-container/30 rounded-t-lg h-full"></div>
						<div class="absolute -bottom-6 text-xs text-outline">Mon</div>
					</div>
					<div class="w-full bg-surface-container-high relative flex items-end justify-center rounded-t-lg group" style="height: 65%">
						<div class="absolute bottom-full mb-2 bg-primary text-white text-xs py-1 px-2 rounded opacity-0 group-hover:opacity-100 transition-opacity">65%</div>
						<div class="w-full bg-primary-container/40 rounded-t-lg h-full"></div>
						<div class="absolute -bottom-6 text-xs text-outline">Tue</div>
					</div>
					<div class="w-full bg-surface-container-high relative flex items-end justify-center rounded-t-lg group" style="height: 55%">
						<div class="absolute bottom-full mb-2 bg-primary text-white text-xs py-1 px-2 rounded opacity-0 group-hover:opacity-100 transition-opacity">55%</div>
						<div class="w-full bg-primary-container/35 rounded-t-lg h-full"></div>
						<div class="absolute -bottom-6 text-xs text-outline">Wed</div>
					</div>
					<div class="w-full bg-surface-container-high relative flex items-end justify-center rounded-t-lg group" style="height: 85%">
						<div class="absolute bottom-full mb-2 bg-primary text-white text-xs py-1 px-2 rounded opacity-0 group-hover:opacity-100 transition-opacity">85%</div>
						<div class="w-full bg-primary-container/60 rounded-t-lg h-full"></div>
						<div class="absolute -bottom-6 text-xs text-outline">Thu</div>
					</div>
					<div class="w-full bg-surface-container-high relative flex items-end justify-center rounded-t-lg group" style="height: 70%">
						<div class="absolute bottom-full mb-2 bg-primary text-white text-xs py-1 px-2 rounded opacity-0 group-hover:opacity-100 transition-opacity">70%</div>
						<div class="w-full bg-primary-container/50 rounded-t-lg h-full"></div>
						<div class="absolute -bottom-6 text-xs text-outline">Fri</div>
					</div>
					<div class="w-full bg-surface-container-high relative flex items-end justify-center rounded-t-lg group" style="height: 30%">
						<div class="absolute bottom-full mb-2 bg-primary text-white text-xs py-1 px-2 rounded opacity-0 group-hover:opacity-100 transition-opacity">30%</div>
						<div class="w-full bg-primary-container/20 rounded-t-lg h-full"></div>
						<div class="absolute -bottom-6 text-xs text-outline">Sat</div>
					</div>
					<div class="w-full bg-surface-container-high relative flex items-end justify-center rounded-t-lg group" style="height: 25%">
						<div class="absolute bottom-full mb-2 bg-primary text-white text-xs py-1 px-2 rounded opacity-0 group-hover:opacity-100 transition-opacity">25%</div>
						<div class="w-full bg-primary-container/15 rounded-t-lg h-full"></div>
						<div class="absolute -bottom-6 text-xs text-outline">Sun</div>
					</div>
				</div>
			</div>

			<div class="bg-surface-container-lowest border border-outline-variant rounded-xl overflow-hidden">
				<div class="p-6 border-b border-outline-variant flex items-center justify-between bg-surface-container-low/50">
					<h2 class="font-headline-sm text-headline-sm text-on-surface">Upcoming Deadlines</h2>
					<a class="text-primary font-label-md text-label-md hover:underline" href="#">View Schedule</a>
				</div>
				<div class="overflow-x-auto">
					<table class="w-full border-collapse">
						<thead class="bg-surface-container-low/30">
							<tr>
								<th class="px-6 py-3 text-left font-label-sm text-label-sm uppercase text-outline tracking-widest">Task Name</th>
								<th class="px-6 py-3 text-left font-label-sm text-label-sm uppercase text-outline tracking-widest">Student Group</th>
								<th class="px-6 py-3 text-left font-label-sm text-label-sm uppercase text-outline tracking-widest">Due Date</th>
								<th class="px-6 py-3 text-left font-label-sm text-label-sm uppercase text-outline tracking-widest">Status</th>
							</tr>
						</thead>
						<tbody class="divide-y divide-outline-variant">
							<tr class="hover:bg-surface-container-low transition-colors group">
								<td class="px-6 py-4 font-body-md text-body-md text-on-surface">Data Analysis Project</td>
								<td class="px-6 py-4 font-body-md text-body-md text-on-surface-variant">Class 10-A</td>
								<td class="px-6 py-4 font-body-md text-body-md text-on-surface-variant">Today, 5:00 PM</td>
								<td class="px-6 py-4">
									<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-tertiary-fixed text-on-tertiary-fixed-variant">Urgent</span>
								</td>
							</tr>
							<tr class="hover:bg-surface-container-low transition-colors group">
								<td class="px-6 py-4 font-body-md text-body-md text-on-surface">World History Essay</td>
								<td class="px-6 py-4 font-body-md text-body-md text-on-surface-variant">Class 11-C</td>
								<td class="px-6 py-4 font-body-md text-body-md text-on-surface-variant">Oct 24, 2023</td>
								<td class="px-6 py-4">
									<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-surface-container-high text-on-surface-variant">Pending</span>
								</td>
							</tr>
							<tr class="hover:bg-surface-container-low transition-colors group">
								<td class="px-6 py-4 font-body-md text-body-md text-on-surface">Physics Lab Report</td>
								<td class="px-6 py-4 font-body-md text-body-md text-on-surface-variant">Class 12-B</td>
								<td class="px-6 py-4 font-body-md text-body-md text-on-surface-variant">Oct 26, 2023</td>
								<td class="px-6 py-4">
									<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-surface-container-high text-on-surface-variant">Pending</span>
								</td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
		</section>

		<section class="lg:col-span-4 flex flex-col gap-6">
			<div class="bg-surface-container-lowest border border-outline-variant rounded-xl p-6 h-full">
				<h2 class="font-headline-sm text-headline-sm text-on-surface mb-6">Recent Activity</h2>
				<div class="flex flex-col gap-6">
					<div class="flex gap-4">
						<div class="w-10 h-10 rounded-full bg-secondary-container flex items-center justify-center shrink-0">
							<span class="material-symbols-outlined text-on-secondary-container">check_circle</span>
						</div>
						<div class="flex flex-col gap-1">
							<p class="font-body-md text-body-md text-on-surface"><span class="font-bold">Sarah Jenkins</span> submitted "Math Quiz 4"</p>
							<p class="font-label-sm text-label-sm text-outline">12 minutes ago</p>
						</div>
					</div>
					<div class="flex gap-4">
						<div class="w-10 h-10 rounded-full bg-primary-container flex items-center justify-center shrink-0">
							<span class="material-symbols-outlined text-on-primary-container">rate_review</span>
						</div>
						<div class="flex flex-col gap-1">
							<p class="font-body-md text-body-md text-on-surface">You graded <span class="font-bold">Leo Martinez's</span> report</p>
							<p class="font-label-sm text-label-sm text-outline">1 hour ago</p>
						</div>
					</div>
					<div class="flex gap-4">
						<div class="w-10 h-10 rounded-full bg-tertiary-fixed-dim flex items-center justify-center shrink-0">
							<span class="material-symbols-outlined text-on-tertiary-fixed">priority_high</span>
						</div>
						<div class="flex flex-col gap-1">
							<p class="font-body-md text-body-md text-on-surface">Deadline missed: <span class="font-bold">James Wilson</span></p>
							<p class="font-label-sm text-label-sm text-outline">2 hours ago</p>
						</div>
					</div>
					<div class="flex gap-4">
						<div class="w-10 h-10 rounded-full bg-secondary-container flex items-center justify-center shrink-0">
							<span class="material-symbols-outlined text-on-secondary-container">person_add</span>
						</div>
						<div class="flex flex-col gap-1">
							<p class="font-body-md text-body-md text-on-surface">New student <span class="font-bold">Emily Chen</span> enrolled</p>
							<p class="font-label-sm text-label-sm text-outline">4 hours ago</p>
						</div>
					</div>
					<div class="flex gap-4">
						<div class="w-10 h-10 rounded-full bg-primary-container flex items-center justify-center shrink-0">
							<span class="material-symbols-outlined text-on-primary-container">edit_document</span>
						</div>
						<div class="flex flex-col gap-1">
							<p class="font-body-md text-body-md text-on-surface">Updated rubric for <span class="font-bold">Final Thesis</span></p>
							<p class="font-label-sm text-label-sm text-outline">Yesterday</p>
						</div>
					</div>
				</div>
				<button class="w-full mt-8 py-3 border border-outline-variant rounded-full font-label-md text-label-md text-on-surface hover:bg-surface-container-low transition-colors">
					View All Activity
				</button>
			</div>
		</section>
	</div>
@endsection
