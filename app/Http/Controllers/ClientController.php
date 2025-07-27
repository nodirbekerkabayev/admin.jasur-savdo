<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class ClientController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/clients",
     *     tags={"Clients"},
     *     summary="Get list of clients",
     *     description="Retrieve a list of clients with optional search and filter",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="name",
     *         in="query",
     *         description="Search clients by name",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="phone",
     *         in="query",
     *         description="Search clients by phone number",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="is_deleted",
     *         in="query",
     *         description="Filter clients by deletion status (true/false)",
     *         required=false,
     *         @OA\Schema(type="boolean", default=false)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="John Doe"),
     *                 @OA\Property(property="info", type="string", example="Client information"),
     *                 @OA\Property(property="phone", type="string", example="+998901234567"),
     *                 @OA\Property(property="image", type="string", nullable=true, example="null"),
     *                 @OA\Property(property="debt", type="string", example="100.00"),
     *                 @OA\Property(property="recorded_by", type="string", example="Admin"),
     *                 @OA\Property(property="is_deleted", type="boolean", example=false),
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
        $query = Client::query();

        // Search by name
        if ($request->has('name')) {
            $query->where('name', 'like', '%' . $request->input('name') . '%');
        }

        // Search by phone
        if ($request->has('phone')) {
            $query->where('phone', 'like', '%' . $request->input('phone') . '%');
        }

        // Filter by is_deleted
        if ($request->has('is_deleted')) {
            $query->where('is_deleted', $request->input('is_deleted', false));
        } else {
            $query->where('is_deleted', false); // Default to non-deleted clients
        }

        $clients = $query->get();

        return response()->json($clients, 200);
    }

    /**
     * @OA\Post(
     *     path="/api/clients",
     *     tags={"Clients"},
     *     summary="Create a new client",
     *     description="Store a new client in the database",
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name", "info", "phone", "debt", "recorded_by"},
     *             @OA\Property(property="name", type="string", example="John Doe"),
     *             @OA\Property(property="info", type="string", example="Client information"),
     *             @OA\Property(property="phone", type="string", example="+998901234567"),
     *             @OA\Property(property="image", type="string", nullable=true, example="null"),
     *             @OA\Property(property="debt", type="string", example="100.00"),
     *             @OA\Property(property="recorded_by", type="string", example="Admin")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Client created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="client", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="John Doe"),
     *                 @OA\Property(property="info", type="string", example="Client information"),
     *                 @OA\Property(property="phone", type="string", example="+998901234567"),
     *                 @OA\Property(property="image", type="string", nullable=true, example="null"),
     *                 @OA\Property(property="debt", type="string", example="100.00"),
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
            'name' => 'required|string|max:255',
            'info' => 'required|string',
            'phone' => 'required|string|max:20',
            'image' => 'nullable|string',
            'debt' => 'required|string',
            'recorded_by' => 'required|string|max:255',
        ]);

        $client = Client::create($validated);

        return response()->json([
            'status' => 'success',
            'client' => $client,
        ], 201);
    }

    /**
     * @OA\Get(
     *     path="/api/clients/{id}",
     *     tags={"Clients"},
     *     summary="Get a specific client",
     *     description="Retrieve a client by ID",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Client ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="name", type="string", example="John Doe"),
     *             @OA\Property(property="info", type="string", example="Client information"),
     *             @OA\Property(property="phone", type="string", example="+998901234567"),
     *             @OA\Property(property="image", type="string", nullable=true, example="null"),
     *             @OA\Property(property="debt", type="string", example="100.00"),
     *             @OA\Property(property="recorded_by", type="string", example="Admin"),
     *             @OA\Property(property="is_deleted", type="boolean", example=false),
     *             @OA\Property(property="created_at", type="string", format="date-time"),
     *             @OA\Property(property="updated_at", type="string", format="date-time")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Client not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="Client not found")
     *         )
     *     )
     * )
     */
    public function show($id): JsonResponse
    {
        $client = Client::find($id);

        if (!$client) {
            return response()->json([
                'status' => 'error',
                'message' => 'Client not found',
            ], 404);
        }

        return response()->json($client, 200);
    }

    /**
     * @OA\Put(
     *     path="/api/clients/{id}",
     *     tags={"Clients"},
     *     summary="Update a client",
     *     description="Update client details by ID",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Client ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name", "info", "phone", "debt", "recorded_by"},
     *             @OA\Property(property="name", type="string", example="John Doe"),
     *             @OA\Property(property="info", type="string", example="Updated client info"),
     *             @OA\Property(property="phone", type="string", example="+998901234567"),
     *             @OA\Property(property="image", type="string", nullable=true, example="null"),
     *             @OA\Property(property="debt", type="string", example="200.00"),
     *             @OA\Property(property="recorded_by", type="string", example="Admin")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Client updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="client", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="John Doe"),
     *                 @OA\Property(property="info", type="string", example="Updated client info"),
     *                 @OA\Property(property="phone", type="string", example="+998901234567"),
     *                 @OA\Property(property="image", type="string", nullable=true, example="null"),
     *                 @OA\Property(property="debt", type="string", example="200.00"),
     *                 @OA\Property(property="recorded_by", type="string", example="Admin"),
     *                 @OA\Property(property="is_deleted", type="boolean", example=false),
     *                 @OA\Property(property="created_at", type="string", format="date-time"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Client not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="Client not found")
     *         )
     *     )
     * )
     */
    public function update(Request $request, $id): JsonResponse
    {
        $client = Client::find($id);

        if (!$client) {
            return response()->json([
                'status' => 'error',
                'message' => 'Client not found',
            ], 404);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'info' => 'required|string',
            'phone' => 'required|string|max:20',
            'image' => 'nullable|string',
            'debt' => 'required|string',
            'recorded_by' => 'required|string|max:255',
        ]);

        $client->update($validated);

        return response()->json([
            'status' => 'success',
            'client' => $client,
        ], 200);
    }

    /**
     * @OA\Delete(
     *     path="/api/clients/{id}",
     *     tags={"Clients"},
     *     summary="Soft delete a client",
     *     description="Mark a client as deleted by setting is_deleted to true",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Client ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Client marked as deleted",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Client marked as deleted")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Client not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="Client not found")
     *         )
     *     )
     * )
     */
    public function destroy($id): JsonResponse
    {
        $client = Client::find($id);

        if (!$client) {
            return response()->json([
                'status' => 'error',
                'message' => 'Client not found',
            ], 404);
        }

        $client->update(['is_deleted' => true]);

        return response()->json([
            'status' => 'success',
            'message' => 'Client marked as deleted',
        ], 200);
    }
}
