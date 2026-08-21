@extends('layouts.admin')

@section('header_title', 'Payment Methods')

@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <h2 class="text-2xl font-bold text-gray-900">Payment Methods</h2>
    </div>

    <!-- Add New Method -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden p-6">
        <h3 class="text-lg font-bold mb-4">Add New Payment Method</h3>
        <form action="{{ route('admin.payment-methods.store') }}" method="POST" class="space-y-4">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Provider Name (e.g., bKash)</label>
                    <input type="text" name="provider_name" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-primary focus:border-primary" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Account Number</label>
                    <input type="text" name="account_number" maxlength="11" minlength="11" pattern="01[3-9][0-9]{8}" title="Must be exactly 11 digits starting with 01" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-primary focus:border-primary" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Account Type</label>
                    <select name="account_type" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-primary focus:border-primary">
                        <option value="Personal">Personal</option>
                        <option value="Agent">Agent</option>
                        <option value="Merchant">Merchant</option>
                    </select>
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Instructions (Optional)</label>
                <textarea name="instructions" rows="2" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-primary focus:border-primary" placeholder="E.g., Send money and use your student ID as reference"></textarea>
            </div>
            <button type="submit" class="bg-primary hover:bg-green-700 text-white font-bold py-2 px-6 rounded-lg transition-colors">Add Payment Method</button>
        </form>
    </div>

    <!-- Existing Methods -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-gray-500">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th scope="col" class="px-6 py-4 font-semibold">Provider</th>
                        <th scope="col" class="px-6 py-4 font-semibold">Account Details</th>
                        <th scope="col" class="px-6 py-4 font-semibold">Status</th>
                        <th scope="col" class="px-6 py-4 font-semibold text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($methods as $method)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4 font-bold text-gray-900">{{ $method->provider_name }}</td>
                        <td class="px-6 py-4">
                            {{ $method->account_number }} <span class="text-xs text-gray-400">({{ $method->account_type }})</span>
                            @if($method->instructions)
                                <div class="text-xs text-gray-500 mt-1 truncate max-w-xs">{{ $method->instructions }}</div>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            @if($method->is_active)
                                <span class="bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded-full">Active</span>
                            @else
                                <span class="bg-gray-100 text-gray-800 text-xs font-medium px-2.5 py-0.5 rounded-full">Inactive</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right">
                            <button onclick='openEditPaymentMethodModal({{ $method->id }}, @json($method->provider_name), @json($method->account_number), @json($method->account_type), @json($method->instructions), {{ $method->is_active ? "true" : "false" }})' class="text-gray-500 hover:text-gray-900 font-medium mr-3">Edit</button>
                            <form action="{{ route('admin.payment-methods.destroy', $method) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this payment method?');" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-900 font-medium">Delete</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-6 py-8 text-center text-gray-500">
                            No payment methods found. Add one above.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Edit Payment Method Modal -->
<div id="editPaymentMethodModal" class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm flex items-center justify-center z-50 hidden">
    <div class="bg-white rounded-2xl p-6 w-full max-w-lg shadow-xl">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-bold">Edit Payment Method</h3>
            <button onclick="document.getElementById('editPaymentMethodModal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">&times;</button>
        </div>
        <form method="POST" id="editPaymentMethodForm" action="">
            @csrf
            @method('PUT')
            <div class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Provider Name <span class="text-red-500">*</span></label>
                        <input type="text" name="provider_name" id="edit_provider_name" required class="w-full rounded-lg border-gray-300 focus:border-primary focus:ring-primary">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Account Number <span class="text-red-500">*</span></label>
                        <input type="text" name="account_number" id="edit_account_number" maxlength="11" minlength="11" pattern="01[3-9][0-9]{8}" title="Must be exactly 11 digits starting with 01" required class="w-full rounded-lg border-gray-300 focus:border-primary focus:ring-primary">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Account Type <span class="text-red-500">*</span></label>
                    <select name="account_type" id="edit_account_type" required class="w-full rounded-lg border-gray-300 focus:border-primary focus:ring-primary">
                        <option value="Personal">Personal</option>
                        <option value="Agent">Agent</option>
                        <option value="Merchant">Merchant</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Instructions (Optional)</label>
                    <textarea name="instructions" id="edit_instructions" rows="2" class="w-full rounded-lg border-gray-300 focus:border-primary focus:ring-primary"></textarea>
                </div>
                <div class="flex items-center">
                    <input type="checkbox" name="is_active" id="edit_pm_is_active" value="1" class="rounded text-primary focus:ring-primary mr-2">
                    <label for="edit_pm_is_active" class="text-sm font-medium text-gray-700">Active</label>
                </div>
                <button type="submit" class="w-full px-4 py-2 bg-primary text-white rounded-lg font-medium hover:opacity-90">Save Changes</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openEditPaymentMethodModal(id, provider, account, type, instructions, isActive) {
        document.getElementById('edit_provider_name').value = provider;
        document.getElementById('edit_account_number').value = account;
        document.getElementById('edit_account_type').value = type;
        document.getElementById('edit_instructions').value = instructions;
        document.getElementById('edit_pm_is_active').checked = isActive;
        
        document.getElementById('editPaymentMethodForm').action = `/admin/payment-methods/${id}`;
        document.getElementById('editPaymentMethodModal').classList.remove('hidden');
    }
</script>
@endsection
