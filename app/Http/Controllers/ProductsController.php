<?php

namespace App\Http\Controllers;

use App\Models\Products;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

/**
 * @OA\Tag(
 *     name="Products",
 *     description="Operations about products"
 * )
 */
class ProductsController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/products",
     *     tags={"Products"},
     *     summary="Get list of products",
     *     description="Retrieve a list of products with optional name search",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="name",
     *         in="query",
     *         description="Search products by name",
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
     *                 @OA\Property(property="order_id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="Mahsulot X"),
     *                 @OA\Property(property="karobkadagi_soni", type="string", example="50"),
     *                 @OA\Property(property="necha_karobka_kelgani", type="string", example="10"),
     *                 @OA\Property(property="kelgan_narxi_dona", type="string", example="100"),
     *                 @OA\Property(property="kelgan_narxi_blok", type="string", example="5000"),
     *                 @OA\Property(property="sotish_narxi_dona", type="string", example="150"),
     *                 @OA\Property(property="sotish_narxi_blok", type="string", example="7500"),
     *                 @OA\Property(property="sotish_narxi_optom_dona", type="string", example="120"),
     *                 @OA\Property(property="sotish_narxi_optom_blok", type="string", example="6000"),
     *                 @OA\Property(property="sotish_narxi_toyga_dona", type="string", example="130"),
     *                 @OA\Property(property="sotish_narxi_toyga_blok", type="string", example="6500"),
     *                 @OA\Property(property="created_at", type="string", format="date-time"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="Unauthorized")
     *         )
     *     )
     * )
     */
    public function index(Request $request): JsonResponse
    {
        $query = Products::query();

        if ($request->has('name')) {
            $query->where('name', 'like', '%' . $request->input('name') . '%');
        }

        $products = $query->get();

        return response()->json($products, 200);
    }

    /**
     * @OA\Post(
     *     path="/api/products",
     *     tags={"Products"},
     *     summary="Create a new product",
     *     description="Store a new product in the database",
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"order_id", "name", "karobkadagi_soni", "necha_karobka_kelgani", "kelgan_narxi_dona", "kelgan_narxi_blok", "sotish_narxi_dona", "sotish_narxi_blok", "sotish_narxi_optom_dona", "sotish_narxi_optom_blok", "sotish_narxi_toyga_dona", "sotish_narxi_toyga_blok"},
     *             @OA\Property(property="order_id", type="integer", example=1),
     *             @OA\Property(property="name", type="string", example="Mahsulot X"),
     *             @OA\Property(property="karobkadagi_soni", type="string", example="50"),
     *             @OA\Property(property="necha_karobka_kelgani", type="string", example="10"),
     *             @OA\Property(property="kelgan_narxi_dona", type="string", example="100"),
     *             @OA\Property(property="kelgan_narxi_blok", type="string", example="5000"),
     *             @OA\Property(property="sotish_narxi_dona", type="string", example="150"),
     *             @OA\Property(property="sotish_narxi_blok", type="string", example="7500"),
     *             @OA\Property(property="sotish_narxi_optom_dona", type="string", example="120"),
     *             @OA\Property(property="sotish_narxi_optom_blok", type="string", example="6000"),
     *             @OA\Property(property="sotish_narxi_toyga_dona", type="string", example="130"),
     *             @OA\Property(property="sotish_narxi_toyga_blok", type="string", example="6500")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Product created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(
     *                 property="product",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="order_id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="Mahsulot X"),
     *                 @OA\Property(property="karobkadagi_soni", type="string", example="50"),
     *                 @OA\Property(property="necha_karobka_kelgani", type="string", example="10"),
     *                 @OA\Property(property="kelgan_narxi_dona", type="string", example="100"),
     *                 @OA\Property(property="kelgan_narxi_blok", type="string", example="5000"),
     *                 @OA\Property(property="sotish_narxi_dona", type="string", example="150"),
     *                 @OA\Property(property="sotish_narxi_blok", type="string", example="7500"),
     *                 @OA\Property(property="sotish_narxi_optom_dona", type="string", example="120"),
     *                 @OA\Property(property="sotish_narxi_optom_blok", type="string", example="6000"),
     *                 @OA\Property(property="sotish_narxi_toyga_dona", type="string", example="130"),
     *                 @OA\Property(property="sotish_narxi_toyga_blok", type="string", example="6500"),
     *                 @OA\Property(property="created_at", type="string", format="date-time"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time")
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
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'order_id' => 'required|exists:orders,id',
            'name' => 'required|string|max:255',
            'karobkadagi_soni' => 'required|string|max:255',
            'necha_karobka_kelgani' => 'required|string|max:255',
            'kelgan_narxi_dona' => 'required|string|max:255',
            'kelgan_narxi_blok' => 'required|string|max:255',
            'sotish_narxi_dona' => 'required|string|max:255',
            'sotish_narxi_blok' => 'required|string|max:255',
            'sotish_narxi_optom_dona' => 'required|string|max:255',
            'sotish_narxi_optom_blok' => 'required|string|max:255',
            'sotish_narxi_toyga_dona' => 'required|string|max:255',
            'sotish_narxi_toyga_blok' => 'required|string|max:255',
        ]);

        $product = Products::create($validated);

        return response()->json([
            'status' => 'success',
            'product' => $product,
        ], 201);
    }

    /**
     * @OA\Get(
     *     path="/api/products/{id}",
     *     tags={"Products"},
     *     summary="Get a specific product",
     *     description="Retrieve a product's details by ID",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Product ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="order_id", type="integer", example=1),
     *             @OA\Property(property="name", type="string", example="Mahsulot X"),
     *             @OA\Property(property="karobkadagi_soni", type="string", example="50"),
     *             @OA\Property(property="necha_karobka_kelgani", type="string", example="10"),
     *             @OA\Property(property="kelgan_narxi_dona", type="string", example="100"),
     *             @OA\Property(property="kelgan_narxi_blok", type="string", example="5000"),
     *             @OA\Property(property="sotish_narxi_dona", type="string", example="150"),
     *             @OA\Property(property="sotish_narxi_blok", type="string", example="7500"),
     *             @OA\Property(property="sotish_narxi_optom_dona", type="string", example="120"),
     *             @OA\Property(property="sotish_narxi_optom_blok", type="string", example="6000"),
     *             @OA\Property(property="sotish_narxi_toyga_dona", type="string", example="130"),
     *             @OA\Property(property="sotish_narxi_toyga_blok", type="string", example="6500"),
     *             @OA\Property(property="created_at", type="string", format="date-time"),
     *             @OA\Property(property="updated_at", type="string", format="date-time")
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
    public function show($id): JsonResponse
    {
        $product = Products::find($id);

        if (!$product) {
            return response()->json([
                'status' => 'error',
                'message' => 'Product not found',
            ], 404);
        }

        return response()->json($product, 200);
    }

    /**
     * @OA\Put(
     *     path="/api/products/{id}",
     *     tags={"Products"},
     *     summary="Update a product",
     *     description="Update a product's details by ID",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Product ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="order_id", type="integer", example=1),
     *             @OA\Property(property="name", type="string", example="Mahsulot X"),
     *             @OA\Property(property="karobkadagi_soni", type="string", example="50"),
     *             @OA\Property(property="necha_karobka_kelgani", type="string", example="10"),
     *             @OA\Property(property="kelgan_narxi_dona", type="string", example="100"),
     *             @OA\Property(property="kelgan_narxi_blok", type="string", example="5000"),
     *             @OA\Property(property="sotish_narxi_dona", type="string", example="150"),
     *             @OA\Property(property="sotish_narxi_blok", type="string", example="7500"),
     *             @OA\Property(property="sotish_narxi_optom_dona", type="string", example="120"),
     *             @OA\Property(property="sotish_narxi_optom_blok", type="string", example="6000"),
     *             @OA\Property(property="sotish_narxi_toyga_dona", type="string", example="130"),
     *             @OA\Property(property="sotish_narxi_toyga_blok", type="string", example="6500")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Product updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(
     *                 property="product",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="order_id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="Mahsulot X"),
     *                 @OA\Property(property="karobkadagi_soni", type="string", example="50"),
     *                 @OA\Property(property="necha_karobka_kelgani", type="string", example="10"),
     *                 @OA\Property(property="kelgan_narxi_dona", type="string", example="100"),
     *                 @OA\Property(property="kelgan_narxi_blok", type="string", example="5000"),
     *                 @OA\Property(property="sotish_narxi_dona", type="string", example="150"),
     *                 @OA\Property(property="sotish_narxi_blok", type="string", example="7500"),
     *                 @OA\Property(property="sotish_narxi_optom_dona", type="string", example="120"),
     *                 @OA\Property(property="sotish_narxi_optom_blok", type="string", example="6000"),
     *                 @OA\Property(property="sotish_narxi_toyga_dona", type="string", example="130"),
     *                 @OA\Property(property="sotish_narxi_toyga_blok", type="string", example="6500"),
     *                 @OA\Property(property="created_at", type="string", format="date-time"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Product not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="Product not found")
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
    public function update(Request $request, $id): JsonResponse
    {
        $product = Products::find($id);

        if (!$product) {
            return response()->json([
                'status' => 'error',
                'message' => 'Product not found',
            ], 404);
        }

        $validated = $request->validate([
            'order_id' => 'sometimes|exists:orders,id',
            'name' => 'sometimes|string|max:255',
            'karobkadagi_soni' => 'sometimes|string|max:255',
            'necha_karobka_kelgani' => 'sometimes|string|max:255',
            'kelgan_narxi_dona' => 'sometimes|string|max:255',
            'kelgan_narxi_blok' => 'sometimes|string|max:255',
            'sotish_narxi_dona' => 'sometimes|string|max:255',
            'sotish_narxi_blok' => 'sometimes|string|max:255',
            'sotish_narxi_optom_dona' => 'sometimes|string|max:255',
            'sotish_narxi_optom_blok' => 'sometimes|string|max:255',
            'sotish_narxi_toyga_dona' => 'sometimes|string|max:255',
            'sotish_narxi_toyga_blok' => 'sometimes|string|max:255',
        ]);

        $product->update($validated);

        return response()->json([
            'status' => 'success',
            'product' => $product,
        ], 200);
    }

    /**
     * @OA\Delete(
     *     path="/api/products/{id}",
     *     tags={"Products"},
     *     summary="Delete a product",
     *     description="Permanently delete a product by ID",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Product ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Product deleted successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Product deleted")
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
    public function destroy($id): JsonResponse
    {
        $product = Products::find($id);

        if (!$product) {
            return response()->json([
                'status' => 'error',
                'message' => 'Product not found',
            ], 404);
        }

        $product->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Product deleted',
        ], 200);
    }
}
