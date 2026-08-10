<?php

namespace App\Http\Controllers\Api\V1\Products;

use App\Http\Controllers\Controller;
use App\Models\Products\Product;
use App\Models\Products\ProductVariant;
use App\Models\Products\ProductImage;
use App\Resources\Products\ProductResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $products = Product::query()
            ->with(['category', 'brand', 'unit', 'variants', 'images', 'stockItems'])
            ->when($request->search, fn ($q, $s) => $q->where('name', 'like', "%{$s}%")->orWhere('sku', 'like', "%{$s}%"))
            ->when($request->category_id, fn ($q, $c) => $q->where('category_id', $c))
            ->when($request->brand_id, fn ($q, $b) => $q->where('brand_id', $b))
            ->when($request->status, fn ($q, $s) => $q->where('status', $s))
            ->when($request->type, fn ($q, $t) => $q->where('type', $t))
            ->paginate($request->get('per_page', 15));

        return $this->paginated($products, ProductResource::collection($products->items()));
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'sku' => 'required|string|max:100|unique:products,sku',
            'barcode' => 'nullable|string|max:100',
            'description' => 'nullable|string|max:2000',
            'category_id' => 'required|exists:product_categories,id',
            'brand_id' => 'nullable|exists:product_brands,id',
            'unit_id' => 'required|exists:product_units,id',
            'cost_price' => 'required|numeric|min:0',
            'selling_price' => 'required|numeric|min:0',
            'tax_rate' => 'nullable|numeric|min:0|max:100',
            'type' => 'sometimes|string|in:simple,variant,service',
            'status' => 'sometimes|string|in:active,inactive',
            'weight' => 'nullable|numeric|min:0',
            'dimensions' => 'nullable|string|max:100',
            'minimum_stock_level' => 'nullable|integer|min:0',
            'reorder_point' => 'nullable|integer|min:0',
            'is_taxable' => 'sometimes|boolean',
            'is_active' => 'sometimes|boolean',
        ]);

        $product = Product::create($validated);

        return $this->created(
            new ProductResource($product->load(['category', 'brand', 'unit'])),
            'Product created successfully'
        );
    }

    public function show(Product $product): JsonResponse
    {
        return $this->success(
            new ProductResource($product->load(['category', 'brand', 'unit', 'variants', 'images', 'stockItems.warehouse']))
        );
    }

    public function update(Request $request, Product $product): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'sku' => 'sometimes|string|max:100|unique:products,sku,' . $product->id,
            'barcode' => 'nullable|string|max:100',
            'description' => 'nullable|string|max:2000',
            'category_id' => 'sometimes|exists:product_categories,id',
            'brand_id' => 'nullable|exists:product_brands,id',
            'unit_id' => 'sometimes|exists:product_units,id',
            'cost_price' => 'sometimes|numeric|min:0',
            'selling_price' => 'sometimes|numeric|min:0',
            'tax_rate' => 'nullable|numeric|min:0|max:100',
            'type' => 'sometimes|string|in:simple,variant,service',
            'status' => 'sometimes|string|in:active,inactive',
            'weight' => 'nullable|numeric|min:0',
            'dimensions' => 'nullable|string|max:100',
            'minimum_stock_level' => 'nullable|integer|min:0',
            'reorder_point' => 'nullable|integer|min:0',
            'is_taxable' => 'sometimes|boolean',
            'is_active' => 'sometimes|boolean',
        ]);

        $product->update($validated);

        return $this->success(
            new ProductResource($product->fresh()->load(['category', 'brand', 'unit'])),
            'Product updated successfully'
        );
    }

    public function destroy(Product $product): JsonResponse
    {
        $product->delete();

        return $this->noContent('Product deleted successfully');
    }

    public function variants(Product $product): JsonResponse
    {
        return $this->success($product->variants()->get());
    }

    public function storeVariant(Request $request, Product $product): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'sku' => 'required|string|max:100|unique:product_variants,sku',
            'cost_price' => 'required|numeric|min:0',
            'selling_price' => 'required|numeric|min:0',
            'stock' => 'nullable|integer|min:0',
            'attributes' => 'nullable|array',
            'is_active' => 'sometimes|boolean',
        ]);

        $validated['product_id'] = $product->id;
        $variant = ProductVariant::create($validated);

        return $this->created($variant, 'Variant created successfully');
    }

    public function updateVariant(Request $request, Product $product, ProductVariant $variant): JsonResponse
    {
        if ($variant->product_id !== $product->id) {
            abort(403, 'Variant does not belong to this product');
        }

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'sku' => 'sometimes|string|max:100|unique:product_variants,sku,' . $variant->id,
            'cost_price' => 'sometimes|numeric|min:0',
            'selling_price' => 'sometimes|numeric|min:0',
            'stock' => 'nullable|integer|min:0',
            'attributes' => 'nullable|array',
            'is_active' => 'sometimes|boolean',
        ]);

        $variant->update($validated);

        return $this->success($variant->fresh(), 'Variant updated successfully');
    }

    public function deleteVariant(Product $product, ProductVariant $variant): JsonResponse
    {
        if ($variant->product_id !== $product->id) {
            abort(403, 'Variant does not belong to this product');
        }

        $variant->delete();

        return $this->noContent('Variant deleted successfully');
    }

    public function images(Product $product): JsonResponse
    {
        return $this->success($product->images()->get());
    }

    public function uploadImage(Request $request, Product $product): JsonResponse
    {
        $request->validate([
            'image' => 'required|image|max:5120',
            'alt_text' => 'nullable|string|max:255',
            'sort_order' => 'nullable|integer|min:0',
            'is_primary' => 'sometimes|boolean',
        ]);

        if ($request->boolean('is_primary')) {
            $product->images()->where('is_primary', true)->update(['is_primary' => false]);
        }

        $image = $request->file('image');
        $path = $image->store('products/' . $product->id, 'public');

        $productImage = ProductImage::create([
            'product_id' => $product->id,
            'path' => $path,
            'filename' => $image->getClientOriginalName(),
            'alt_text' => $request->alt_text,
            'sort_order' => $request->sort_order ?? 0,
            'is_primary' => $request->boolean('is_primary'),
            'size' => $image->getSize(),
            'mime_type' => $image->getMimeType(),
        ]);

        return $this->created($productImage, 'Image uploaded successfully');
    }

    public function deleteImage(Product $product, ProductImage $image): JsonResponse
    {
        if ($image->product_id !== $product->id) {
            abort(403, 'Image does not belong to this product');
        }

        Storage::disk('public')->delete($image->path);
        $image->delete();

        return $this->noContent('Image deleted successfully');
    }

    public function stock(Product $product): JsonResponse
    {
        $stock = $product->stockItems()
            ->with('warehouse')
            ->get()
            ->map(fn ($item) => [
                'warehouse' => $item->warehouse->name,
                'quantity_on_hand' => $item->quantity_on_hand,
                'quantity_reserved' => $item->quantity_reserved,
                'quantity_available' => $item->quantity_available,
                'quantity_on_order' => $item->quantity_on_order,
            ]);

        return $this->success([
            'total_on_hand' => $stock->sum('quantity_on_hand'),
            'total_reserved' => $stock->sum('quantity_reserved'),
            'total_available' => $stock->sum('quantity_available'),
            'warehouses' => $stock,
        ]);
    }
}
