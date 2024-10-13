<?php

namespace App\Http\Controllers;

use App\Http\Requests\CategoryRequest;
use App\Http\Requests\CategoryUpdateRequest;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\DataTables;

class CategoryController extends Controller
{
    public function index(Request $request) {
        if($request->ajax()) {
            $user_id = Auth::user()->id;
            $category = Category::select('*')->where('user_id',$user_id)->get();

            return DataTables::of($category)
                        ->addIndexColumn()
                        ->addColumn('name', function($row) {
                            return !empty($row->name) ? $row->name : '';
                        })
                        ->addColumn('description', function($row) {
                            return !empty($row->description) ? $row->description : '';
                        })
                        ->addColumn('image', function($row) {
                            $image = Storage::url($row->image);
                            return "<a href='$image' target='__blank'>Image Link</a>";
                        })
                        ->addColumn('status', function($row) {
                            $btn = '';
                            if ($row->status == 'active') {
                                $btn .= '<div class="form-check form-switch">
                                            <input class="form-check-input status" data-id="'.$row->id.'" type="checkbox" role="switch" checked>
                                        </div>';
                            } else {
                                $btn .= '<div class="form-check form-switch">
                                            <input class="form-check-input status" data-id="'.$row->id.'" type="checkbox" role="switch" >
                                        </div>';
                            }
                            return $btn;
                        })
                        ->addColumn('action', function($row) {
                            $btn = '';
                            $btn .= '<button type="button" data-id="'.$row->id.'" class="btn btn-primary mx-1 editBtn">Edit</button>';
                            $btn .= '<button type="button" data-id="'.$row->id.'" class="btn btn-danger deleteBtn">Delete</button>';
                            return $btn;
                        })
                        ->rawColumns(['action','image','status'])
                        ->make(true);
        };

        return view('dashboard');
    }

    public function store(CategoryRequest $request) {
        $image = !empty($request->image) ? $request->image : '';

        if($request->hasFile('image') && Storage::disk('public')->exists($image)) {
            Storage::disk('public')->delete($image);
            $image = Storage::disk('public')->put('images/',$request->file('image'));
        } else {
            $image = Storage::disk('public')->put('images/',$request->file('image'));
        }

        $data = [
            'name' => !empty($request->name) ? $request->name : '',
            // 'status' => !empty($request->status) ? $request->status : '',
            'status' => 'active',
            'description' => !empty($request->description) ? $request->description : '',
            'image' => $image,
            'user_id' => Auth::user()->id,
        ];

        try{
            Category::create($data);
        } catch (\Exception $e) {
            return response()->json($this->ajaxResponse(false,[],$e->getMessage()),200);
        }
        return response()->json($this->ajaxResponse(true, [], 'Category creaded successully'), 200);
    }

    public function edit(Request $request){
        $category_id = !empty($request->category_id) ? $request->category_id : '';
        $category = Category::find($category_id);
        $form  = view('categories.form',compact('category'))->render();

        return response()->json($this->ajaxResponse(true,['categoryHtml' => $form],'Category fetch successfully.'),200);
    }

    public function ajaxResponse($status,$data,$message) {
        return [
            'status' => $status,
            'data' => $data,
            'message' => $message
        ];
    }

    public function update(CategoryUpdateRequest $request){
        $category_id = !empty($request->category_id) ? $request->category_id : '';
        $category = Category::find($category_id);
        $check_image = !empty($request->check_image) ? $request->check_image : '';
        if($request->hasFile('image') && Storage::disk('public')->exists($check_image)) {
            Storage::disk('public')->delete($check_image);
            $check_image = Storage::disk('public')->put('images/',$request->file('image'));
        }
        
        $data = [
            'name' => !empty($request->name) ? $request->name : '',
            'image' => $check_image,
            'description' => !empty($request->description) ? $request->description : '',
            'user_id' => Auth::user()->id,
        ];

        try{
            $category->update($data);
        } catch (\Exception $e) {
            return response()->json($this->ajaxResponse(false,[],$e->getMessage()),200);
        }
        return response()->json($this->ajaxResponse(true,[],'Category fetch successfully.'),200);
    }

    public function status(Request $request) {
        $category_id = !empty($request->category_id) ? $request->category_id : "";
        $status = !empty($request->status) ? $request->status : '';
        try{
            category::find($category_id)->update(['status'=>$status]);
        } catch (\Exception $e) {
            return response()->json($this->ajaxResponse(false,[],$e->getMessage()),200);
        }
        return response()->json($this->ajaxResponse(true,[],'Category status changed successfully.'),200);
    }

    public function delete(Request $request) {
        $category_id = !empty($request->category_id) ? $request->category_id : "";

        try{
            category::where('id',$category_id)->delete();
        } catch (\Exception $e) {
            return response()->json($this->ajaxResponse(false,[],$e->getMessage()),200);
        }
        return response()->json($this->ajaxResponse(true,[],'Category deleted.'),200);
    }

}
