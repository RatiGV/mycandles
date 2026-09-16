{{--
    თვალთვალის სკრიპტები. ბაზაში ინახება მხოლოდ იდენტიფიკატორი,
    სრულ კოდს კი ეს შაბლონი აგენერირებს.
--}}
@php
    $pixel_id = data_get($info ?? null, 'pixel');
    $analytics_id = data_get($info ?? null, 'analytics');
@endphp
@if ($pixel_id)
    <script>
        !function(f,b,e,v,n,t,s)
        {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
        n.callMethod.apply(n,arguments):n.queue.push(arguments)};
        if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
        n.queue=[];t=b.createElement(e);t.async=!0;
        t.src=v;s=b.getElementsByTagName(e)[0];
        s.parentNode.insertBefore(t,s)}(window, document,'script',
        'https://connect.facebook.net/en_US/fbevents.js');
        fbq('init', '{{ $pixel_id }}');
        fbq('track', 'PageView');
    </script>
    <noscript>
        <img height="1" width="1" style="display:none"
            src="https://www.facebook.com/tr?id={{ $pixel_id }}&ev=PageView&noscript=1" alt="" />
    </noscript>
@endif
@if ($analytics_id)
    <script async src="https://www.googletagmanager.com/gtag/js?id={{ $analytics_id }}"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag() {
            dataLayer.push(arguments);
        }
        gtag('js', new Date());
        gtag('config', '{{ $analytics_id }}');
    </script>
@endif
