<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Model\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class ProductController extends Controller
{

    protected $rules = array(
        'title' => 'required|unique:products',
        'description' => 'required',
        'price' => 'nullable',
        'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
    );
    public function index(Request $request)
    {
        if($request->message){
            Session::flash('message', 'Deleted Successfully.');
        }
        $products = Product::all();
        return view('product.index', compact('products'));
    }

    public function create()
    {
        return view('product.create');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), $this->rules);

        if ($validator->fails()) {
            return redirect()->route('admin.product.create')->withErrors($validator);
        } else {
            $product = new Product();
            $this->saveProduct($product, $request->all(), 1);
            Session::flash('message', 'Created Successfully.');
            return redirect()->route('admin.product.index');

        }

    }

    public function show($id)
    {
        dd($id);
    }

    public function edit($id)
    {
        $product = Product::findOrfail($id);

        return view('product.edit', compact('product'));
    }

    public function update(Request $request, Product $product)
    {
        $this->rules = collect($this->rules)->merge([
            'title' => 'required|unique:products,title' . ($product->id ? ",$product->id" : ''),
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ])->toArray();

        $validator = Validator::make($request->all(), $this->rules);
        if ($validator->fails()) {
            $errorMessage = $validator->messages()->all();
            Session::flash('message', $errorMessage[0]);
            return redirect()->route('admin.product.edit',$product->id)->withErrors($validator);
        } else {
            if ($request->file('image')) {
                $this->saveProduct($product, $request->all(), 1,1);
            } else {
                $this->saveProduct($product, $request->all(), 0);
            }
            Session::flash('message', 'updated Successfully.');
            return redirect()->route('admin.product.index');

        }

    }

    public function destroy(Product $product)
    {
        //dd($product->id);
        if (file_exists(public_path($product->image))) {
            //dd('hi');
            unlink(public_path($product->image)); //delete image from folder
        }
        $product->delete();
        // return redirect()->route('admin.product.index');
        return response()->json([
            'messge' => 'product deleted successfully.',
        ]);

    }
    public function saveProduct($model, $data = [], $imageUpdate = null,$is_update=null)
    {
        $path = '';
        if ($imageUpdate) {
            $fileName = Str::random(25) . '_123_' . $data['image']->getClientOriginalName();
            request()->image->move(public_path('storage/product'), $fileName);
            $path = 'storage/product/' . $fileName;
            if (file_exists(public_path($model->image)) && $is_update) {
                unlink(public_path($model->image));
            }
        } else {
            $path = $model->image;
        }

        $model->title = $data['title'];
        $model->description = $data['description'];
        $model->price = $data['price'];
        $model->image = $path;
        $model->save();

    }

}
