<div class="x_panel" style="padding: 20px;">
    <form class="form-inline" action="" id="filter-form">
        <div class="form-group">
            <label>@lang('admin.payed')</label><br>
            <select name="pay_status"  class="form-control">
                <option value="1" {{ Request::get('pay_status') == 1 ? 'selected' : '' }}>
                        @lang('admin.all')
                </option>
                <option value="2" {{ Request::get('pay_status') == 2 ? 'selected' : '' }}>
                    @lang('admin.yes')
                </option>
                <option value="3" {{ Request::get('pay_status') == 3 ? 'selected' : '' }}>
                    @lang('admin.no')
                </option>
            </select>
        </div>
        <div class="form-group">
            <label>@lang('admin.done')</label><br>
            <select name="done"  class="form-control">
                <option value="1" {{ Request::get('done') == 1 ? 'selected' : '' }}>
                        @lang('admin.all')
                </option>
                <option value="2" {{ Request::get('done') == 2 ? 'selected' : '' }}>
                    @lang('admin.yes')
                </option>
                <option value="3" {{ Request::get('done') == 3 ? 'selected' : '' }}>
                    @lang('admin.no')
                </option>
            </select>
        </div>
        <div class="form-group">
            <label>@lang('admin.code')</label><br>
            <input type="text" 
                   name="code" 
                   class="form-control" 
                   value="{{ Request::get('code') }}"
            >
        </div>
        <div class="form-group">
            <label>@lang('admin.user')</label><br>
            <select name="user_id"  class="form-control">
                <option value="">@lang('admin.all')</option>
                @forelse($users as $user)
                    <option value="{{ $user->id }}" {{ Request::has('user_id') && Request::get('user_id') == $user->id ? 'selected' : '' }}>
                        {{ $user->first_name . ' ' . $user->last_name }}
                    </option>
                @empty
                @endforelse
            </select>
        </div>
        <div class="form-group">
            <label>₾ @lang('admin.from')</label><br>
            <input type="number" 
                   name="total_from" 
                   class="form-control" 
                   value="{{ Request::get('total_from') }}"
                   min="0."
                   step="0.01"
            >
        </div>
        <div class="form-group">
            <label>₾ @lang('admin.to')</label><br>
            <input type="number" 
                   name="total_to" 
                   class="form-control" 
                   value="{{ Request::get('total_to') }}"
                   min="0.01"
                   step="0.01"
            >
        </div>
        <div class="form-group">
            <label>@lang('admin.pay_type')</label><br>
            <select name="payment_type"  class="form-control">
                <option value="">@lang('admin.all')</option>
                <option value="1"
                        {{ Request::has('payment_type') && Request::get('payment_type') == 1 ? 'selected' : '' }}
                >
                    @lang('admin.online_pay')
                </option>
                <option value="2" 
                        {{ Request::has('payment_type') && Request::get('payment_type') == 2 ? 'selected' : '' }}
                >
                    @lang('admin.bank_transfer')
                </option>
            </select>
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