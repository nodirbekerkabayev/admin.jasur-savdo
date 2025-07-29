<?php

namespace App\Http\Controllers;

use App\Models\Firm;
use App\Models\FirmDebt;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

/**
 * @OA\Tag(
 *     name="Firms",
 *     description="Operations about firms"
 * )
 */
class FirmController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/firms",
     *     tags={"Firms"},
     *     summary="Get paginated list of firms",
     *     description="Retrieve a paginated list of firms with optional name search",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="name",
     *         in="query",
     *         description="Search firms by name",
     *         required=false,
     *         @OA\Schema(type="string")
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
     *                     @OA\Property(property="name", type="string", example="Firma A"),
     *                     @OA\Property(property="supervisor", type="string", example="John Doe"),
     *                     @OA\Property(property="s_phone", type="string", nullable=true, example="+998901234567"),
     *                     @OA\Property(property="agent", type="string", example="Jane Smith"),
     *                     @OA\Property(property="a_phone", type="string", nullable=true, example="+998901234568"),
     *                     @OA\Property(property="currier", type="string", example="Bob Wilson"),
     *                     @OA\Property(property="c_phone", type="string", nullable=true, example="+998901234569"),
     *                     @OA\Property(property="humo", type="boolean", example=true),
     *                     @OA\Property(property="uzcard", type="boolean", example=false),
     *                     @OA\Property(property="day", type="string", example="Monday"),
     *                     @OA\Property(property="debt", type="string", example="70000"),
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
        $query = Firm::where('is_deleted', false);

        if ($request->has('name')) {
            $query->where('name', 'like', '%' . $request->input('name') . '%');
        }

        $firms = $query->paginate(10);

        return response()->json($firms, 200);
    }

    /**
     * @OA\Post(
     *     path="/api/firms",
     *     tags={"Firms"},
     *     summary="Create a new firm with initial debt",
     *     description="Store a new firm and its initial debt in the database",
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name", "supervisor", "agent", "currier", "day", "debt", "recorded_by"},
     *             @OA\Property(property="name", type="string", example="Firma A"),
     *             @OA\Property(property="supervisor", type="string", example="John Doe"),
     *             @OA\Property(property="s_phone", type="string", nullable=true, example="+998901234567"),
     *             @OA\Property(property="agent", type="string", example="Jane Smith"),
     *             @OA\Property(property="a_phone", type="string", nullable=true, example="+998901234568"),
     *             @OA\Property(property="currier", type="string", example="Bob Wilson"),
     *             @OA\Property(property="c_phone", type="string", nullable=true, example="+998901234569"),
     *             @OA\Property(property="humo", type="boolean", example=true),
     *             @OA\Property(property="uzcard", type="boolean", example=false),
     *             @OA\Property(property="day", type="string", example="Monday"),
     *             @OA\Property(property="debt", type="string", example="100000"),
     *             @OA\Property(property="recorded_by", type="string", example="Admin")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Firm created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(
     *                 property="firm",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="Firma A"),
     *                 @OA\Property(property="supervisor", type="string", example="John Doe"),
     *                 @OA\Property(property="s_phone", type="string", nullable=true, example="+998901234567"),
     *                 @OA\Property(property="agent", type="string", example="Jane Smith"),
     *                 @OA\Property(property="a_phone", type="string", nullable=true, example="+998901234568"),
     *                 @OA\Property(property="currier", type="string", example="Bob Wilson"),
     *                 @OA\Property(property="c_phone", type="string", nullable=true, example="+998901234569"),
     *                 @OA\Property(property="humo", type="boolean", example=true),
     *                 @OA\Property(property="uzcard", type="boolean", example=false),
     *                 @OA\Property(property="day", type="string", example="Monday"),
     *                 @OA\Property(property="debt", type="string", example="100000"),
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
            'name' => 'required|string|max:255',
            'supervisor' => 'required|string|max:255',
            's_phone' => 'nullable|string|max:20',
            'agent' => 'required|string|max:255',
            'a_phone' => 'nullable|string|max:20',
            'currier' => 'required|string|max:255',
            'c_phone' => 'nullable|string|max:20',
            'humo' => 'required|boolean',
            'uzcard' => 'required|boolean',
            'day' => 'required|string|max:255',
            'debt' => 'required|numeric|min:0',
            'recorded_by' => 'required|string|max:255',
        ]);

        $firm = Firm::create([
            'name' => $validated['name'],
            'supervisor' => $validated['supervisor'],
            's_phone' => $validated['s_phone'],
            'agent' => $validated['agent'],
            'a_phone' => $validated['a_phone'],
            'currier' => $validated['currier'],
            'c_phone' => $validated['c_phone'],
            'humo' => $validated['humo'],
            'uzcard' => $validated['uzcard'],
            'day' => $validated['day'],
            'debt' => $validated['debt'],
            'is_deleted' => false,
        ]);

        FirmDebt::create([
            'firm_id' => $firm->id,
            'amount' => $validated['debt'],
            'status' => 'oldi',
            'recorded_by' => $validated['recorded_by'],
        ]);

        return response()->json([
            'status' => 'success',
            'firm' => $firm,
        ], 201);
    }

    /**
     * @OA\Get(
     *     path="/api/firms/{id}",
     *     tags={"Firms"},
     *     summary="Get a specific firm with debt history",
     *     description="Retrieve a firm's details and their debt history by ID",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Firm ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="firm",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="Firma A"),
     *                 @OA\Property(property="supervisor", type="string", example="John Doe"),
     *                 @OA\Property(property="s_phone", type="string", nullable=true, example="+998901234567"),
     *                 @OA\Property(property="agent", type="string", example="Jane Smith"),
     *                 @OA\Property(property="a_phone", type="string", nullable=true, example="+998901234568"),
     *                 @OA\Property(property="currier", type="string", example="Bob Wilson"),
     *                 @OA\Property(property="c_phone", type="string", nullable=true, example="+998901234569"),
     *                 @OA\Property(property="humo", type="boolean", example=true),
     *                 @OA\Property(property="uzcard", type="boolean", example=false),
     *                 @OA\Property(property="day", type="string", example="Monday"),
     *                 @OA\Property(property="debt", type="string", example="70000"),
     *                 @OA\Property(property="is_deleted", type="boolean", example=false),
     *                 @OA\Property(property="created_at", type="string", format="date-time"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time")
     *             ),
     *             @OA\Property(
     *                 property="firm_debts",
     *                 type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="firm_id", type="integer", example=1),
     *                     @OA\Property(property="debt", type="string", example="100000"),
     *                     @OA\Property(property="status", type="string", example="oldi"),
     *                     @OA\Property(property="recorded_by", type="string", nullable=true, example="Admin"),
     *                     @OA\Property(property="created_at", type="string", format="date-time"),
     *                     @OA\Property(property="updated_at", type="string", format="date-time")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Firm not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="Firm not found")
     *         )
     *     )
     * )
     */
    public function show($id): JsonResponse
    {
        $firm = Firm::with(['firmDebts' => function ($query) {
            $query->orderBy('created_at', 'desc');
        }])->where('is_deleted', false)->find($id);

        if (!$firm) {
            return response()->json([
                'status' => 'error',
                'message' => 'Firm not found',
            ], 404);
        }

        return response()->json([
            'firm' => $firm,
        ], 200);
    }

    /**
     * @OA\Put(
     *     path="/api/firms/{id}",
     *     tags={"Firms"},
     *     summary="Update a firm",
     *     description="Update a firm's details by ID",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Firm ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string", example="Firma A"),
     *             @OA\Property(property="supervisor", type="string", example="John Doe"),
     *             @OA\Property(property="s_phone", type="string", nullable=true, example="+998901234567"),
     *             @OA\Property(property="agent", type="string", example="Jane Smith"),
     *             @OA\Property(property="a_phone", type="string", nullable=true, example="+998901234568"),
     *             @OA\Property(property="currier", type="string", example="Bob Wilson"),
     *             @OA\Property(property="c_phone", type="string", nullable=true, example="+998901234569"),
     *             @OA\Property(property="humo", type="boolean", example=true),
     *             @OA\Property(property="uzcard", type="boolean", example=false),
     *             @OA\Property(property="day", type="string", example="Monday")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Firm updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(
     *                 property="firm",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="Firma A"),
     *                 @OA\Property(property="supervisor", type="string", example="John Doe"),
     *                 @OA\Property(property="s_phone", type="string", nullable=true, example="+998901234567"),
     *                 @OA\Property(property="agent", type="string", example="Jane Smith"),
     *                 @OA\Property(property="a_phone", type="string", nullable=true, example="+998901234568"),
     *                 @OA\Property(property="currier", type="string", example="Bob Wilson"),
     *                 @OA\Property(property="c_phone", type="string", nullable=true, example="+998901234569"),
     *                 @OA\Property(property="humo", type="boolean", example=true),
     *                 @OA\Property(property="uzcard", type="boolean", example=false),
     *                 @OA\Property(property="day", type="string", example="Monday"),
     *                 @OA\Property(property="debt", type="string", example="70000"),
     *                 @OA\Property(property="is_deleted", type="boolean", example=false),
     *                 @OA\Property(property="created_at", type="string", format="date-time"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Firm not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="Firm not found")
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
        $firm = Firm::where('is_deleted', false)->find($id);

        if (!$firm) {
            return response()->json([
                'status' => 'error',
                'message' => 'Firm not found',
            ], 404);
        }

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'supervisor' => 'sometimes|string|max:255',
            's_phone' => 'nullable|string|max:20',
            'agent' => 'sometimes|string|max:255',
            'a_phone' => 'nullable|string|max:20',
            'currier' => 'sometimes|string|max:255',
            'c_phone' => 'nullable|string|max:20',
            'humo' => 'sometimes|boolean',
            'uzcard' => 'sometimes|boolean',
            'day' => 'sometimes|string|max:255',
        ]);

        $firm->update($validated);

        return response()->json([
            'status' => 'success',
            'firm' => $firm,
        ], 200);
    }

    /**
     * @OA\Delete(
     *     path="/api/firms/{id}",
     *     tags={"Firms"},
     *     summary="Soft delete a firm",
     *     description="Mark a firm as deleted by setting is_deleted to true",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Firm ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Firm marked as deleted",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Firm marked as deleted")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Firm not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="Firm not found")
     *         )
     *     )
     * )
     */
    public function destroy($id): JsonResponse
    {
        $firm = Firm::where('is_deleted', false)->find($id);

        if (!$firm) {
            return response()->json([
                'status' => 'error',
                'message' => 'Firm not found',
            ], 404);
        }

        $firm->update(['is_deleted' => true]);

        return response()->json([
            'status' => 'success',
            'message' => 'Firm marked as deleted',
        ], 200);
    }

    /**
     * @OA\Post(
     *     path="/api/firms/{id}/change-debt",
     *     tags={"Firms"},
     *     summary="Add a debt transaction for a firm",
     *     description="Add a new debt or payment transaction for a firm",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Firm ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"debt", "status"},
     *             @OA\Property(property="debt", type="string", example="50000"),
     *             @OA\Property(property="status", type="string", enum={"oldi", "berdi"}, example="berdi"),
     *             @OA\Property(property="recorded_by", type="string", nullable=true, example="Admin")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Debt transaction added successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(
     *                 property="firm_debt",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="firm_id", type="integer", example=1),
     *                 @OA\Property(property="debt", type="string", example="50000"),
     *                 @OA\Property(property="status", type="string", example="berdi"),
     *                 @OA\Property(property="recorded_by", type="string", nullable=true, example="Admin"),
     *                 @OA\Property(property="created_at", type="string", format="date-time"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Firm not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="Firm not found")
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
    public function changeDebt(Request $request, $id): JsonResponse
    {
        $firm = Firm::where('is_deleted', false)->find($id);

        if (!$firm) {
            return response()->json([
                'status' => 'error',
                'message' => 'Firm not found',
            ], 404);
        }

        $validated = $request->validate([
            'debt' => 'required|numeric|min:0',
            'status' => 'required|in:oldi,berdi',
            'recorded_by' => 'nullable|string|max:255',
        ]);

        $firmDebt = FirmDebt::create([
            'firm_id' => $firm->id,
            'debt' => $validated['debt'],
            'status' => $validated['status'],
            'recorded_by' => $validated['recorded_by'],
        ]);

        return response()->json([
            'status' => 'success',
            'firm_debt' => $firmDebt,
        ], 201);
    }
}
