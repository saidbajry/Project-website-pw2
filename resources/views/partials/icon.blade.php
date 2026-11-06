@php
$paths = [
 'home'=>'<path d="M3 11l9-8 9 8"/><path d="M5 10v10h14V10"/>',
 'car'=>'<path d="M3 16v-4l2-5h14l2 5v4"/><path d="M3 16h18"/><circle cx="7.5" cy="16.5" r="1.8"/><circle cx="16.5" cy="16.5" r="1.8"/>',
 'key'=>'<circle cx="8" cy="15" r="4"/><path d="M11 12l9-9M16 7l3 3"/>',
 'calendar'=>'<rect x="3" y="5" width="18" height="16" rx="2"/><path d="M3 10h18M8 3v4M16 3v4"/>',
 'clock'=>'<circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/>',
 'list'=>'<path d="M8 6h13M8 12h13M8 18h13M3 6h.01M3 12h.01M3 18h.01"/>',
 'users'=>'<circle cx="9" cy="8" r="3.5"/><path d="M2 20c0-4 3-6 7-6s7 2 7 6"/><path d="M17 4.5a3.5 3.5 0 0 1 0 7M22 20c0-3-1.5-5-4-5.8"/>',
 'wrench'=>'<path d="M14 6a4 4 0 0 0 5 5l-9 9a2.1 2.1 0 0 1-3-3z"/><path d="M14 6l3-3 4 4-3 3"/>',
 'box'=>'<path d="M3 7l9-4 9 4v10l-9 4-9-4z"/><path d="M3 7l9 4 9-4M12 11v10"/>',
 'layout'=>'<rect x="3" y="3" width="18" height="18" rx="2"/><path d="M3 9h18M9 21V9"/>',
 'chart'=>'<path d="M4 20V10M10 20V4M16 20v-8M22 20H2"/>',
 'file'=>'<path d="M6 3h8l4 4v14H6z"/><path d="M14 3v4h4M9 13h6M9 17h6"/>',
 'lock'=>'<rect x="4" y="10" width="16" height="11" rx="2"/><path d="M8 10V7a4 4 0 0 1 8 0v3"/>',
 'logout'=>'<path d="M9 4H5v16h4M16 8l4 4-4 4M20 12H9"/>',
 'menu'=>'<path d="M4 6h16M4 12h16M4 18h16"/>',
 'search'=>'<circle cx="11" cy="11" r="7"/><path d="M20 20l-4-4"/>',
 'eye'=>'<path d="M2 12s4-7 10-7 10 7 10 7-4 7-10 7S2 12 2 12z"/><circle cx="12" cy="12" r="3"/>',
 'shield'=>'<path d="M12 3l8 3v6c0 5-3.5 8-8 9-4.5-1-8-4-8-9V6z"/><path d="M9 12l2 2 4-4"/>',
 'wallet'=>'<rect x="3" y="6" width="18" height="14" rx="2"/><path d="M3 10h18M16 15h2"/>',
];
@endphp
<svg class="ic" viewBox="0 0 24 24" aria-hidden="true">{!! $paths[$n] ?? '' !!}</svg>
