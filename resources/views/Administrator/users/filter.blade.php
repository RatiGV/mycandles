<div class="x_panel" style="padding: 20px;">
    <form class="form-inline" action="" id="filter-form">
        <div class="form-group">
            <label>@lang('admin.first_name')</label><br>
            <input type="text"
                   name="name"
                   class="form-control"
                   value="{{ Request::get('first_name') }}"
            >
        </div>
        <div class="form-group">
            <label>@lang('admin.last_name')</label><br>
            <input type="text"
                   name="surname"
                   class="form-control"
                   value="{{ Request::get('last_name') }}"
            >
        </div>
        <div class="form-group">
            <label>@lang('admin.email')</label><br>
            <input type="text"
                   name="email"
                   class="form-control"
                   value="{{ Request::get('email') }}"
            >
        </div>
        <div class="form-group">
            <label>@lang('admin.from')</label><br>
            <input type="date"
                   name="from"
                   value="{{ Request::get('from') }}"
                   class="form-control"
            >
        </div>
        <div class="form-group">
            <label>@lang('admin.to')</label><br>
            <input type="date"
                   name="to"
                   value="{{ Request::get('to') }}"
                   class="form-control"
                   value=""
            >
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
