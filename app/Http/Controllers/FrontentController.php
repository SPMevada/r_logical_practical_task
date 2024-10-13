<?php

namespace App\Http\Controllers;

use App\Http\Requests\FrontUserLoginRequest;
use App\Http\Requests\FrontUserRequest;
use App\Models\Cart;
use App\Models\FrontUser;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;

class FrontentController extends Controller
{
    public function frontent() {
        if(Auth::guard('frontUser')->check()) {
            return redirect()->route('frontent.dashboard');
        }
        return view('frontent.index');   
    }

    public function dashboard() {
        if(!Auth::guard('frontUser')->check()) {
            return redirect()->route('frontent.index');
        }
        $products = Product::with(['categories'])->where('status','active')->get();
        $front_user_id = Auth::guard('frontUser')->user()->id;
        // $carts = Cart::where('front_user_id',$front_user_id)->get();
        
        return view('frontent.dashboard',compact('products'));
    }

    public function register() {
        return  view('frontent.register');   
    }

    public function store(FrontUserRequest $request){
        try{
            FrontUser::create([
                'name' => !empty($request->name) ? $request->name : '',
                'email' => !empty($request->email) ? $request->email : '',
                'password' => !empty($request->password) ? Hash::make($request->password) : ''
            ]);
        } catch (\Exception $e) {
            return response()->json($this->ajaxResponse(false,[],$e->getMessage()),200);
        }
        return response()->json($this->ajaxResponse(true,[],'User registed.'),200);
    }

    public function ajaxResponse($status,$data,$message) {
        return [
            'status' => $status,
            'data' => $data,
            'message' => $message
        ];
    }

    public function login(FrontUserLoginRequest $request){
        $credintials = [
            'email' => $request->email,
            'password' => $request->password,
        ];

        if(Auth::guard('frontUser')->attempt($credintials)) {
            return response()->json($this->ajaxResponse(true,[],'loged in succeessfully.'),200);   
        }
        return response()->json($this->ajaxResponse(true,[],'Something went wrong.'),200);
    }

    public function logout(){
        Auth::guard('frontUser')->logout();
        return redirect()->route('frontent.index');
    }

    public function addToCart(Request $request) {
        $product_id = !empty($request->product_id) ? $request->product_id : '';
        $card_data = [
            'product_id' => $product_id,
            'product_qty' => 1,
            'front_user_id' => Auth::guard('frontUser')->user()->id,
            'category_id' => !empty($request->category_id) ? $request->category_id : '',
        ];
        $product = Product::find($product_id);
        $product_qty = !empty($product->qty) ? $product->qty : 0;
        $cart_product_count = Cart::where('product_id',$product_id)->count('product_qty');
        
        if($cart_product_count >= $product_qty) {
            return response()->json($this->ajaxResponse(false,[],'No more product store available.'),200);
        }

        try{
            Cart::create($card_data);
        } catch (\Exception $e) {
            return response()->json($this->ajaxResponse(false,[],$e->getMessage()),200);
        }

        return response()->json($this->ajaxResponse(true,[],'Product added.'),200);
    }

    public function cartItem(Request $request) {
        if(!Auth::guard('frontUser')->check()) {
            return redirect()->route('frontent.index');
        }
        if($request->ajax()) {
            $front_user_id = Auth::guard('frontUser')->user()->id;
            $carts = Cart::with(['products','categories'])->where('front_user_id',$front_user_id)->get();

            return DataTables::of($carts)
                        ->addIndexColumn()
                        ->addColumn('name', function($row) {
                            return !empty($row->products->name) ? $row->products->name : '';
                        })
                        ->addColumn('image', function($row) {
                            $category_image = !empty($row->categories->image) ? $row->categories->image : '';
                            $image = Storage::url($category_image);
                            return "<a href='$image' target='__blank'>Image Link</a>";
                        })
                        ->addColumn('price', function($row) {
                            return !empty($row->products->price) ? $row->products->price : '';
                        })
                        ->addColumn('qty', function($row) {
                            return !empty($row->products->qty) ? $row->products->qty : '';
                        })
                        ->rawColumns(['image'])
                        ->make(true);
        };
        return view('card_item.index');
    }
}
