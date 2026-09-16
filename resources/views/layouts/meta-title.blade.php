@if (isset($title->meta_title))
    {{ $title->meta_title }}
@elseif(isset($title->title))
    {{ $title->title }}
@elseif(isset($title))
    {{ $title }}
@else
    {{ $title = 'Tetri Tskaro' }}
@endif
