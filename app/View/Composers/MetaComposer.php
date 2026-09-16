<?php

namespace App\View\Composers;

use App\Models\News;
use Illuminate\View\View;
use App\Models\NewsTranslate;
use App\Models\Product;
use App\Models\ProductsTranslate;
use App\Models\ProductCategoryTranslate;

class MetaComposer
{
    /**
     * Bind data to the view.
     */
    public function compose(View $view): void
    {
        if (request()->segment(2) && ! request()->segment(3)) {
            if (strlen(request()->segment(2)) > 60) {
                $metaTitle = 'password reset';
            } else {
                $metaTitle = str_replace('-', ' ', request()->segment(2));
            }
        } elseif (request()->segment(3) && ! request()->segment(4)) {

            switch (request()->segment(2)) {
                case "blog":
                    $blog = News::with(['trans'])
                        ->where(function ($q) {
                            $q->where('id', (int)request()->segment(3))
                                ->orWhere('slug', request()->segment(3));
                        })->firstOrFail();

                    $metaTitle = NewsTranslate::where('parent_id', $blog->id)->where('lang', locale())->first();
                    break;
                case "product":
                    $product = Product::with(['trans'])
                        ->where(function ($q) {
                            $q->where('id', (int)request()->segment(3))
                                ->orWhere('slug', request()->segment(3));
                        })->firstOrFail();

                    $metaTitle = ProductsTranslate::where('parent_id', $product->id)->where('lang', locale())->first();
                    break;
                default:
                    break;
            }
        } elseif (request()->segment(4)) {
            $metaTitle = ProductCategoryTranslate::where('slug', request()->segment(4))->where('lang', locale())->first();
            if (! $metaTitle) {
                $metaTitle = 'პროდუქტები';
            }
        } else {
            $metaTitle = 'index';
        }

        if (isset($metaTitle)) {
            $view->with('metaTitle', $metaTitle);
        }
    }
}
