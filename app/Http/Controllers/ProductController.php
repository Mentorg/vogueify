<?php

namespace App\Http\Controllers;

use App\FIlters\Search;
use App\Http\Requests\StoreProductRequest;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Services\ProductService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ProductController extends Controller
{
    use AuthorizesRequests;

    protected $productService;

    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }

    public function index(Request $request)
    {
        return Inertia::render('Products', [
            'products' => $this->productService->getProducts($request)
        ]);
    }

    public function show(Product $product)
    {
        return Inertia::render('Product', [
            'product' => $this->productService->getProduct($product)
        ]);
    }

    public function create()
    {
        $this->authorize('modify', Product::class);

        return Inertia::render('Admin/ProductForm', [
            'categories' => ProductCategory::all()
        ]);
    }

    public function store(StoreProductRequest $request)
    {
        $this->authorize('modify', Product::class);

        $createdProduct = $this->productService->create($request->validated(), $request);

        return redirect()->route('product.show', $createdProduct->id)
                     ->with(['success' => 'Product created successfully.', 'product' => $createdProduct ]);
    }

    public function searchResults()
    {
        return Inertia::render('Products', [
            'products' => $this->productService->getSearchResults(),
        ]);
    }
}
