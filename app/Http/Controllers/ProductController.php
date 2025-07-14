<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Models\Color;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\ProductType;
use App\Models\ProductVariation;
use App\Models\Size;
use App\Services\ProductService;
use Exception;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
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
        $productData = $this->productService->getProduct($product, $variation);

        return Inertia::render('Product', [
            'product' => $productData['product'],
            'activeVariation' => $productData['activeVariation'],
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
        $this->authorize('modify', Product::class);

        $this->productService->update($request, $product);

        $defaultVariation = $product->productVariations()->first();

        return redirect()->route('product.show', [
            'product' => $product->slug,
            'variation' => optional($defaultVariation)->sku,
        ])->with('success', 'Product updated successfully!');
    }

    public function destroy(Request $request, $id)
    {
        $this->authorize('modify', Product::class);

        $type = $request->query('type', 'product');

        if ($type === 'variation') {
            $variation = ProductVariation::findOrFail($id);
            $this->productService->deleteVariation($variation);
        } else {
            $product = Product::findOrFail($id);
            $this->productService->delete($product);
        }

        return redirect()->route('admin.products')->with('success', 'Deletion successful.');
    }

    public function searchResults()
    {
        return Inertia::render('Products', [
            'products' => $this->productService->getSearchResults(),
        ]);
    }
}
