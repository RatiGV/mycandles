<?php

namespace App\Http\Controllers\Client;

use App\Models\Benefit;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Models\ProductCategory;
use App\Models\ProductsTranslate;
use App\Http\Controllers\Controller;
use App\Models\ProductCategoryTranslate;

class ProductController extends Controller
{
    public $data = [];

    public function index(Request $request)
    {
        $category = $request->category;
        $search = $request->search;
        $price = $request->priceRange;
        $sort = $request->sort;

        $this->data['products'] = Product::with('trans')
            ->when($category, function ($q, $category) {
                $cat = ProductCategoryTranslate::where('title', 'like', '%' . $category . '%')->where('lang', locale())->pluck('parent_id')->toArray();
                $q->whereHasCategories($cat);
            })
            ->when($search, function ($q, $search) {
                $titles = ProductsTranslate::where('title', 'like', '%' . $search . '%')->where('lang', locale())->pluck('parent_id')->toArray();
                $q->whereIn('id', $titles);
            })
            ->when($price, function ($q, $price) {
                [$min, $max] = explode(';', $price);

                $q->whereBetween('price', [(float)$min, (float)$max]);
            })
            ->where('status', 1)
            ->when($sort, function ($q, $sort) {
                if ($sort === 'price_asc') {
                    $q->orderBy('price', 'asc');
                } elseif ($sort === 'price_desc') {
                    $q->orderBy('price', 'desc');
                }
            }, function ($q) {
                $q->orderBy('created_at', 'desc');
            })
            ->paginate(12);

        $this->data['categories'] = ProductCategory::query()
            ->with('trans')
            ->withProductsCount()
            ->get();

        return view('client.products.index', $this->data);
    }

    public function inner($product)
    {
        $this->data['product'] = Product::with(['trans', 'images'])
            ->where('status', 1)
            ->where(function ($q) use ($product) {
                $q->where('id', (int)$product)
                    ->orWhere('slug', $product);
            })->firstOrFail();

        $this->data['categories'] = ProductCategory::with('trans')->whereIn('id', json_decode($this->data['product']->category_id, true))->get();


        $categoryIds = json_decode($this->data['product']->category_id, true);

        $this->data['relateds'] = Product::with(['trans'])
            ->where('status', 1)
            ->where('id', '!=', $this->data['product']->id)
            ->where(function ($q) use ($categoryIds) {
                foreach ($categoryIds as $catId) {
                    $q->orWhereJsonContains('category_id', (string) $catId);
                }
            })
            ->take(4)
            ->get();

        $this->data['benefits'] = Benefit::allItems(locale(), true);

        return view('client.products.inner', $this->data);
    }

    public function getProductInfo(Request $request)
    {
        if(!$request->has('id')){
            return response()->json(['critical_error' => 'Request does not have id']);
        }

        $productId = $request->id;

        $product = Product::with('trans')->findOrFail($productId);

        return response()->json(
            [
                'status' => 1,
                'product' => [
                    'id' => $product->id,
                    'title' => $product->trans->title,
                    'description' => $product->trans->short_description,
                    'price' => $product->price,
                    'image' => $product->image
                ]
            ]);

    }
}
