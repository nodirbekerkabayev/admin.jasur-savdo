<?php

namespace App\Http\Controllers;

use App\Models\Worker;
use App\Models\WorkerPay;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

/**
 * @OA\Tag(
 *     name="Workers",
 *     description="Operations about workers"
 * )
 */
class WorkerController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/workers",
     *     tags={"Workers"},
     *     summary="Get list of workers",
     *     description="Retrieve a list of workers with optional name search",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="name",
     *         in="query",
     *         description="Search workers by name",
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
     *                 @OA\Property(property="amount", type="integer", example=50000),
     *                 @OA\Property(property="day", type="string", format="date", example="2025-07-01"),
     *                 @OA\Property(property="status", type="string", example="ishlayabdi"),
     *                 @OA\Property(property="image", type="string", nullable=true, example="workers/ali.jpg"),
     *                 @OA\Property(property="summa", type="integer", example=1450000),
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
        $query = Worker::query();

        if ($request->has('name')) {
            $query->where('name', 'like', '%' . $request->input('name') . '%');
        }

        $workers = $query->get();

        return response()->json($workers, 200);
    }

    /**
     * @OA\Post(
     *     path="/api/workers",
     *     tags={"Workers"},
     *     summary="Create a new worker",
     *     description="Store a new worker in the database",
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 required={"name", "phone", "amount", "day"},
     *                 @OA\Property(property="name", type="string", example="Ali"),
     *                 @OA\Property(property="phone", type="string", example="+998901234567"),
     *                 @OA\Property(property="amount", type="integer", example=50000),
     *                 @OA\Property(property="day", type="string", format="date", example="2025-07-01"),
     *                 @OA\Property(property="image", type="file", description="Worker image")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Worker created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(
     *                 property="worker",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="Ali"),
     *                 @OA\Property(property="phone", type="string", example="+998901234567"),
     *                 @OA\Property(property="amount", type="integer", example=50000),
     *                 @OA\Property(property="day", type="string", format="date", example="2025-07-01"),
     *                 @OA\Property(property="status", type="string", example="ishlayabdi"),
     *                 @OA\Property(property="image", type="string", nullable=true, example="workers/ali.jpg"),
     *                 @OA\Property(property="summa", type="integer", example=0),
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
            'phone' => 'required|string|max:255',
            'amount' => 'required|integer|min:0',
            'day' => 'required|date',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('workers', 'public');
            $validated['image'] = $imagePath;
        }

        $worker = Worker::create(array_merge($validated, [
            'status' => 'ishlayabdi',
            'summa' => 0,
        ]));

        return response()->json([
            'status' => 'success',
            'worker' => $worker,
        ], 201);
    }

    /**
     * @OA\Get(
     *     path="/api/workers/{id}",
     *     tags={"Workers"},
     *     summary="Get a specific worker with pays",
     *     description="Retrieve a worker's details and their payment history by ID",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Worker ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="worker",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="Ali"),
     *                 @OA\Property(property="phone", type="string", example="+998901234567"),
     *                 @OA\Property(property="amount", type="integer", example=50000),
     *                 @OA\Property(property="day", type="string", format="date", example="2025-07-01"),
     *                 @OA\Property(property="status", type="string", example="ishlayabdi"),
     *                 @OA\Property(property="image", type="string", nullable=true, example="workers/ali.jpg"),
     *                 @OA\Property(property="summa", type="integer", example=1450000),
     *                 @OA\Property(property="created_at", type="string", format="date-time"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time")
     *             ),
     *             @OA\Property(
     *                 property="pays",
     *                 type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="worker_id", type="integer", example=1),
     *                     @OA\Property(property="amount", type="integer", example=1000000),
     *                     @OA\Property(property="status", type="string", example="oldi"),
     *                     @OA\Property(property="created_at", type="string", format="date-time"),
     *                     @OA\Property(property="updated_at", type="string", format="date-time")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Worker not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="Worker not found")
     *         )
     *     )
     * )
     */
    public function show($id): JsonResponse
    {
        $worker = Worker::with(['pays' => function ($query) {
            $query->orderBy('created_at', 'desc');
        }])->find($id);

        if (!$worker) {
            return response()->json([
                'status' => 'error',
                'message' => 'Worker not found',
            ], 404);
        }

        return response()->json([
            'worker' => $worker,
            'pays' => $worker->pays,
        ], 200);
    }

    /**
     * @OA\Post(
     *     path="/api/workers/{id}",
     *     tags={"Workers"},
     *     summary="Update a worker",
     *     description="Update a worker's details by ID",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Worker ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property(property="name", type="string", example="Ali"),
     *                 @OA\Property(property="phone", type="string", example="+998901234567"),
     *                 @OA\Property(property="amount", type="integer", example=50000),
     *                 @OA\Property(property="day", type="string", format="date", example="2025-07-01"),
     *                 @OA\Property(property="status", type="string", example="ishlayabdi"),
     *                 @OA\Property(property="image", type="file", description="Worker image")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Worker updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(
     *                 property="worker",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="Ali"),
     *                 @OA\Property(property="phone", type="string", example="+998901234567"),
     *                 @OA\Property(property="amount", type="integer", example=50000),
     *                 @OA\Property(property="day", type="string", format="date", example="2025-07-01"),
     *                 @OA\Property(property="status", type="string", example="ishlayabdi"),
     *                 @OA\Property(property="image", type="string", nullable=true, example="workers/ali.jpg"),
     *                 @OA\Property(property="summa", type="integer", example=1450000),
     *                 @OA\Property(property="created_at", type="string", format="date-time"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Worker not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="Worker not found")
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
        $worker = Worker::find($id);

        if (!$worker) {
            return response()->json([
                'status' => 'error',
                'message' => 'Worker not found',
            ], 404);
        }

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'phone' => 'sometimes|string|max:255',
            'amount' => 'sometimes|integer|min:0',
            'day' => 'sometimes|date',
            'status' => 'sometimes|string|in:ishlayabdi,ishlamayabdi',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($request->hasFile('image')) {
            if ($worker->image) {
                Storage::disk('public')->delete($worker->image);
            }
            $imagePath = $request->file('image')->store('workers', 'public');
            $validated['image'] = $imagePath;
        }

        $worker->update($validated);

        return response()->json([
            'status' => 'success',
            'worker' => $worker,
        ], 200);
    }

    /**
     * @OA\Delete(
     *     path="/api/workers/{id}",
     *     tags={"Workers"},
     *     summary="Delete a worker",
     *     description="Permanently delete a worker and their pays by ID",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Worker ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Worker deleted successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Worker deleted")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Worker not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="Worker not found")
     *         )
     *     )
     * )
     */
    public function destroy($id): JsonResponse
    {
        $worker = Worker::find($id);

        if (!$worker) {
            return response()->json([
                'status' => 'error',
                'message' => 'Worker not found',
            ], 404);
        }

        if ($worker->image) {
            Storage::disk('public')->delete($worker->image);
        }

        $worker->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Worker deleted',
        ], 200);
    }

    /**
     * @OA\Post(
     *     path="/api/workers/{id}/pays",
     *     tags={"Workers"},
     *     summary="Add a payment or debt for a worker",
     *     description="Record a payment (oldi) or debt (berdi) for a worker",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Worker ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"amount", "status"},
     *             @OA\Property(property="amount", type="integer", example=1000000),
     *             @OA\Property(property="status", type="string", example="oldi", enum={"oldi", "berdi"})
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Payment recorded successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(
     *                 property="pay",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="worker_id", type="integer", example=1),
     *                 @OA\Property(property="amount", type="integer", example=1000000),
     *                 @OA\Property(property="status", type="string", example="oldi"),
     *                 @OA\Property(property="created_at", type="string", format="date-time"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time")
     *             ),
     *             @OA\Property(
     *                 property="worker",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="summa", type="integer", example=450000)
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Worker not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="Worker not found")
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
    public function changePay(Request $request, $id): JsonResponse
    {
        $worker = Worker::find($id);

        if (!$worker) {
            return response()->json([
                'status' => 'error',
                'message' => 'Worker not found',
            ], 404);
        }

        $validated = $request->validate([
            'amount' => 'required|integer|min:0',
            'status' => 'required|string|in:oldi,berdi',
        ]);

        $pay = WorkerPay::create([
            'worker_id' => $worker->id,
            'amount' => $validated['amount'],
            'status' => $validated['status'],
        ]);

        // Update summa based on oldi/berdi
        if ($validated['status'] === 'oldi') {
            $worker->summa -= $validated['amount'];
        } else {
            $worker->summa += $validated['amount'];
        }

        $worker->save();

        return response()->json([
            'status' => 'success',
            'pay' => $pay,
            'worker' => $worker,
        ], 201);
    }
}
