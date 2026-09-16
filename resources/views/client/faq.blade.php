     @extends('layouts.client')
     @section('content')
         <main class="main-content">
             <div class="breadcrumb-area breadcrumb-height"
                 data-bg-image="{{ $info->top_banner }}">
                 <div class="container h-100">
                     <div class="row h-100">
                         <div class="col-lg-12">
                             <div class="breadcrumb-item">
                                 <h2 class="breadcrumb-heading">FAQ</h2>
                                 <ul>
                                     <li>
                                         <a href="{{ route('clientHome') }}">Home</a>
                                     </li>
                                     <li>FAQ</li>
                                 </ul>
                             </div>
                         </div>
                     </div>
                 </div>
             </div>
             <div class="faq-area section-space-y-axis-100">
                 <div class="container">
                     <div class="row">
                         <div class="col-lg-12">
                             <div class="frequently-area">
                                 <h2 class="heading mb-0">{{ trans('Frequently Asked Questions') }}</h2>
                                 <div class="row">
                                     <div class="col-md-12">
                                         <div class="row">
                                             <div class="col-lg-12">
                                                 <div class="frequently-item">
                                                     <ul>
                                                         @forelse($faqs as $faq)
                                                             <li class="has-sub active">
                                                                 <a href="javascript:void(0)">{{ $faq->question }}
                                                                     <i class="pe-7s-angle-down"></i>
                                                                 </a>
                                                                 <ul class="frequently-body">
                                                                     <li>
                                                                         {!! $faq->answer !!}
                                                                     </li>
                                                                 </ul>
                                                             </li>
                                                         @empty
                                                         @endforelse
                                                     </ul>
                                                 </div>
                                             </div>
                                         </div>
                                     </div>
                                 </div>
                             </div>
                         </div>
                     </div>
                 </div>
             </div>
         </main>
     @endsection
