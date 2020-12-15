<?php

namespace App\Http\Controllers;

use App\Model\Product;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $products = Product::all();
        return view('product.index', compact('products'));

        //redirect()->route('admin.product.index');
        return view('home');
    }
}
