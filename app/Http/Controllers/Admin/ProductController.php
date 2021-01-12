<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Model\Product;
use App\Validator\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class ProductController extends Controller
{

    public function __construct(ApiResponse $apiResponse)
    {
        $this->apiResponse = $apiResponse;
    }
    protected $apiResponse;
    protected $rules = array(
        'title' => 'required|unique:products',
        'description' => 'required',
        'price' => 'nullable',
        'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
    );
    public function index(Request $request)
    {
        $products = Product::all();
        if ($request->is('wedevs/*')) { //for api request response
            return $this->apiResponse->responseApiWithSuccess('product list get successfully', $products);
        }
        if ($request->message) {
            Session::flash('message', 'Deleted Successfully.');
        }
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

            if ($request->is('wedevs/*')) { //for api request response
                return $this->apiResponse->responseApiWithError('Invalid from   data', $validator->messages()->all());
            }

            return redirect()->route('admin.product.create')->withErrors($validator);
        } else {
            $product = new Product();
            $this->saveProduct($product, $request->all(), 1);

            if ($request->is('wedevs/*')) { //for api request response
                return $this->apiResponse->responseApiWithSuccess('Product save successfully', []);
            }

            Session::flash('message', 'Created Successfully.');
            return redirect()->route('admin.product.index');
        }

    }

    public function show($id)
    {
        $product = Product::whereId($id)->get();
        if (!$product->isEmpty()) {
            return $this->apiResponse->responseApiWithSuccess('Product get successfully', $product);
        } else {
            return $this->apiResponse->responseApiWithSuccess('No Product found ', []);
        }

    }

    public function edit($id)
    {
        $product = Product::findOrfail($id);

        return view('product.edit', compact('product'));
    }

    public function update(Request $request, Product $product)
    {
        $this->rules = collect($this->rules)->merge([
            'title' => 'nullable|unique:products,title' . ($product->id ? ",$product->id" : ''),
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ])->toArray();

        $validator = Validator::make($request->all(), $this->rules);
        if ($validator->fails()) {

            if ($request->is('wedevs/*')) { //for api request response
                return $this->apiResponse->responseApiWithError('Invalid from data', $validator->messages()->all());
            }

            $errorMessage = $validator->messages()->all();
            Session::flash('message', $errorMessage[0]);
            return redirect()->route('admin.product.edit', $product->id)->withErrors($validator);
        } else {
            if ($request->file('image')) {
                $this->saveProduct($product, $request->all(), 1, 1);
            } else {
                $this->saveProduct($product, $request->all(), 0);
            }

            if ($request->is('wedevs/*')) { //for api request response
                return $this->apiResponse->responseApiWithSuccess('Product updated successfully', []);
            }

            Session::flash('message', 'updated Successfully.');
            return redirect()->route('admin.product.index');

        }

    }

    public function destroy(Request $request, $id)
    {
        $product = Product::whereId($id)->first();
        // return $this->apiResponse->responseApiWithSuccess('Product Deleted successfully', $product);

        if ($product === null) {
            return $this->apiResponse->responseApiWithError('No Product Found', []);
        }
        if (file_exists(public_path($product->image))) {
            //dd('hi');
            unlink(public_path($product->image)); //delete image from folder
        }
        $product->delete();
        // return redirect()->route('admin.product.index');
        if ($request->is('wedevs/*')) { //for api request response
            return $this->apiResponse->responseApiWithSuccess('Product Deleted successfully', []);
        }

        return response()->json([
            'messge' => 'product deleted successfully.',
        ]);

    }
    public function saveProduct($model, $data = [], $imageUpdate = null, $is_update = null)
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
