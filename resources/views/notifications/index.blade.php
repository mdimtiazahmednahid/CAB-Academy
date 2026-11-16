@extends('layouts.student')

@section('header_title', 'Notifications')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="flex justify-between items-center mb-4">
        <h2 class="text-base sm:text-lg font-extrabold text-[#063b96]">Notifications</h2>
        @if(auth()->user()->unreadNotifications->count() > 0)
            <form action="{{ route('notifications.markAllAsRead') }}" method="POST">
                @csrf
                <button type="submit" class="text-xs font-bold text-[#c4040a] hover:underline cursor-pointer">Mark all read</button>
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
                @php
                    $nStatus = $notification->data['status'] ?? null;
                    $nStatusLabel = $notification->data['status_label'] ?? null;
                    $nIcon = $notification->data['icon'] ?? '';
                    $iconBoxClass = match ($nStatus) {
                        'verified' => 'bg-emerald-600 text-white',
                        'processing' => 'bg-sky-600 text-white',
                        'pending' => 'bg-amber-500 text-white',
                        'rejected' => 'bg-rose-600 text-white',
                        default => $notification->read_at ? 'bg-slate-100 text-slate-500' : 'bg-[#063b96] text-white',
                    };
                    $statusBadgeClass = match ($nStatus) {
                        'verified' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                        'processing' => 'bg-sky-50 text-sky-700 border-sky-200',
                        'pending' => 'bg-amber-50 text-amber-700 border-amber-200',
                        'rejected' => 'bg-rose-50 text-rose-700 border-rose-200',
                        default => 'bg-slate-100 text-slate-600 border-slate-200',
                    };
                @endphp
                <div class="p-3.5 {{ $notification->read_at ? 'bg-white' : 'bg-amber-50/40' }} flex items-start gap-3 transition-colors">
                    <div class="shrink-0 w-9 h-9 rounded-xl flex items-center justify-center {{ $iconBoxClass }}">
                        @if($nIcon == 'academic-cap')
                            <i class="fas fa-graduation-cap text-xs"></i>
                        @elseif($nIcon == 'briefcase')
                            <i class="fas fa-briefcase text-xs"></i>
                        @elseif($nIcon == 'check-circle')
                            <i class="fas fa-check-circle text-xs"></i>
                        @elseif($nIcon == 'clock')
                            <i class="fas fa-clock text-xs"></i>
                        @elseif($nIcon == 'times-circle')
                            <i class="fas fa-times-circle text-xs"></i>
                        @else
                            <i class="fas fa-bullhorn text-xs"></i>
                        @endif
                    </div>
                    
                    <div class="flex-1 min-w-0">
                        <div class="flex justify-between items-start gap-2">
                            <div class="flex flex-wrap items-center gap-1.5 min-w-0">
                                <h4 class="text-xs font-extrabold text-slate-900">{{ $notification->data['title'] ?? 'Notification' }}</h4>
                                @if($nStatusLabel)
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-extrabold border {{ $statusBadgeClass }}">
                                        @if($nStatus === 'processing')
                                            <span class="w-1.5 h-1.5 rounded-full bg-sky-500 animate-pulse"></span>
                                        @endif
                                        {{ $nStatusLabel }}
                                    </span>
                                @endif
                            </div>
                            <span class="text-[10px] text-slate-400 shrink-0">{{ $notification->created_at->diffForHumans() }}</span>
                        </div>
                        <p class="text-xs text-slate-600 mt-0.5">{{ $notification->data['message'] ?? '' }}</p>
                        
                        <div class="flex items-center gap-3 mt-2">
                            @if(!empty($notification->data['url']) && $notification->data['url'] !== '#')
                                <a href="{{ $notification->data['url'] }}" class="text-[11px] font-bold text-[#c4040a] hover:underline">View &rarr;</a>
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
