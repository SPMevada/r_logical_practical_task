<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductRequest;
use App\Models\Category;
use App\Models\Product;
use App\Models\productCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;

class ProductController extends Controller
{
    public function index(Request $request) {
        if($request->ajax()) {
            $user_id = Auth::user()->id;
            $product = Product::select('*')->where('user_id',$user_id)->get();

            return DataTables::of($product)
                        ->addIndexColumn()
                        ->addColumn('name', function($row) {
                            return !empty($row->name) ? $row->name : '';
                        })
                        ->addColumn('description', function($row) {
                            return !empty($row->description) ? $row->description : '';
                        })
                        ->addColumn('status', function($row) {
                            if ($row->status == 'active') {
                                return 'Active';
                            } else {
                                return 'In Active';
                            }
                        })
                        ->addColumn('action', function($row) {
                            $btn = '';
                            $btn .= '<button type="button" data-id="'.$row->id.'" class="btn btn-primary mx-1 editBtn">Edit</button>';
                            $btn .= '<button type="button" data-id="'.$row->id.'" class="btn btn-danger deleteBtn">Delete</button>';
                            return $btn;
                        })
                        ->rawColumns(['action'])
                        ->make(true);
        };
        $categories = Category::select('id','name')->where('status','active')->get();
        return view('product.index',compact('categories'));
    }

    public function store(ProductRequest $request) {
        $data = [
            'name' => !empty($request->name) ? $request->name : '',
            'status' => !empty($request->status) ? $request->status : '',
            'description' => !empty($request->description) ? $request->description : '',
            'qty' => !empty($request->qty) ? $request->qty : '',
            'price' => !empty($request->price) ? $request->price : '',
            'status' => !empty($request->status) ? $request->status : '',
            'user_id' => Auth::user()->id,
        ];
        $categories = !empty($request->categories) ? $request->categories : '';
        try{
            $product = Product::create($data);
            foreach($categories as $category) {
                $category_arr = [
                    'category_id' => $category,
                    'product_id' => $product->id
                ];

                productCategory::create($category_arr);
            }
        } catch (\Exception $e) {
            return response()->json($this->ajaxResponse(false,[],$e->getMessage()),200);
        }
        return response()->json($this->ajaxResponse(true, [], 'Product creaded.'), 200);
    }

    public function edit(Request $request){
        $product_id = !empty($request->product_id) ? $request->product_id : '';
        $product = product::find($product_id);
        $product_category = productCategory::where('product_id',$product_id)->get()->toArray();
        $categories = Category::select('id','name')->where('status','active')->get();
        $form  = view('product.form',compact('product','product_category','categories'))->render();

        return response()->json($this->ajaxResponse(true,['productHtml' => $form],'Product fetch successfully.'),200);
    }

    public function update(ProductRequest $request){
        $product_id = !empty($request->product_id) ? $request->product_id : '';
        $data = [
            'name' => !empty($request->name) ? $request->name : '',
            'status' => !empty($request->status) ? $request->status : '',
            'description' => !empty($request->description) ? $request->description : '',
            'qty' => !empty($request->qty) ? $request->qty : '',
            'price' => !empty($request->price) ? $request->price : '',
            'status' => !empty($request->status) ? $request->status : '',
            'user_id' => Auth::user()->id,
        ];

        $categories = !empty($request->categories) ? $request->categories : '';
        try{
            Product::where('id',$product_id)->update($data);
            productCategory::where('product_id',$product_id)->delete();
            foreach($categories as $category) {
                $category_arr = [
                    'category_id' => $category,
                    'product_id' => $product_id
                ];
                productCategory::create($category_arr);
            }
        } catch (\Exception $e) {
            return response()->json($this->ajaxResponse(false,[],$e->getMessage()),200);
        }
        return response()->json($this->ajaxResponse(true, [], 'Product updated.'), 200);
    }

    public function delete(Request $request) {
        $product_id = !empty($request->product_id) ? $request->product_id : "";

        try{
            Product::where('id',$product_id)->delete();
        } catch (\Exception $e) {
            return response()->json($this->ajaxResponse(false,[],$e->getMessage()),200);
        }
        return response()->json($this->ajaxResponse(true,[],'Product deleted.'),200);
    }

    public function ajaxResponse($status,$data,$message) {
        return [
            'status' => $status,
            'data' => $data,
            'message' => $message
        ];
    }
}
