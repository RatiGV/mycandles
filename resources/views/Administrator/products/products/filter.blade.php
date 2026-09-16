<div class="x_panel" style="padding: 20px;">
    <form class="form-inline" action="" id="filter-form">
        <div class="form-group">
            <label>@lang('admin.status')</label><br>
            <select name="status" class="form-control">
                <option value="1" {{ Request::get('status') == 1 ? 'selected' : '' }}>
                    @lang('admin.all')
                </option>
                <option value="2" {{ Request::get('status') == 2 ? 'selected' : '' }}>
                    @lang('admin.yes')
                </option>
                <option value="3" {{ Request::get('status') == 3 ? 'selected' : '' }}>
                    @lang('admin.no')
                </option>
            </select>
        </div>
        <div class="form-group">
            <label>@lang('admin.available')</label><br>
            <select name="available" class="form-control">
                <option value="1" {{ Request::get('available') == 1 ? 'selected' : '' }}>
                    @lang('admin.all')
                </option>
                <option value="2" {{ Request::get('available') == 2 ? 'selected' : '' }}>
                    @lang('admin.yes')
                </option>
                <option value="3" {{ Request::get('available') == 3 ? 'selected' : '' }}>
                    @lang('admin.no')
                </option>
            </select>
        </div>
        <div class="form-group">
            <label>@lang('admin.title')</label><br>
            <input type="text" name="title" class="form-control" value="{{ Request::get('title') }}">
        </div>
        <div class="form-group not-need">
            <label class="control-label col-md-3 col-sm-3 col-xs-12">
                @lang('admin.parent_category')
            </label>
            <div class="col-md-7 col-sm-7 col-xs-12">
                <select name="field_id" class="chosen-select-deselect form-control">
                    <option value=""></option>
                    @forelse($categories as $category)
                        <option value="{{ $category->id }}">{{ $category->title }}</option>
                    @empty
                    @endforelse
                </select>
            </div>
        </div>
        <div class="form-group">
            <label>₾ @lang('admin.from')</label><br>
            <input type="number" name="from" class="form-control" value="{{ Request::get('from') }}" min="0."
                step="0.01">
        </div>
        <div class="form-group">
            <label>₾ @lang('admin.to')</label><br>
            <input type="number" name="to" class="form-control" value="{{ Request::get('to') }}" min="0.01"
                step="0.01">
        </div>
        <div class="form-group">
            <label></label><br>
            <button type="submit" class="btn btn-warning">
                <i class="fas fa-search"></i>
                @lang('admin.filter')
            </button>
        </div>
    </form>
</div>
