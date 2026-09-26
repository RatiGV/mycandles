     @extends('layouts.client')
     @section('content')
         <main class="main-content">
             <div class="breadcrumb-area breadcrumb-height" data-bg-image="{{ $info->top_banner }}"></div>
             <div class="about-area section-space-top-95">
                 <div class="container">
                     <div class="row">
                         <div class="col-lg-12">
                             <div class="about-content">
                                 <h2 class="about-title"><span>{{ $terms->title }}</span></h2>
                                 <p class="about-desc">{!! $terms->description !!}</p>
                             </div>
                         </div>
                     </div>
                 </div>
             </div>
             @if ($benefits->isNotEmpty())
                 <!-- Begin Shipping Area -->
                 <div class="shipping-area section-space-y-axis-100">
                     <div class="container">
                         <div class="shipping-bg">
                             <div class="row shipping-wrap">
                                 @foreach ($benefits as $benefit)
                                     <div class="col-lg-4 col-md-6">
                                         <div class="shipping-item">
                                             <div class="shipping-img">
                                                 <img src="{{ $benefit->image }}" alt="{{ $benefit->alt }}">
                                             </div>
                                             <div class="shipping-content">
                                                 <h2 class="title">{{ $benefit->title }}</h2>
                                                 <p class="short-desc mb-0">{{ $benefit->short_description }}</p>
                                             </div>
                                         </div>
                                     </div>
                                 @endforeach
                             </div>
                         </div>
                     </div>
                 </div>
                 <!-- Shipping Area End Here -->
             @endif


         </main>
     @endsection
