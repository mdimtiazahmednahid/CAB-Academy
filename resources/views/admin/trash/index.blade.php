@extends('layouts.admin')

@section('header_title', 'Trash Bin')

@section('content')
<div class="space-y-6" x-data="{ activeTab: 'users' }">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Trash Bin</h1>
            <p class="text-gray-500 text-sm mt-1">Manage and restore deleted records.</p>
        </div>
    </div>

    @if(session('success'))
        <div class="p-4 text-sm text-green-800 rounded-lg bg-green-50 border border-green-200">
            {{ session('success') }}
        </div>
    @endif
    @if($errors->any())
        <div class="p-4 text-sm text-red-800 rounded-lg bg-red-50 border border-red-200">
            {{ $errors->first() }}
        </div>
    @endif

    <!-- Tabs -->
    <div class="border-b border-gray-200">
        <nav class="-mb-px flex space-x-8 overflow-x-auto" aria-label="Tabs">
            <button @click="activeTab = 'users'" :class="{'border-primary text-primary font-bold': activeTab === 'users', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'users'}" class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors">
                Users ({{ $trashedUsers->count() }})
            </button>
            <button @click="activeTab = 'courses'" :class="{'border-primary text-primary font-bold': activeTab === 'courses', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'courses'}" class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors">
                Courses ({{ $trashedCourses->count() }})
            </button>
            <button @click="activeTab = 'jobs'" :class="{'border-primary text-primary font-bold': activeTab === 'jobs', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'jobs'}" class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors">
                Job Posts ({{ $trashedJobs->count() }})
            </button>
            <button @click="activeTab = 'payments'" :class="{'border-primary text-primary font-bold': activeTab === 'payments', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'payments'}" class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors">
                Payment Methods ({{ $trashedPaymentMethods->count() }})
            </button>
        </nav>
    </div>

    <!-- Users Tab -->
    <div x-show="activeTab === 'users'" x-cloak class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead class="bg-gray-50 text-gray-500 text-sm uppercase tracking-wide border-b border-gray-100">
                    <tr>
                        <th class="px-6 py-4 font-medium">Name</th>
                        <th class="px-6 py-4 font-medium">Email</th>
                        <th class="px-6 py-4 font-medium">Role</th>
                        <th class="px-6 py-4 font-medium">Deleted At</th>
                        <th class="px-6 py-4 font-medium text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($trashedUsers as $user)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 font-medium text-gray-900">{{ $user->name }}</td>
                            <td class="px-6 py-4 text-gray-500">{{ $user->email }}</td>
                            <td class="px-6 py-4 text-gray-500">{{ ucfirst($user->role) }}</td>
                            <td class="px-6 py-4 text-gray-500 text-sm">{{ $user->deleted_at->format('M d, Y h:i A') }}</td>
                            <td class="px-6 py-4 text-right">
                                <form method="POST" action="{{ route('admin.trash.restore', ['type' => 'user', 'id' => $user->id]) }}" class="inline">
                                    @csrf
                                    <button type="submit" class="text-primary hover:text-primary-dark font-medium text-sm mr-3">Restore</button>
                                </form>
                                <form method="POST" action="{{ route('admin.trash.forceDelete', ['type' => 'user', 'id' => $user->id]) }}" onsubmit="return confirm('Permanently delete this user? This cannot be undone.');" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-500 hover:text-red-700 font-medium text-sm">Delete Forever</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-6 py-12 text-center text-gray-500">No trashed users.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Courses Tab -->
    <div x-show="activeTab === 'courses'" x-cloak class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead class="bg-gray-50 text-gray-500 text-sm uppercase tracking-wide border-b border-gray-100">
                    <tr>
                        <th class="px-6 py-4 font-medium">Title</th>
                        <th class="px-6 py-4 font-medium">Category</th>
                        <th class="px-6 py-4 font-medium">Deleted At</th>
                        <th class="px-6 py-4 font-medium text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($trashedCourses as $course)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 font-medium text-gray-900">{{ $course->title }}</td>
                            <td class="px-6 py-4 text-gray-500">{{ $course->category ?? 'N/A' }}</td>
                            <td class="px-6 py-4 text-gray-500 text-sm">{{ $course->deleted_at->format('M d, Y h:i A') }}</td>
                            <td class="px-6 py-4 text-right">
                                <form method="POST" action="{{ route('admin.trash.restore', ['type' => 'course', 'id' => $course->id]) }}" class="inline">
                                    @csrf
                                    <button type="submit" class="text-primary hover:text-primary-dark font-medium text-sm mr-3">Restore</button>
                                </form>
                                <form method="POST" action="{{ route('admin.trash.forceDelete', ['type' => 'course', 'id' => $course->id]) }}" onsubmit="return confirm('Permanently delete this course?');" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-500 hover:text-red-700 font-medium text-sm">Delete Forever</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="px-6 py-12 text-center text-gray-500">No trashed courses.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Jobs Tab -->
    <div x-show="activeTab === 'jobs'" x-cloak class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead class="bg-gray-50 text-gray-500 text-sm uppercase tracking-wide border-b border-gray-100">
                    <tr>
                        <th class="px-6 py-4 font-medium">Job Title</th>
                        <th class="px-6 py-4 font-medium">Company</th>
                        <th class="px-6 py-4 font-medium">Deleted At</th>
                        <th class="px-6 py-4 font-medium text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($trashedJobs as $job)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 font-medium text-gray-900">{{ $job->title }}</td>
                            <td class="px-6 py-4 text-gray-500">{{ $job->company }}</td>
                            <td class="px-6 py-4 text-gray-500 text-sm">{{ $job->deleted_at->format('M d, Y h:i A') }}</td>
                            <td class="px-6 py-4 text-right">
                                <form method="POST" action="{{ route('admin.trash.restore', ['type' => 'job', 'id' => $job->id]) }}" class="inline">
                                    @csrf
                                    <button type="submit" class="text-primary hover:text-primary-dark font-medium text-sm mr-3">Restore</button>
                                </form>
                                <form method="POST" action="{{ route('admin.trash.forceDelete', ['type' => 'job', 'id' => $job->id]) }}" onsubmit="return confirm('Permanently delete this job?');" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-500 hover:text-red-700 font-medium text-sm">Delete Forever</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="px-6 py-12 text-center text-gray-500">No trashed job posts.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Payments Tab -->
    <div x-show="activeTab === 'payments'" x-cloak class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead class="bg-gray-50 text-gray-500 text-sm uppercase tracking-wide border-b border-gray-100">
                    <tr>
                        <th class="px-6 py-4 font-medium">Method Name</th>
                        <th class="px-6 py-4 font-medium">Deleted At</th>
                        <th class="px-6 py-4 font-medium text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($trashedPaymentMethods as $pm)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 font-medium text-gray-900">{{ $pm->name ?? $pm->provider_name }}</td>
                            <td class="px-6 py-4 text-gray-500 text-sm">{{ $pm->deleted_at->format('M d, Y h:i A') }}</td>
                            <td class="px-6 py-4 text-right">
                                <form method="POST" action="{{ route('admin.trash.restore', ['type' => 'payment_method', 'id' => $pm->id]) }}" class="inline">
                                    @csrf
                                    <button type="submit" class="text-primary hover:text-primary-dark font-medium text-sm mr-3">Restore</button>
                                </form>
                                <form method="POST" action="{{ route('admin.trash.forceDelete', ['type' => 'payment_method', 'id' => $pm->id]) }}" onsubmit="return confirm('Permanently delete this payment method?');" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-500 hover:text-red-700 font-medium text-sm">Delete Forever</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="3" class="px-6 py-12 text-center text-gray-500">No trashed payment methods.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
