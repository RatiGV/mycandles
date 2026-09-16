 @if (isset($title->meta_description))
     {{ $title->meta_description }}
 @elseif(isset($title->short_description))
     {{ $title->short_description }}
 @elseif(isset($title))
     {{ $title }}
 @else
     {{ $title = 'Tetri Tskaro Gardensi' }}
 @endif
