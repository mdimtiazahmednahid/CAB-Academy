@extends('layouts.student')

@section('header_title', 'Notifications')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="flex justify-between items-center mb-4">
        <h2 class="text-base sm:text-lg font-extrabold text-[#051c45]">Notifications</h2>
        @if(auth()->user()->unreadNotifications->count() > 0)
            <form action="{{ route('notifications.markAllAsRead') }}" method="POST">
                @csrf
                <button type="submit" class="text-xs font-bold text-[#c30102] hover:underline cursor-pointer">Mark all read</button>
            </form>
        @endif
    </div>

    @if($notifications->isEmpty())
        <div class="bg-white rounded-2xl p-8 text-center shadow-2xs border border-slate-200/80">
            <div class="w-11 h-11 bg-slate-100 rounded-xl flex items-center justify-center mx-auto mb-3 text-slate-400">
                <i class="far fa-bell text-base"></i>
            </div>
            <h3 class="text-sm font-extrabold text-slate-800">No Notifications Yet</h3>
            <p class="text-xs text-slate-500 mt-1">Course updates and enrollment notices will appear here.</p>
        </div>
    @else
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-2xs divide-y divide-slate-100 overflow-hidden">
            @foreach($notifications as $notification)
                <div class="p-3.5 {{ $notification->read_at ? 'bg-white' : 'bg-amber-50/40' }} flex items-start gap-3 transition-colors">
                    <div class="shrink-0 w-9 h-9 rounded-xl flex items-center justify-center {{ $notification->read_at ? 'bg-slate-100 text-slate-500' : 'bg-[#051c45] text-white' }}">
                        @if(($notification->data['icon'] ?? '') == 'academic-cap')
                            <i class="fas fa-graduation-cap text-xs"></i>
                        @elseif(($notification->data['icon'] ?? '') == 'briefcase')
                            <i class="fas fa-briefcase text-xs"></i>
                        @else
                            <i class="fas fa-bullhorn text-xs"></i>
                        @endif
                    </div>
                    
                    <div class="flex-1 min-w-0">
                        <div class="flex justify-between items-start gap-2">
                            <h4 class="text-xs font-extrabold text-slate-900">{{ $notification->data['title'] ?? 'Notification' }}</h4>
                            <span class="text-[10px] text-slate-400 shrink-0">{{ $notification->created_at->diffForHumans() }}</span>
                        </div>
                        <p class="text-xs text-slate-600 mt-0.5">{{ $notification->data['message'] ?? '' }}</p>
                        
                        <div class="flex items-center gap-3 mt-2">
                            @if(!empty($notification->data['url']))
                                <a href="{{ $notification->data['url'] }}" class="text-[11px] font-bold text-[#c30102] hover:underline">View &rarr;</a>
                            @endif
                            
                            @if(is_null($notification->read_at))
                                <form action="{{ route('notifications.markAsRead', $notification->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="text-[11px] font-semibold text-slate-500 hover:text-slate-800 cursor-pointer">Mark read</button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-4">
            {{ $notifications->links() }}
        </div>
    @endif
</div>
@endsection
