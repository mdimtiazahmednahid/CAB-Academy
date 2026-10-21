@php
    $logo = \App\Models\Setting::getVal('site_logo') ?: asset('images/logo.png');
@endphp

<img src="{{ $logo }}" alt="{{ \App\Models\Setting::getVal('site_name', 'CAB Academy') }}" {{ $attributes }}>
