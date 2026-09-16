@php
    $total_price = 0;
    $phones = explode(',',$contact_info->phone);
    $emails = explode(',',$contact_info->email);
@endphp
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
        <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
        <title>{{ $order->code }}</title>
        <style>
            * {
                margin: 0;
                padding: 0;
                text-decoration: none;
                border: none;
                outline: none;
                list-style-type: none;
                -webkit-box-sizing: border-box;
                box-sizing: border-box;
                font-style: normal;
                font-weight: normal;
                font-size: 100%;
                -webkit-appearance: none;
                -moz-appearance: none;
                border: 0;
                resize: none;
                -webkit-transition: 0.3s;
                -o-transition: 0.3s;
                transition: 0.3s; 
                font-family: DejaVu Sans, sans-serif; 
            }
           
            main {
                width: 820px;
                margin: 0 auto;
                min-height: 85px;
                -webkit-box-sizing: border-box;
                box-sizing: border-box; }

            .header {
                width: 720px;
                padding: 15px 40px 0px 40px;
                background-color: #FFF6F3;
                height: 80px;
                display:block;
                position:relative;
            }

            .header-img-text {
                display:block;
                float:left;
             }

             .header-img-text img{
                 margin-top:10px;
                 width:36px;
                 height:31px;
                 float:left;
                 display:inline-block;
             }

            .heder-text {
            display: block;
            /* float:right; */
            /* clear:both; */
            /* position:absolute; */
            /* right:0px; */
            /* margin-top:50px; */
            text-align: right; }

            .hdr-text {
                padding-left:49px;
            .hdr-text p {
                font-size: 12px;
                font-family: sem;
                font-family: DejaVu Sans, sans-serif;
                color: #EF4E4F; }
            .hdr-text span {
                font-size: 10px;
                font-family: med;
                font-family: DejaVu Sans, sans-serif;
                color: #422F70;
                margin-top: 5px;
                display: block; }

            

            .heder-text h1 {
                font-size: 12px;
                font-family: 'sem';
                font-family: DejaVu Sans, sans-serif;
                color: #422F70;
                line-height: 12px; }
            .heder-text p {
                font-size: 12px;
                font-family: 'med';
                font-family: DejaVu Sans, sans-serif;
                color: #EF4E4F;
                line-height: 19px;
                margin-top: 10px; }

            .content1 {
                /* height:200px; */
                display:inline-block;
                padding: 20px 40px;
                padding-bottom:0px;
             }
            .content1 h1 {
                margin-top:15px;
                display:inline-block;
                padding-bottom: 8px;
                font-size: 12px;
                font-family: sem;
                font-family: DejaVu Sans, sans-serif;
                color: #EF4E4F;
                border-bottom: solid 1px #EF4E4F;
                /* margin-bottom: 18px; */
             }

                .ddsh{
                    margin-top:15px;
                }
            .vis-table {
                width: 600px;
             }
            .vis-table th {
                font-size: 10px;
                font-family: 'reg';
                font-family: DejaVu Sans, sans-serif;
                color: #422F70;
                opacity: 0.7;
                /* min-width: 1147px; */
                /* width: 247px; */
                text-align: left;
                padding-bottom: 10px; }
            .vis-table td {
                font-size: 12px;
                font-family: med;
                font-family: DejaVu Sans, sans-serif;
                color: #422F70;
                text-align: left;
                white-space: nowrap;
                /* padding-right: 100px;  */
            }

            .content2 {
                width: 100%;
                background-color: rgba(66, 47, 112, 0.062);
                padding: 20px 40px; }

            .dasaxleba-table {
                width: 100%; }
            .dasaxleba-table th {
                font-size: 10px;
                font-family: 'reg';
                font-family: DejaVu Sans, sans-serif;
                color: #422F70;
                opacity: 0.7;
                text-align: center; 
                padding-bottom: 13px;
                /* min-width: 130px; */
             }
            .dasaxleba-table  {
                text-align: center; 
            }
            .dasaxleba-table td {
                text-align: center; 
                font-size: 12px;
                font-family: sem;
                font-family: DejaVu Sans, sans-serif;
                color: #422F70;
                padding-bottom: 14px; }
            .dasaxleba-table tr td:first-of-type {
                text-align: left; 
            }
            .dasaxleba-table th:first-of-type {
                text-align: left; 
            }

            .content3 {
                padding: 20px 40px; }
            .content3 .vis-table td {
                font-size: 15px; }

            .content4 {
                display:inline-block;
                width: 100%;
                background-color: rgba(66, 47, 112, 0.062);
                padding: 20px 40px; }
            .content4 h1 {
                /* width: -webkit-fit-content;
                width: -moz-fit-content;
                width: fit-content; */
                padding-bottom: 8px;
                font-size: 12px;
                font-family: sem;
                font-family: DejaVu Sans, sans-serif;
                color: #EF4E4F;
                border-bottom: solid 1px #EF4E4F;
                display:inline-block;
                margin-bottom: 18px; }

            .container5 {
                padding: 30px 40px;
                width: 100%;
                -webkit-box-pack: justify;
                -ms-flex-pack: justify;
                justify-content: space-between;
                -webkit-box-align: center;
                -ms-flex-align: center;
                align-items: center; }

            .left5 > p {
                font-size: 12px;
                font-family: reg;
                font-family: DejaVu Sans, sans-serif;
                color: #422F70;
                max-width: 187px;
                /* display: inline-block; */
                margin-bottom: 15px; }

            .nomrebi {
              display:inline-block;
            }
            .nomrebi P {
                border: solid 1px #EBEBEB;
                border-radius: 19px;
                font-size: 13px;
                font-family: sem;
                font-family: DejaVu Sans, sans-serif;
                color: #EF4E4F;
                margin-top:10px;
                display: inline-block;
                padding: 11px 16px; }

            .nomrebi P:last-of-type {
                margin-left:10px;
            }

            .left5{
                clear:both;
                float:left;
            }

            .right5 {
                border: solid 1px #EBEBEB;
                border-radius: 19px;
                font-size: 11px;
                /* height: fit-content; */
                width: 163px;
                font-family: sem;
                font-family: DejaVu Sans, sans-serif;
                color: #EF4E4F;
                background-color: #FFE8E0;
                padding: 12px 12px;
                border-radius: 5px;
                text-align: center;
                float:right;
                clear:both;
                display:inline-block;
                margin-top:20px;
                 }

            .foo {
                font-size: 13px;
                font-family: sem;
                font-family: DejaVu Sans, sans-serif;
                color: #422F70;
                text-align: center;
                width: 100%;
                display: inline-block;
                margin-top: 160px;
                clear:both;
                 }

        </style>
    </head>
    <body>
        <header class="header">
            <div class="header-img-text">
                <!--<img src="{{ '../public_html/uploads/invoice_logo.png' }}">-->
                <img src="{{ public_path('uploads/invoice_logo.png') }}">
                <div class="hdr-text">
                    <p>{{ $contact_info->title }}</p>
                    <span>@lang('invoice.sk'): {{ $contact_info->sk }}</span>
                </div>
            </div>
            <div class="heder-text">
                <h1>{{ $contact_info->address }}</h1>
                <p>
                    @forelse($phones as $key => $phone)
                         {{ $phone }} 
                         {{ $key % 2 == 0 ? '/' : '' }}
                    @empty
                    @endforelse
                    @forelse($emails as $key => $email)
                        {{ $email }}
                    @empty
                    @endforelse
                </p>
            </div>
        </header>
        <main>
            <div class="content1">
                <h1>@lang('invoice.who')</h1>
                <table class="vis-table ddsh">
                    <tbody>
                        <tr>
                            <th>@lang('invoice.first_name')</th>
                            <th>@lang('invoice.address')</th>
                            <th>@lang('invoice.phone')</th>
                            <th>@lang('invoice.personal_id')</th>
                        </tr>
                        <tr> 
                            <td>{{ $user->first_name }}</td>
                            <td>{{ $order->address }}</td>
                            <td>{{ $user->phone }}</td>
                            <td>{{ $user->personal_id }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="content2">
                <table class="dasaxleba-table">
                    <tbody>
                        <tr>
                            <th>@lang('invoice.title')</th>
                            <th>@lang('invoice.unit_price')</th>
                            <th>@lang('invoice.qty')</th>
                            <th>@lang('invoice.total')</th>
                        </tr>
                        @foreach($order->products as $prod)
                            @php 
                                $product = \App\Models\Product::getItemInfo($prod->product_id, App::getLocale(), $status_on = false);
                                $total_price += $product->price * $prod->qty;
                            @endphp
                            <tr> 
                                <td>{{ $product->title }}</td>
                                <td>{{ $product->price }}</td>
                                <td>{{ $prod->qty }}</td>
                                <td>{{ $prod->qty * $product->price }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="content3">
                <table class="vis-table">
                    <tbody>
                        <tr>
                            <th>@lang('invoice.total')</th>
                            <th>@lang('invoice.delivery')</th>
                            <th>@lang('invoice.need_pay')</th>
                        </tr>
                        <tr> 
                            <td>{{ $total_price }}</td>
                            <td>{{ $order->delivery_price }}</td>
                            <td>{{ $total_price + $order->delivery_price }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="content4">
                <h1>@lang('invoice.bank_account')</h1>
                <table class="vis-table">
                    <tbody>
                        <tr>
                            <th>@lang('invoice.bog'): </th>
                            <th>@lang('invoice.tbc'):</th>
                        </tr>
                        <tr> 
                            <td>{{ $contact_info->bog }}</td>
                            <td>{{ $contact_info->tbc }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="container5">
                <div class="left5">
                    <p>@lang('invoice.any_quests') </p>
                    <div class="nomrebi">
                        @forelse($phones as $key => $phone)
                            <p>{{ $phone }} </p>
                        @empty
                        @endforelse
                    </div>
                </div>
                <div class="right5">@lang('invoice.vat')</div>
            </div>
            <br>
            <div class="foo">@lang('invoice.thnks')</div>
        </main>
    </body>
</html>