@foreach($subcategories as $subcategory)
    <option value="{{ $subcategory->id }}" data-level="2">
        @for($i=0; $i <= $subcategory->level; $i++)
        &nbsp;&nbsp;
        @endfor
        {{ $subcategory->title }}
    </option> 
    @if($subcategory->childs && count($subcategory->childs))
        @include('Administrator.categories.sub_categories',['subcategories' => $subcategory->childs])
    @endif
@endforeach