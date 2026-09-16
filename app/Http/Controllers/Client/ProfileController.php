<?php

namespace App\Http\Controllers\Client;

use App\Models\User;
use App\Models\Order;
use App\Models\Product;
use App\Models\District;
use Illuminate\Http\Request;
use App\Services\CartService;
use App\Services\WishlistService;
use App\Services\Payments\GatewayConfigResolver;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    public $data;

    public function addToWishlist(Request $request)
    {
        $productId = (int) $request->id;

        if (!$productId) {
            return response()->json(['status' => 0], 404);
        }

        $product = Product::find($productId);

        if (!$product || $product->finished) {
            return response()->json(['status' => 0], 200);
        }

        $wishlist = new WishlistService;

        if ($wishlist->has($productId)) {
            return response()->json(['status' => 0], 200);
        }

        $wishlist->add($productId);

        return response()->json(['status' => 1], 200);
    }

    public function removeFromWishlist(Request $request)
    {
        $productId = (int) $request->id;

        if (!$productId) {
            return response()->json(['status' => 0], 404);
        }

        (new WishlistService)->remove($productId);

        return response()->json(['status' => 1], 200);
    }

    public function removeFromCart(Request $request)
    {
        $productID = (int)$request->id;

        if (!$productID) {
            return response()->json(['status' => 0, 'not_added' => true]);
        }

        (new CartService)->remove($productID);

        return response()->json(['status' => true, 'total_price' => getPrice(\Cart::getTotal()), 'item_in_cart' => \Cart::getContent()->count()]);
    }

    public function addToCart(Request $request)
    {
        $productID = (int)$request->id;
        $qty = $request->quantity;

        (new CartService)->remove($productID);

        $product = Product::getItemInfo($productID, locale());

        if (!$product) {
            return response()->json(['status' => 0, 'not_added' => true]);
        }

        $add_to_cart = \Cart::add(array(
            'id' =>  $product->id,
            'name' => $product->title,
            'price' => $product->price,
            'quantity' => $qty,
            'attributes' => array(
                'image' => $product->image
            ),
            'associatedModel' => $product
        ));


        if (!$add_to_cart) {
            return response()->json(['status' => 0, 'not_added' => true]);
        }

        return response()->json([
            'status' => true,
            'total_price' => getPrice(\Cart::getTotal()),
            'item_in_cart' => \Cart::getContent()->count(),
            'sub_tota_product' => getPrice($product->price * $qty)
        ]);
    }

    public function wishlist()
    {
        $ids = (new WishlistService)->ids();

        $this->data['wishlists'] = Product::with('trans')->whereIn('id', $ids)->get();

        return view('client.profile.wishlist', $this->data);
    }

    public function cart()
    {
        $this->data['cart'] = \Cart::getContent();
        
        $this->data['products'] = $this->data['cart']->pluck('id')->toArray();
        
        $products = Product::whereIn('id',$this->data['products'])->get();
        
        foreach($products as $product){
            
            if(!$product || !$product->available || !$product->status){
                \Cart::remove($product->id);
            }
        }
        
        return view('client.profile.cart', $this->data);
    }

    public function account()
    {
        $this->data['user'] = User::findOrFail(auth()->id());

        $this->data['orders'] = Order::where('user_id', auth()->id())->where('status', 1)->get();

        return view('client.profile.my-account', $this->data);
    }

    public function register()
    {
        return view('client.profile.register');
    }

    public function checkout(GatewayConfigResolver $gatewayConfigResolver)
    {
        $this->data['cart'] = \Cart::getContent();

        $this->data['cities'] = District::allItems(locale(), true);
        $this->data['paymentMethods'] = $gatewayConfigResolver->activeCheckoutMethods();

        return view('client.profile.checkout', $this->data);
    }

    public function reset()
    {
        return view('client.profile.reset');
    }


    public function update(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'surname' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255'],
            'phone' => ['required'],
        ]);

        $ifUserExists = User::where('email', $data['email'])->where('id', '!=', auth()->id())->first();

        if (!is_null($ifUserExists)) {
            return redirect()->back()->with(['error' => 'User exists with that email!']);
        }

        auth()->user()->update($data);

        $userPassword = $request->validate([
            'password' => [
                'nullable',
                'confirmed',
                Password::min(8)
                    ->mixedCase()
                    ->numbers()
                    ->symbols()
            ],
            'password_confirmation' => ['nullable'],
            'oldPassword' => ['nullable']
        ]);

        if (is_null($userPassword['password'])) {
            unset($userPassword['password']);
        } else {

            if (!Auth::attempt(['email' => auth()->user()->email, 'password' => $userPassword['oldPassword']])) {
                return redirect()->back()->with(['error' => 'Credentials is not correct!']);
            }

            $userPassword['password'] = bcrypt($userPassword['password']);
        }

        unset($userPassword['oldPassword'], $userPassword['password_confirmation']);


        auth()->user()->update($userPassword);

        return redirect()->back()->with(['success' => 'Information updated successfully!']);
    }

    public function login()
    {
        return view('client.profile.login');
    }
}
