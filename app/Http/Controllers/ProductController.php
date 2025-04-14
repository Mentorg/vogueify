<?php

namespace App\Http\Controllers;

use App\FIlters\Search;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Services\ProductService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
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
            'products' => $this->productService->getProducts($request),
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

        return redirect()->route('product.show', $createdProduct)
                     ->with(['success' => 'Product created successfully.', 'product' => $createdProduct ]);
    }

    public function show(Product $product)
    {
        return Inertia::render('Product', [
            'product' => $this->productService->getProduct($product)
        ]);
    }

    public function edit(Product $product)
    {
        $this->authorize('modify', Product::class);

        $product->load(['productVariations', 'category']);

        return Inertia::render('Admin/ProductUpdate', [
            'product' => $product->only([
                'id', 'name', 'description', 'features', 'gender', 'category_id'
            ]) + [
                'product_variations' => $product->productVariations,
                'category' => $product->category
            ],
            'categories' => ProductCategory::all()
        ]);
    }

    public function update(UpdateProductRequest $request, $id)
    {
        $product = Product::with('productVariations')->findOrFail($id);
        Log::info('ProductController@update - Product:', [$product]);
        $this->authorize('modify', Product::class);

        $product->load('productVariations');

        $updatedProduct = $this->productService->update($product, $request, $request->file('image'));

        return redirect()->route('product.show', $updatedProduct->id)
                         ->with(['success' => 'Product updated successfully.']);
    }

    public function searchResults()
    {
        return Inertia::render('Products', [
            'products' => $this->productService->getSearchResults(),
        ]);
    }
}
