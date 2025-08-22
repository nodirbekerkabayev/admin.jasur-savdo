<?php

namespace App\Http\Controllers;

use App\Models\Optom;
use App\Models\Products;
use App\Models\Sale;
use App\Models\SaleItem;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class SaleController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/optomchilar",
     *     tags={"Optomchilar"},
     *     summary="List all optomchilar with search",
     *     description="Retrieve a list of optomchilar with sale_type 'optom' filtered by name",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="q",
     *         in="query",
     *         description="Search term for optomchi name",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="Ali"),
     *                 @OA\Property(property="phone", type="string", example="+998901234567"),
     *                 @OA\Property(property="address", type="string", example="Tashkent"),
     *                 @OA\Property(property="sale_type", type="string", example="optom"),
     *                 @OA\Property(property="created_by", type="string", example="Hasan"),
     *                 @OA\Property(property="created_at", type="string", format="date-time"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time")
     *             )
     *         )
     *     )
     * )
     */
    public function indexOptomchilar(Request $request): JsonResponse
    {
        $search = $request->input('q');
        $optomchilar = Optom::where('sale_type', 'optom')
            ->when($search, function ($query) use ($search) {
                return $query->where('name', 'like', "%{$search}%");
            })->get();

        return response()->json($optomchilar);
    }

    /**
     * @OA\Post(
     *     path="/api/optomchilar",
     *     tags={"Optomchilar"},
     *     summary="Create a new optomchi with products",
     *     description="Create a new optomchi and its products (sale items) together",
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name", "phone", "address", "created_by", "items"},
     *             @OA\Property(property="name", type="string", example="Ali"),
     *             @OA\Property(property="phone", type="string", example="+998901234567"),
     *             @OA\Property(property="address", type="string", example="Tashkent"),
     *             @OA\Property(property="sale_type", type="string", example="toychi"),
     *             @OA\Property(property="recorded_by", type="string", example="Jasur"),
     *             @OA\Property(
     *                 property="items",
     *                 type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="product_id", type="integer", nullable=true, example=1),
     *                     @OA\Property(property="name", type="string", nullable=true, example="Yangı meva"),
     *                     @OA\Property(property="quantity", type="integer", example=10),
     *                     @OA\Property(property="unit", type="string", example="dona", enum={"dona", "blok"}),
     *                     @OA\Property(property="price", type="integer", example=5000)
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Optomchi and products created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="optomchi", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="Ali"),
     *                 @OA\Property(property="phone", type="string", example="+998901234567"),
     *                 @OA\Property(property="address", type="string", example="Tashkent"),
     *                 @OA\Property(property="sale_type", type="string", example="optom"),
     *                 @OA\Property(property="created_by", type="string", example="Hasan"),
     *                 @OA\Property(property="created_at", type="string", format="date-time"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time")
     *             ),
     *             @OA\Property(
     *                 property="sale",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="optomchi_id", type="integer", example=1),
     *                 @OA\Property(property="total_sum", type="integer", example=50000),
     *                 @OA\Property(property="created_at", type="string", format="date-time"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time")
     *             ),
     *             @OA\Property(
     *                 property="sale_items",
     *                 type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="sale_id", type="integer", example=1),
     *                     @OA\Property(property="product_id", type="integer", nullable=true, example=1),
     *                     @OA\Property(property="name", type="string", nullable=true, example="Yangı meva"),
     *                     @OA\Property(property="quantity", type="integer", example=10),
     *                     @OA\Property(property="unit", type="string", example="dona"),
     *                     @OA\Property(property="price", type="integer", example=5000),
     *                     @OA\Property(property="subtotal", type="integer", example=50000),
     *                     @OA\Property(property="created_at", type="string", format="date-time"),
     *                     @OA\Property(property="updated_at", type="string", format="date-time")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="Validation failed"),
     *             @OA\Property(property="errors", type="object")
     *         )
     *     )
     * )
     */
    public function storeOptomchi(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:255|unique:optomchilar,phone',
            'address' => 'required|string|max:255',
            'sale_type' => 'required|string|max:255',
            'created_by' => 'required|string|max:255',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'nullable|exists:products,id',
            'items.*.name' => 'required_if:product_id,null|string|max:255',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit' => 'required|string|in:dona,blok',
            'items.*.price' => 'required|integer|min:0',
        ]);

        // Optomchi yaratish
        $optomchi = Optom::create([
            'name' => $validated['name'],
            'phone' => $validated['phone'],
            'address' => $validated['address'],
            'sale_type' => 'optom',
            'created_by' => $validated['created_by'],
        ]);

        // Sale yaratish
        $totalSum = 0;
        $sale = Sale::create([
            'optomchi_id' => $optomchi->id,
            'total_sum' => 0,
        ]);

        // Sale items yaratish
        $saleItems = [];
        foreach ($validated['items'] as $item) {
            $product = $item['product_id'] ? Products::find($item['product_id']) : null;
            $price = $item['price'];
            if ($product) {
                $priceKey = 'optom' === 'optom'
                    ? ($item['unit'] === 'dona' ? 'optom_dona_narxi' : 'optom_blok_narxi')
                    : ($item['unit'] === 'dona' ? 'toychi_dona_narxi' : 'toychi_blok_narxi');
                $price = $product->$priceKey;
            }
            $subtotal = $item['quantity'] * $price;

            $saleItem = SaleItem::create([
                'sale_id' => $sale->id,
                'product_id' => $item['product_id'],
                'name' => $item['product_id'] ? null : $item['name'],
                'quantity' => $item['quantity'],
                'unit' => $item['unit'],
                'price' => $price,
                'subtotal' => $subtotal,
            ]);

            $saleItems[] = $saleItem;
            $totalSum += $subtotal;
        }

        $sale->update(['total_sum' => $totalSum]);

        return response()->json([
            'status' => 'success',
            'optomchi' => $optomchi,
            'sale' => $sale,
            'sale_items' => $saleItems,
        ], 201);
    }

    /**
     * @OA\Get(
     *     path="/api/optomchilar/{id}",
     *     tags={"Optomchilar"},
     *     summary="Show an optomchi with sales and items",
     *     description="Retrieve details of a specific optomchi including sales and sale items",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Optomchi ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="name", type="string", example="Ali"),
     *             @OA\Property(property="phone", type="string", example="+998901234567"),
     *             @OA\Property(property="address", type="string", example="Tashkent"),
     *             @OA\Property(property="sale_type", type="string", example="optom"),
     *             @OA\Property(property="created_by", type="string", example="Hasan"),
     *             @OA\Property(property="created_at", type="string", format="date-time"),
     *             @OA\Property(property="updated_at", type="string", format="date-time"),
     *             @OA\Property(
     *                 property="sales",
     *                 type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="optomchi_id", type="integer", example=1),
     *                     @OA\Property(property="total_sum", type="integer", example=50000),
     *                     @OA\Property(property="created_at", type="string", format="date-time"),
     *                     @OA\Property(property="updated_at", type="string", format="date-time")
     *                 )
     *             ),
     *             @OA\Property(
     *                 property="sale_items",
     *                 type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="sale_id", type="integer", example=1),
     *                     @OA\Property(property="product_id", type="integer", nullable=true, example=1),
     *                     @OA\Property(property="name", type="string", nullable=true, example="Yangı meva"),
     *                     @OA\Property(property="quantity", type="integer", example=10),
     *                     @OA\Property(property="unit", type="string", example="dona"),
     *                     @OA\Property(property="price", type="integer", example=5000),
     *                     @OA\Property(property="subtotal", type="integer", example=50000),
     *                     @OA\Property(property="created_at", type="string", format="date-time"),
     *                     @OA\Property(property="updated_at", type="string", format="date-time")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Optomchi not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="Optomchi not found")
     *         )
     *     )
     * )
     */
    public function showOptomchi($id): JsonResponse
    {
        $optomchi = Optom::with(['sales', 'sales.saleItems'])->find($id);
        if (!$optomchi) {
            return response()->json(['status' => 'error', 'message' => 'Optomchi not found'], 404);
        }

        return response()->json($optomchi);
    }

    /**
     * @OA\Put(
     *     path="/api/optomchilar/{id}",
     *     tags={"Optomchilar"},
     *     summary="Update an optomchi with sales and items",
     *     description="Update an optomchi, its sales, and sale items together",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Optomchi ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string", example="Ali Updated"),
     *             @OA\Property(property="phone", type="string", example="+998901234568"),
     *             @OA\Property(property="address", type="string", example="Tashkent Updated"),
     *             @OA\Property(
     *                 property="sales",
     *                 type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="total_sum", type="integer", example=60000)
     *                 )
     *             ),
     *             @OA\Property(
     *                 property="sale_items",
     *                 type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="quantity", type="integer", example=15),
     *                     @OA\Property(property="unit", type="string", example="dona", enum={"dona", "blok"}),
     *                     @OA\Property(property="price", type="integer", example=4000)
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Optomchi, sales, and items updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="optomchi", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="Ali Updated"),
     *                 @OA\Property(property="phone", type="string", example="+998901234568"),
     *                 @OA\Property(property="address", type="string", example="Tashkent Updated"),
     *                 @OA\Property(property="sale_type", type="string", example="optom"),
     *                 @OA\Property(property="created_by", type="string", example="Hasan"),
     *                 @OA\Property(property="created_at", type="string", format="date-time"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time")
     *             ),
     *             @OA\Property(
     *                 property="sales",
     *                 type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="optomchi_id", type="integer", example=1),
     *                     @OA\Property(property="total_sum", type="integer", example=60000),
     *                     @OA\Property(property="created_at", type="string", format="date-time"),
     *                     @OA\Property(property="updated_at", type="string", format="date-time")
     *                 )
     *             ),
     *             @OA\Property(
     *                 property="sale_items",
     *                 type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="sale_id", type="integer", example=1),
     *                     @OA\Property(property="product_id", type="integer", nullable=true, example=1),
     *                     @OA\Property(property="name", type="string", nullable=true, example="Yangı meva"),
     *                     @OA\Property(property="quantity", type="integer", example=15),
     *                     @OA\Property(property="unit", type="string", example="dona"),
     *                     @OA\Property(property="price", type="integer", example=4000),
     *                     @OA\Property(property="subtotal", type="integer", example=60000),
     *                     @OA\Property(property="created_at", type="string", format="date-time"),
     *                     @OA\Property(property="updated_at", type="string", format="date-time")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Optomchi not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="Optomchi not found")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="Validation failed"),
     *             @OA\Property(property="errors", type="object")
     *         )
     *     )
     * )
     */
    public function updateOptomchi(Request $request, $id): JsonResponse
    {
        $optomchi = Optom::find($id);
        if (!$optomchi) {
            return response()->json(['status' => 'error', 'message' => 'Optomchi not found'], 404);
        }

        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'phone' => 'sometimes|required|string|max:255|unique:optomchilar,phone,' . $id,
            'address' => 'sometimes|required|string|max:255',
            'sales' => 'sometimes|array',
            'sales.*.id' => 'sometimes|required|exists:sales,id',
            'sales.*.total_sum' => 'sometimes|required|integer|min:0',
            'sale_items' => 'sometimes|array',
            'sale_items.*.id' => 'sometimes|required|exists:sale_items,id',
            'sale_items.*.quantity' => 'sometimes|required|integer|min:1',
            'sale_items.*.unit' => 'sometimes|required|string|in:dona,blok',
            'sale_items.*.price' => 'sometimes|required|integer|min:0',
        ]);

        $optomchi->update(array_filter([
            'name' => $validated['name'] ?? $optomchi->name,
            'phone' => $validated['phone'] ?? $optomchi->phone,
            'address' => $validated['address'] ?? $optomchi->address,
        ]));

        if (isset($validated['sales'])) {
            foreach ($validated['sales'] as $saleData) {
                $sale = Sale::find($saleData['id']);
                if ($sale && $sale->optomchi_id === $optomchi->id) {
                    $sale->update(['total_sum' => $saleData['total_sum']]);
                }
            }
        }

        if (isset($validated['sale_items'])) {
            foreach ($validated['sale_items'] as $itemData) {
                $saleItem = SaleItem::find($itemData['id']);
                if ($saleItem && $saleItem->sale->optomchi_id === $optomchi->id) {
                    $product = $saleItem->product_id ? Products::find($saleItem->product_id) : null;
                    $price = $itemData['price'] ?? $saleItem->price;
                    if ($product) {
                        $priceKey = 'optom' === 'optom'
                            ? ($itemData['unit'] === 'dona' ? 'optom_dona_narxi' : 'optom_blok_narxi')
                            : ($itemData['unit'] === 'dona' ? 'toychi_dona_narxi' : 'toychi_blok_narxi');
                        $price = $product->$priceKey;
                    }
                    $subtotal = ($itemData['quantity'] ?? $saleItem->quantity) * $price;
                    $saleItem->update(array_merge($itemData, ['price' => $price, 'subtotal' => $subtotal]));

                    $sale = $saleItem->sale;
                    $sale->update(['total_sum' => $sale->saleItems->sum('subtotal')]);
                }
            }
        }

        $optomchi->refresh();
        $optomchi->load(['sales', 'sales.saleItems']);

        return response()->json([
            'status' => 'success',
            'optomchi' => $optomchi,
            'sales' => $optomchi->sales,
            'sale_items' => $optomchi->sales->flatMap->saleItems,
        ], 200);
    }

    /**
     * @OA\Delete(
     *     path="/api/optomchilar/{id}",
     *     tags={"Optomchilar"},
     *     summary="Delete an optomchi with all sales and items",
     *     description="Delete an optomchi and all associated sales and sale items, or delete a specific sale item",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Optomchi ID or Sale Item ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Optomchi or sale item deleted successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Optomchi and all data deleted or Sale item deleted"),
     *             @OA\Property(property="new_total_sum", type="integer", nullable=true, example=0)
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Optomchi or sale item not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="Optomchi or sale item not found")
     *         )
     *     )
     * )
     */
    public function destroyOptomchi($id): JsonResponse
    {
        // Tekshirish: Optomchi yoki SaleItemni o‘chirish
        $optomchi = Optom::find($id);
        $saleItem = SaleItem::find($id);

        if ($optomchi) {
            // Optomchi va uning barcha ma'lumotlarini o‘chirish
            $optomchi->sales()->delete();
            $optomchi->delete();
            return response()->json(['status' => 'success', 'message' => 'Optomchi and all data deleted']);
        } elseif ($saleItem) {
            // Faqat bitta sale itemni o‘chirish
            $sale = $saleItem->sale;
            $newTotalSum = $sale->total_sum - $saleItem->subtotal;
            $sale->update(['total_sum' => $newTotalSum]);
            $saleItem->delete();
            return response()->json([
                'status' => 'success',
                'message' => 'Sale item deleted',
                'new_total_sum' => $newTotalSum,
            ], 200);
        } else {
            return response()->json(['status' => 'error', 'message' => 'Optomchi or sale item not found'], 404);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/getProducts",
     *     tags={"Products"},
     *     summary="Search products",
     *     description="Retrieve a list of products based on search query",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="q",
     *         in="query",
     *         description="Search term for product name",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="Olma")
     *             )
     *         )
     *     )
     * )
     */
    public function getProducts(Request $request): JsonResponse
    {
        $search = $request->input('q');
        $products = Products::when($search, function ($query) use ($search) {
            return $query->where('name', 'like', "%{$search}%");
        })->get(['id', 'name']);

        return response()->json($products);
    }

    /**
     * @OA\Get(
     *     path="/api/product-price/{productId}/{saleType}/{unit}",
     *     tags={"Products"},
     *     summary="Get product price based on sale type",
     *     description="Retrieve price for a product based on sale type and unit",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="productId",
     *         in="path",
     *         description="Product ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="saleType",
     *         in="path",
     *         description="Sale type (optom or toychi)",
     *         required=true,
     *         @OA\Schema(type="string", enum={"optom", "toychi"})
     *     ),
     *     @OA\Parameter(
     *         name="unit",
     *         in="path",
     *         description="Unit type (dona or blok)",
     *         required=true,
     *         @OA\Schema(type="string", enum={"dona", "blok"})
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             @OA\Property(property="price", type="integer", example=5000)
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Product not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="Product not found")
     *         )
     *     )
     * )
     */
    public function getProductPrice($productId, $saleType, $unit): JsonResponse
    {
        $product = Products::find($productId);
        if (!$product) {
            return response()->json(['status' => 'error', 'message' => 'Product not found'], 404);
        }

        $priceKey = $saleType === 'optom'
            ? ($unit === 'dona' ? 'optom_dona_narxi' : 'optom_blok_narxi')
            : ($unit === 'dona' ? 'toychi_dona_narxi' : 'toychi_blok_narxi');
        $price = $product->$priceKey;

        return response()->json(['price' => $price]);
    }
}
