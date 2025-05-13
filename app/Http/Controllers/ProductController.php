<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Models\Color;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\ProductType;
use App\Models\Size;
use App\Services\ProductService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
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
            'categories' => ProductCategory::all(),
            'types' => ProductType::all(),
            'sizes' => Size::all(),
            'colors' => Color::all()
        ]);
    }

    public function store(StoreProductRequest $request)
    {
        $this->authorize('modify', Product::class);

        $createdProduct = $this->productService->create($request->validated(), $request);

        return redirect()->route('product.show', $createdProduct)
                     ->with(['success' => 'Product created successfully.', 'product' => $createdProduct ]);
    }

    public function show(Product $product, ?string $variation = null)
    {
        if ($variation && !$product->productVariations()->where('sku', $variation)->exists()) {
            throw ValidationException::withMessages([
                'variation' => 'The selected variation is invalid for this product.',
            ]);
        }

        $data = $this->productService->getProduct($product, $variation);

        return Inertia::render('Product', [
            'product' => $data['product'],
            'activeVariation' => $data['activeVariation'],
        ]);
    }

    public function edit(Product $product)
    {
        $this->authorize('modify', Product::class);

        $product->load(['productVariations.sizes', 'productVariations.type', 'category']);

        return Inertia::render('Admin/ProductUpdate', [
            'product' => $product,
            'categories' => ProductCategory::all(),
            'types' => ProductType::all(),
            'sizes' => Size::all(),
            'colors' => Color::all()
        ]);
    }

    public function update(UpdateProductRequest $request, Product $product)
    {
        try {
            $this->productService->update($request, $product);

            $defaultVariation = $product->productVariations()->first();

            return redirect()->route('product.show', [
                'product' => $product->slug,
                'variation' => optional($defaultVariation)->sku,
            ])->with('success', 'Product updated successfully!');
        } catch (\Exception $e) {
            Log::error('Product update failed: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Product update failed.'])->withInput();
        }
    }

    public function destroy(Product $product)
    {
        $this->authorize('modify', Product::class);

        $this->productService->delete($product);

        return redirect()->route('admin.products');
    }

    public function searchResults()
    {
        return Inertia::render('Products', [
            'products' => $this->productService->getSearchResults(),
        ]);
    }
}
