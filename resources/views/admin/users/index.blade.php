@extends('layouts.admin')

@section('header_title', 'User Management')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <h1 class="text-2xl font-bold text-gray-900">Users</h1>
        
        <div class="flex w-full sm:w-auto gap-2">
            <form method="GET" action="{{ route('admin.users') }}" class="w-full sm:w-auto relative">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search name or email..." class="w-full sm:w-64 pl-10 pr-4 py-2 rounded-xl border-gray-300 focus:border-primary focus:ring-primary text-sm">
                <svg class="w-5 h-5 text-gray-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            </form>
            <button onclick="document.getElementById('createUserModal').classList.remove('hidden')" class="shrink-0 px-4 py-2 bg-primary text-white rounded-xl font-medium hover:opacity-90 transition-opacity whitespace-nowrap">
                + Add User
            </button>
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

    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead class="bg-gray-50 text-gray-500 text-sm uppercase tracking-wide border-b border-gray-100">
                    <tr>
                        <th class="px-6 py-4 font-medium">User</th>
                        <th class="px-6 py-4 font-medium">Role</th>
                        <th class="px-6 py-4 font-medium">Joined</th>
                        <th class="px-6 py-4 font-medium text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($users as $user)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    @if($user->profile_picture)
                                        <img src="{{ Storage::url($user->profile_picture) }}" alt="{{ $user->name }}" class="w-10 h-10 rounded-full object-cover mr-4 shrink-0 border border-gray-200">
                                    @else
                                        <div class="w-10 h-10 rounded-full bg-primary/10 text-primary flex items-center justify-center font-bold mr-4 shrink-0">
                                            {{ substr($user->name, 0, 1) }}
                                        </div>
                                    @endif
                                    <div>
                                        <div class="font-medium text-gray-900">{{ $user->name }}</div>
                                        <div class="text-sm text-gray-500">{{ $user->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-3 py-1 rounded-full text-xs font-medium {{ $user->role === 'admin' ? 'bg-purple-100 text-purple-700' : 'bg-gray-100 text-gray-700' }}">
                                    {{ ucfirst($user->role) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500">
                                {{ $user->created_at->format('M d, Y') }}
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-3">
                                    <a href="{{ route('admin.users.show', $user) }}" class="text-primary hover:text-primary-dark font-medium text-sm transition-colors">View Progress</a>
                                    <button onclick="openEditUserModal({{ $user->id }}, '{{ addslashes($user->name) }}', '{{ addslashes($user->email) }}', '{{ $user->role }}')" class="text-gray-500 hover:text-gray-900 font-medium text-sm transition-colors">Edit</button>
                                    @if($user->id !== auth()->id())
                                        <form method="POST" action="{{ route('admin.users.destroy', $user) }}" onsubmit="return confirm('Are you sure you want to delete this user? This cannot be undone.');" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-500 hover:text-red-700 font-medium text-sm transition-colors">Delete</button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-12 text-center text-gray-500">
                                No users found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($users->hasPages())
            <div class="px-6 py-4 border-t border-gray-100">
                {{ $users->links() }}
            </div>
        @endif
    </div>
</div>

<!-- Create User Modal -->
<div id="createUserModal" class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm flex items-center justify-center z-50 hidden">
    <div class="bg-white rounded-2xl p-6 w-full max-w-md shadow-xl">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-bold">Add New User</h3>
            <button onclick="document.getElementById('createUserModal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">&times;</button>
        </div>
        <form method="POST" action="{{ route('admin.users.store') }}" enctype="multipart/form-data">
            @csrf
            <div class="space-y-4 max-h-[70vh] overflow-y-auto pr-2">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Name <span class="text-red-500">*</span></label>
                    <input type="text" name="name" required class="w-full rounded-lg border-gray-300 focus:border-primary focus:ring-primary">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email <span class="text-red-500">*</span></label>
                    <input type="email" name="email" required class="w-full rounded-lg border-gray-300 focus:border-primary focus:ring-primary">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Password <span class="text-red-500">*</span></label>
                    <input type="password" name="password" required class="w-full rounded-lg border-gray-300 focus:border-primary focus:ring-primary">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Role <span class="text-red-500">*</span></label>
                    <select name="role" required class="w-full rounded-lg border-gray-300 focus:border-primary focus:ring-primary">
                        <option value="student">Student</option>
                        <option value="instructor">Instructor</option>
                        <option value="admin">Admin</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Profile Picture (Optional)</label>
                    <input type="file" name="profile_picture" accept="image/*" class="w-full rounded-lg border-gray-300 focus:border-primary focus:ring-primary text-sm file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-primary/10 file:text-primary hover:file:bg-primary/20">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Cover Photo (Optional)</label>
                    <input type="file" name="cover_photo" accept="image/*" class="w-full rounded-lg border-gray-300 focus:border-primary focus:ring-primary text-sm file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-primary/10 file:text-primary hover:file:bg-primary/20">
                </div>
                <button type="submit" class="w-full px-4 py-2 bg-primary text-white rounded-lg font-medium hover:opacity-90 mt-2">Create User</button>
            </div>
        </form>
    </div>
</div>

<!-- Edit User Modal -->
<div id="editUserModal" class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm flex items-center justify-center z-50 hidden">
    <div class="bg-white rounded-2xl p-6 w-full max-w-md shadow-xl">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-bold">Edit User</h3>
            <button onclick="document.getElementById('editUserModal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">&times;</button>
        </div>
        <form method="POST" id="editUserForm" action="" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="space-y-4 max-h-[70vh] overflow-y-auto pr-2">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Name <span class="text-red-500">*</span></label>
                    <input type="text" name="name" id="edit_name" required class="w-full rounded-lg border-gray-300 focus:border-primary focus:ring-primary">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email <span class="text-red-500">*</span></label>
                    <input type="email" name="email" id="edit_email" required class="w-full rounded-lg border-gray-300 focus:border-primary focus:ring-primary">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                    <input type="password" name="password" placeholder="Leave blank to keep current" class="w-full rounded-lg border-gray-300 focus:border-primary focus:ring-primary">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Role <span class="text-red-500">*</span></label>
                    <select name="role" id="edit_role" required class="w-full rounded-lg border-gray-300 focus:border-primary focus:ring-primary">
                        <option value="student">Student</option>
                        <option value="instructor">Instructor</option>
                        <option value="admin">Admin</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Profile Picture (Optional)</label>
                    <input type="file" name="profile_picture" accept="image/*" class="w-full rounded-lg border-gray-300 focus:border-primary focus:ring-primary text-sm file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-primary/10 file:text-primary hover:file:bg-primary/20">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Cover Photo (Optional)</label>
                    <input type="file" name="cover_photo" accept="image/*" class="w-full rounded-lg border-gray-300 focus:border-primary focus:ring-primary text-sm file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-primary/10 file:text-primary hover:file:bg-primary/20">
                </div>
                <button type="submit" class="w-full px-4 py-2 bg-primary text-white rounded-lg font-medium hover:opacity-90 mt-2">Save Changes</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openEditUserModal(id, name, email, role) {
        document.getElementById('edit_name').value = name;
        document.getElementById('edit_email').value = email;
        document.getElementById('edit_role').value = role;
        
        document.getElementById('editUserForm').action = `/admin/users/${id}`;
        document.getElementById('editUserModal').classList.remove('hidden');
    }
</script>
@endsection
