<form action="{{-- route('SearchProducts') --}}" 
      method="get" 
      class="form-inline" 
      id="statement-filter-form"
>
    <div class="form-group">
        <select name="status"  class="form-control">
            <option value="">სტატუსი</option>
            <option value="">ყველა</option>
            <option value="2"
                    {{ Request::has('status') && Request::get('status') == 2 ? 'selected' : '' }}
            >
                ჩართული
            </option>
            <option value="1" 
                    {{ Request::has('status') && Request::get('status') == 1 ? 'selected' : '' }}
            >
                გამორთული
            </option>
        </select>
    </div>
    <div class="form-group">
        <select name="popular"  class="form-control">
            <option value="">პოპულარული</option>
            <option value="">ყველა</option>
            <option value="2"
                    {{ Request::has('status') && Request::get('popular') == 2 ? 'selected' : '' }}
            >
                კი
            </option>
            <option value="1" 
                    {{ Request::has('status') && Request::get('popular') == 1 ? 'selected' : '' }}
            >
                არა
            </option>
        </select>
    </div>
    {{--
    <div class="form-group">
        <select name="category_id"  class="form-control">
            <option value="">კატეგორია</option>
            <option value="">ყველა</option>
            @forelse($parentCategories as $cat)
                <option value="{{ $cat->id }}"
                        {{ Request::has('category_id') && Request::get('category_id') == $cat->id ? 'selected' : '' }}
                >
                    {{ $cat->title }}
                </option>
            @empty
            @endforelse
        </select>
    </div>
    --}}
    <div class="form-group">
        <input type="text" 
               name="title" 
               value="{{ Request::get('title') ? Request::get('title') : '' }}"
               placeholder="დასახელება" 
               class="form-control"
        >
    </div>
    <div class="form-group text-right">
        <a href="{{ route('Products') }}" 
           id="statement-cancel-filter-btn" 
           class="btn btn-warning pull-right" 
           style="margin-top: -6px;"
        >
            <i class="fas fa-times"></i>
        </a>
        <button type="submit" 
                id="statement-filter-btn" 
                class="btn btn-primary pull-right"
                style="margin-top: -6px;"
        >
            <i class="fas fa-search"></i> 
        </button>
    </div>
</form>