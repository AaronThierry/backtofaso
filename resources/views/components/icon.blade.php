@props(['name', 'size' => 20])
@php
$paths = [
    'home' => '<path d="M3 10.5 12 3l9 7.5V20a1 1 0 0 1-1 1h-5v-6H9v6H4a1 1 0 0 1-1-1z"/>',
    'folder' => '<path d="M3 7a2 2 0 0 1 2-2h4l2 2h8a2 2 0 0 1 2 2v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>',
    'chat' => '<path d="M4 5h16v11H9l-5 4z"/>',
    'users' => '<circle cx="9" cy="8" r="3.5"/><path d="M2.5 20c.8-3.5 3.4-5.5 6.5-5.5s5.7 2 6.5 5.5M16 4.6a3.5 3.5 0 0 1 0 6.8M18 14.8c1.8.8 3 2.6 3.5 5.2"/>',
    'user' => '<circle cx="12" cy="8" r="4"/><path d="M4 21c1-4 4.2-6 8-6s7 2 8 6"/>',
    'bell' => '<path d="M6 8a6 6 0 0 1 12 0c0 7 3 8 3 8H3s3-1 3-8"/><path d="M10.3 20a2 2 0 0 0 3.4 0"/>',
    'back' => '<path d="M15 6l-6 6 6 6"/>',
    'chevron' => '<path d="M9 6l6 6-6 6"/>',
    'down' => '<path d="M6 9l6 6 6-6"/>',
    'arrow' => '<path d="M5 12h14M13 6l6 6-6 6"/>',
    'check' => '<path d="M5 12l5 5 9-10"/>',
    'plus' => '<path d="M12 5v14M5 12h14"/>',
    'search' => '<circle cx="11" cy="11" r="7"/><path d="M20 20l-4-4"/>',
    'upload' => '<path d="M12 16V4M7 9l5-5 5 5M5 20h14"/>',
    'file' => '<path d="M6 3h8l4 4v14H6z"/><path d="M14 3v4h4"/>',
    'send' => '<path d="M4 12l16-8-6 16-3-7z"/>',
    'pin' => '<path d="M12 17v5M9 3h6l-1 6 4 4H6l4-4z"/>',
    'lock' => '<rect x="5" y="11" width="14" height="10" rx="2"/><path d="M8 11V8a4 4 0 0 1 8 0v3"/>',
    'shield' => '<path d="M12 3l7 3v5c0 4.5-3 8-7 10-4-2-7-5.5-7-10V6z"/><path d="M9 12l2 2 4-4"/>',
    'camera' => '<path d="M4 8h3l2-3h6l2 3h3v11H4z"/><circle cx="12" cy="13" r="3.5"/>',
    'phone' => '<rect x="6" y="2.5" width="12" height="19" rx="2.5"/><path d="M10.5 18.5h3"/>',
    'logout' => '<path d="M15 4h4v16h-4M10 8l-4 4 4 4M6 12h10"/>',
    'play' => '<path d="M8 5.5v13l11-6.5z" fill="currentColor" stroke="none"/>',
    'bookmark' => '<path d="M6 3h12v18l-6-4-6 4z"/>',
];
@endphp
<svg width="{{ $size }}" height="{{ $size }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" {{ $attributes->merge(['stroke-width' => '1.8']) }}>{!! $paths[$name] ?? '' !!}</svg>
