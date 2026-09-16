<html>
    <head>
        <meta charset="utf-8"/>
        <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
        <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
        <link href="/Administrator/build/css/login.css" rel="stylesheet">
        <link href="{{ $information ? $information->favicon : '' }}" rel="icon" type="image/x-icon" >
        <title>{{ $information ? $information->title : '' }}</title>
    </head>
    <body>
        <div class="left">
            <img class="bckimg" src="{{ $information ? $information->login_bg : '' }}"/>
            <div class="bck" style="background-color: {{ $configuration->admin_color }}"></div>
            <div class="ss">
                <img class="logo" src="{{ $information ? $information->logo_for_admin : '' }}"/>
                <h2>{{ $information->slogan }}</h2>
                <a href="" style="color: white">{{ $information->title }}</a>
            </div>
        </div>
        <div class="right">
            <form class="block" method="post" action="{{ route('LoginAdmin') }}">
                @csrf
                <input type="mail" 
                       name="email" 
                       class="mail" 
                       value="{{ old('email') }}" 
                       placeholder="@lang('admin.email')"
                />
                <input name="password"
                       type="password"
                       class="pass"
                       placeholder="@lang('admin.password')"
                />
                <button type="submit" style="background-color: {{ $configuration->admin_color }}">@lang('admin.login')</button>
            </form>
        </div>
        <a href="https://smartweb.ge/" class="smart" target="_blank">Powered by Smartweb</a>
    </body>
</html>