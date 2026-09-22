<?php

namespace App\Http\Controllers\Client;

use App\Models\News;
use App\Models\Product;
use App\Http\Controllers\Controller;
use Illuminate\Http\Response;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

class SitemapController extends Controller
{
    public function index(): Response
    {
        $locales = array_keys(LaravelLocalization::getSupportedLocales());

        $staticRoutes = [
            'clientHome',
            'clientProducts',
            'clientBlogs',
            'clientAbout',
            'clientFaq',
            'clientContact',
            'clientTerms',
        ];

        $urls = [];

        foreach ($locales as $locale) {
            foreach ($staticRoutes as $routeName) {
                $urls[] = [
                    'loc' => LaravelLocalization::getLocalizedURL($locale, route($routeName, [], false)),
                    'changefreq' => $routeName === 'clientHome' ? 'daily' : 'weekly',
                    'priority' => $routeName === 'clientHome' ? '1.0' : '0.7',
                ];
            }

            $products = Product::where('status', 1)->get(['id', 'slug', 'updated_at']);

            foreach ($products as $product) {
                $urls[] = [
                    'loc' => LaravelLocalization::getLocalizedURL($locale, route('clientProductsInner', $product->slug ?: $product->id, false)),
                    'lastmod' => optional($product->updated_at)->toAtomString(),
                    'changefreq' => 'weekly',
                    'priority' => '0.8',
                ];
            }

            $blogs = News::where('status', 1)->get(['id', 'slug', 'updated_at']);

            foreach ($blogs as $blog) {
                $urls[] = [
                    'loc' => LaravelLocalization::getLocalizedURL($locale, route('clientBlogsInner', $blog->slug ?: $blog->id, false)),
                    'lastmod' => optional($blog->updated_at)->toAtomString(),
                    'changefreq' => 'monthly',
                    'priority' => '0.5',
                ];
            }
        }

        $xml = view('client.sitemap', compact('urls'))->render();

        return response($xml, 200)->header('Content-Type', 'text/xml');
    }
}
