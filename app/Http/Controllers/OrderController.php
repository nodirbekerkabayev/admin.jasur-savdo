<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

/**
 * @OA\Tag(
 *     name="Orders",
 *     description="Operations about orders"
 * )
 */
class OrderController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/orders",
     *     tags={"Orders"},
     *     summary="Get paginated list of orders",
     *     description="Retrieve a paginated list of orders with optional date filter",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="day",
     *         in="query",
     *         description="Filter orders by date",
     *         required=false,
     *         @OA\Schema(type="string", format="date")
     *     ),
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Page number for pagination",
     *         required=false,
     *         @OA\Schema(type="integer", default=1)
     *     ),
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         description="Number of items per page",
     *         required=false,
     *         @OA\Schema(type="integer", default=10)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="firm_id", type="integer", example=1),
     *                     @OA\Property(property="day", type="string", format="date", example="2025-07-28"),
     *                     @OA\Property(property="recorded_by", type="string", nullable=true, example="Admin"),
     *                     @OA\Property(property="is_deleted", type="boolean", example=false),
     *                     @OA\Property(property="created_at", type="string", format="date-time"),
     *                     @OA\Property(property="updated_at", type="string", format="date-time")
     *                 )
     *             ),
     *             @OA\Property(property="current_page", type="integer", example=1),
     *             @OA\Property(property="per_page", type="integer", example=10),
     *             @OA\Property(property="total", type="integer", example=100),
     *             @OA\Property(property="last_page", type="integer", example=10)
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
        $query = Order::where('is_deleted', false);

        if ($request->has('day')) {
            $query->whereDate('day', $request->input('day'));
        }

        $orders = $query->orderBy('day', 'desc')->paginate(10);

        return response()->json($orders, 200);
    }

    /**
     * @OA\Post(
     *     path="/api/orders",
     *     tags={"Orders"},
     *     summary="Create a new order",
     *     description="Store a new order in the database",
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"firm_id", "day", "recorded_by"},
     *             @OA\Property(property="firm_id", type="integer", example=1),
     *             @OA\Property(property="day", type="string", format="date", example="2025-07-28"),
     *             @OA\Property(property="recorded_by", type="string", example="Admin")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Order created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(
     *                 property="order",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="firm_id", type="integer", example=1),
     *                 @OA\Property(property="day", type="string", format="date", example="2025-07-28"),
     *                 @OA\Property(property="recorded_by", type="string", example="Admin"),
     *                 @OA\Property(property="is_deleted", type="boolean", example=false),
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
            'firm_id' => 'required|exists:firms,id',
            'day' => 'required|date',
            'recorded_by' => 'required|string|max:255',
        ]);

        $order = Order::create(array_merge($validated, ['is_deleted' => false]));

        return response()->json([
            'status' => 'success',
            'order' => $order,
        ], 201);
    }

    /**
     * @OA\Get(
     *     path="/api/orders/{id}",
     *     tags={"Orders"},
     *     summary="Get a specific order with products",
     *     description="Retrieve an order's details and its products by ID",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Order ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="order",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="firm_id", type="integer", example=1),
     *                 @OA\Property(property="day", type="string", format="date", example="2025-07-28"),
     *                 @OA\Property(property="recorded_by", type="string", example="Admin"),
     *                 @OA\Property(property="is_deleted", type="boolean", example=false),
     *                 @OA\Property(property="created_at", type="string", format="date-time"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time")
     *             ),
     *             @OA\Property(
     *                 property="products",
     *                 type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="order_id", type="integer", example=1),
     *                     @OA\Property(property="name", type="string", example="Mahsulot X"),
     *                     @OA\Property(property="karobkadagi_soni", type="string", example="50"),
     *                     @OA\Property(property="necha_karobka_kelgani", type="string", example="10"),
     *                     @OA\Property(property="kelgan_narxi_dona", type="string", example="100"),
     *                     @OA\Property(property="kelgan_narxi_blok", type="string", example="5000"),
     *                     @OA\Property(property="sotish_narxi_dona", type="string", example="150"),
     *                     @OA\Property(property="sotish_narxi_blok", type="string", example="7500"),
     *                     @OA\Property(property="sotish_narxi_optom_dona", type="string", example="120"),
     *                     @OA\Property(property="sotish_narxi_optom_blok", type="string", example="6000"),
     *                     @OA\Property(property="sotish_narxi_toyga_dona", type="string", example="130"),
     *                     @OA\Property(property="sotish_narxi_toyga_blok", type="string", example="6500"),
     *                     @OA\Property(property="created_at", type="string", format="date-time"),
     *                     @OA\Property(property="updated_at", type="string", format="date-time")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Order not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="Order not found")
     *         )
     *     )
     * )
     */
    public function show($id): JsonResponse
    {
        $order = Order::with(['products' => function ($query) {
            $query->orderBy('created_at', 'desc');
        }])->where('is_deleted', false)->find($id);

        if (!$order) {
            return response()->json([
                'status' => 'error',
                'message' => 'Order not found',
            ], 404);
        }

        return response()->json([
            'order' => $order,
            'products' => $order->products,
        ], 200);
    }

    /**
     * @OA\Put(
     *     path="/api/orders/{id}",
     *     tags={"Orders"},
     *     summary="Update an order",
     *     description="Update an order's day and recorded_by fields by ID",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Order ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="day", type="string", format="date", example="2025-07-28"),
     *             @OA\Property(property="recorded_by", type="string", example="Admin")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Order updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(
     *                 property="order",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="firm_id", type="integer", example=1),
     *                 @OA\Property(property="day", type="string", format="date", example="2025-07-28"),
     *                 @OA\Property(property="recorded_by", type="string", example="Admin"),
     *                 @OA\Property(property="is_deleted", type="boolean", example=false),
     *                 @OA\Property(property="created_at", type="string", format="date-time"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Order not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="Order not found")
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
        $order = Order::where('is_deleted', false)->find($id);

        if (!$order) {
            return response()->json([
                'status' => 'error',
                'message' => 'Order not found',
            ], 404);
        }

        $validated = $request->validate([
            'day' => 'sometimes|date',
            'recorded_by' => 'sometimes|string|max:255',
        ]);

        $order->update($validated);

        return response()->json([
            'status' => 'success',
            'order' => $order,
        ], 200);
    }

    /**
     * @OA\Delete(
     *     path="/api/orders/{id}",
     *     tags={"Orders"},
     *     summary="Soft delete an order",
     *     description="Mark an order as deleted by setting is_deleted to true",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Order ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Order marked as deleted",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Order marked as deleted")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Order not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="Order not found")
     *         )
     *     )
     * )
     */
    public function destroy($id): JsonResponse
    {
        $order = Order::where('is_deleted', false)->find($id);

        if (!$order) {
            return response()->json([
                'status' => 'error',
                'message' => 'Order not found',
            ], 404);
        }

        $order->update(['is_deleted' => true]);

        return response()->json([
            'status' => 'success',
            'message' => 'Order marked as deleted',
        ], 200);
    }
}
