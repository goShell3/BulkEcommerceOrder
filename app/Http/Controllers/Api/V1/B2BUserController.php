<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Http\Requests\Api\V1\B2BUser\StoreB2BUserRequest;
use App\Http\Requests\Api\V1\B2BUser\UpdateB2BUserRequest;
use App\Http\Resources\B2BUserResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Hash;

class B2BUserController extends Controller
{
    /**
     * Display a listing of B2B users.
     *
     * @return AnonymousResourceCollection
     */
    public function index(): AnonymousResourceCollection
    {
        $users = User::role('b2b')->with(['company', 'addresses'])->paginate(10);
        return B2BUserResource::collection($users);
    }

    /**
     * Store a newly created B2B user in storage.
     *
     * @param StoreB2BUserRequest $request
     * @return JsonResponse
     */
    public function store(StoreB2BUserRequest $request): JsonResponse
    {
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'phone' => $request->phone,
            'company_name' => $request->company_name,
            'tax_number' => $request->tax_number,
            'company_registration_number' => $request->company_registration_number,
        ]);

        $user->assignRole('b2b');

        if ($request->has('address')) {
            $user->addresses()->create($request->address);
        }

        return response()->json([
            'message' => 'B2B user created successfully',
            'data' => new B2BUserResource($user)
        ], 201);
    }

    /**
     * Display the specified B2B user.
     *
     * @param User $user
     * @return B2BUserResource
     */
    public function show(User $user): B2BUserResource
    {
        $user->load(['company', 'addresses', 'orders']);
        return new B2BUserResource($user);
    }

    /**
     * Update the specified B2B user in storage.
     *
     * @param UpdateB2BUserRequest $request
     * @param User $user
     * @return JsonResponse
     */
    public function update(UpdateB2BUserRequest $request, User $user): JsonResponse
    {
        $user->update($request->validated());

        if ($request->has('password')) {
            $user->update(['password' => Hash::make($request->password)]);
        }

        if ($request->has('address')) {
            $user->addresses()->updateOrCreate(
                ['is_primary' => true],
                $request->address
            );
        }

        return response()->json([
            'message' => 'B2B user updated successfully',
            'data' => new B2BUserResource($user)
        ]);
    }

    /**
     * Remove the specified B2B user from storage.
     *
     * @param User $user
     * @return JsonResponse
     */
    public function destroy(User $user): JsonResponse
    {
        $user->delete();
        return response()->json([
            'message' => 'B2B user deleted successfully'
        ]);
    }
} 