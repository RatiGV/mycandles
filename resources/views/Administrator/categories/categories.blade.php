<select name="parent_id" class="form-control col-md-7 col-xs-12" id="category-select">
    <option value="">@lang('admin.main_category')</option>
    @foreach($parentCategories as $category)
        <option value="{{ $category->id }}" data-level="1">{{ $category->title }}</option>
        @if(count($category->childs))
            @include('Administrator.categories.sub_categories',['subcategories' => $category->childs])
        @endif 
    @endforeach
</select>
