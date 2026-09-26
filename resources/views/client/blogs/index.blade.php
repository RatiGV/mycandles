@extends('layouts.client')
@section('content')
    <main class="main-content">
        <div class="breadcrumb-area breadcrumb-height" data-bg-image="{{ $info->top_banner }}"></div>
        <div class="blog-area section-space-y-axis-100">
            <div class="container">
                <div class="row">
                    <div class="col-xl-3 col-lg-4 order-2 pt-5 pt-lg-0">
                        <div class="sidebar-area">
                            <div class="widgets-searchbox">
                                <form id="widgets-searchbox" action="{{ route('clientBlogs') }}" method="GET">
                                    <input class="input-field" type="text" value="{{ request()->get('search') }}"
                                        name="search" placeholder="{{ trans('Search') }}">
                                    <button class="widgets-searchbox-btn" type="submit">
                                        <i class="fa fa-search"></i>
                                    </button>
                                </form>
                            </div>
                            <div class="widgets-area">
                                <div class="widgets-item pt-0">
                                    <h2 class="widgets-title mb-4">{{ trans('Categories') }}</h2>
                                    <ul class="widgets-category">
                                        <li>
                                            <a href="{{ route('clientBlogs') }}">
                                                <i class="fa fa-chevron-right"></i>
                                                {{ trans('All') }} <span>({{ \App\Models\News::count() }})</span>
                                            </a>
                                        </li>
                                        @forelse($categories as $countCategories)
                                            <li>
                                                <a
                                                    href="{{ route('clientBlogs') }}?category={{ $countCategories->trans->title }}">
                                                    <i class="fa fa-chevron-right"></i>
                                                    {{ $countCategories->trans->title }}
                                                    <span>({{ $countCategories->blogs_count }})</span>
                                                </a>
                                            </li>
                                        @empty
                                        @endforelse
                                    </ul>
                                </div>
                                <div class="widgets-item">
                                    <h2 class="widgets-title mb-4">{{ trans('Popular Tags') }}</h2>
                                    <ul class="widgets-tag">
                                        @forelse($popularTags as $popularTag)
                                            <li>
                                                <a
                                                    href="{{ route('clientBlogs') }}?tag={{ $popularTag->title }}">{{ $popularTag->title }}</a>
                                            </li>
                                        @empty
                                        @endforelse
                                    </ul>
                                </div>
                                <div class="widgets-item">
                                    <h2 class="widgets-title mb-4">{{ trans('Recent Post') }}</h2>
                                    <div class="swiper-container widgets-list-slider">
                                        <div class="swiper-wrapper">
                                            @foreach ($recents as $recent)
                                                <div
                                                    class="swiper-slide @if ($loop->last) without-border @endif">
                                                    <div class="widgets-list-item">
                                                        <div class="widgets-list-img">
                                                            <a
                                                                href="{{ route('clientBlogsInner', $recent->slug ?? $recent->id . '-' . \Illuminate\Support\Str::slug($recent->trans->title, '-', false)) }}">
                                                                <img class="img-full" src="{{ $recent->image }}"
                                                                    alt="{{ $recent->trans->alt }}">
                                                            </a>
                                                        </div>
                                                        <div class="widgets-list-content">
                                                            <div class="widgets-meta">
                                                                <ul>
                                                                    <li class="date">
                                                                        {{ \Carbon\Carbon::parse($recent->created_at)->locale(locale())->translatedFormat('d F, Y') }}
                                                                    </li>
                                                                </ul>
                                                            </div>
                                                            <h2 class="title mb-0">
                                                                <a
                                                                    href="{{ route('clientBlogsInner', $recent->slug ?? $recent->id . '-' . \Illuminate\Support\Str::slug($recent->trans->title, '-', false)) }}">{{ $recent->trans->title }}</a>
                                                            </h2>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-9 col-lg-8 order-1">
                        <div class="blog-item-wrap row g-y-30">
                            @forelse ($blogs as $blog)
                                <div class="col-md-6">
                                    <div class="blog-item">
                                        <div class="blog-content">
                                            <div class="blog-meta">
                                                <ul>
                                                    <li class="date">
                                                        {{ \Carbon\Carbon::parse($blog->created_at)->locale(locale())->translatedFormat('d F, Y') }}
                                                    </li>
                                                </ul>
                                            </div>
                                            <h2 class="title">
                                                <a
                                                    href="{{ route('clientBlogsInner', $blog->slug ?? $blog->id . '-' . \Illuminate\Support\Str::slug($blog->trans->title, '-', false)) }}">{{ $blog->trans->title }}</a>
                                            </h2>
                                            <p class="short-desc mb-7">{{ shorten($blog->trans->short_description) }}</p>
                                        </div>
                                        <div class="blog-img img-hover-effect">
                                            <a
                                                href="{{ route('clientBlogsInner', $blog->slug ?? $blog->id . '-' . \Illuminate\Support\Str::slug($blog->trans->title, '-', false)) }}">
                                                <img class="img-full" src="{{ $blog->image }}"
                                                    alt="{{ $blog->trans->alt }}">
                                            </a>
                                            <div class="inner-btn-wrap">
                                                <a class="inner-btn"
                                                    href="{{ route('clientBlogsInner', $blog->slug ?? $blog->id . '-' . \Illuminate\Support\Str::slug($blog->trans->title, '-', false)) }}">
                                                    <i class="pe-7s-link"></i>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @empty
                            @endforelse

                        </div>
                        {{ $blogs->links() }}
                    </div>
                </div>
            </div>
        </div>
    </main>
@endsection
