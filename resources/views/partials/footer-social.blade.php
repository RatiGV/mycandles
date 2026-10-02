@php
    $socials = [
        ['title' => 'Facebook', 'icon' => 'fa-facebook', 'url' => $info->facebook],
        ['title' => 'Instagram', 'icon' => 'fa-instagram', 'url' => $info->instagram],
        ['title' => 'Youtube', 'icon' => 'fa-youtube-play', 'url' => $info->youtube],
        ['title' => 'WhatsApp', 'icon' => 'fa-whatsapp', 'url' => $info->whatsapp_url],
    ];
@endphp
<ul>
    @foreach ($socials as $social)
        @if (trim((string) $social['url']) !== '')
            <li>
                <a href="{{ $social['url'] }}" target="_blank" rel="noopener" data-tippy="{{ $social['title'] }}"
                    data-tippy-inertia="true" data-tippy-animation="shift-away"
                    data-tippy-delay="50" data-tippy-arrow="true"
                    data-tippy-theme="sharpborder">
                    <i class="fa {{ $social['icon'] }}"></i>
                </a>
            </li>
        @endif
    @endforeach
</ul>
