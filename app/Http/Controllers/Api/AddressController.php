<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Address\StoreAddressRequest;
use App\Http\Requests\Address\UpdateAddressRequest;
use App\Http\Resources\AddressResource;
use App\Models\Address;
use Illuminate\Http\Request;

class AddressController extends Controller
{
    public function index(Request $request)
    {
        $addresses = $request->user()
            ->addresses()
            ->latest()
            ->get();

        return response()->json([
            'success' => true,
            'data' => AddressResource::collection($addresses),
        ]);
    }

    public function store(StoreAddressRequest $request)
    {
        $user = $request->user();

        $data = $request->validated();

        if (($data['is_default'] ?? false) === true) {
            $user->addresses()->update([
                'is_default' => false,
            ]);
        }

        $address = $user->addresses()->create($data);

        return response()->json([
            'success' => true,
            'message' => 'Address created successfully.',
            'data' => [
                'address' => new AddressResource($address),
            ],
        ], 201);
    }

    public function show(Request $request, Address $address)
    {
        abort_unless(
            $address->user_id === $request->user()->id,
            403
        );

        return response()->json([
            'success' => true,
            'data' => [
                'address' => new AddressResource($address),
            ],
        ]);
    }

    public function update(
        UpdateAddressRequest $request,
        Address $address
    ) {
        abort_unless(
            $address->user_id === $request->user()->id,
            403
        );

        $data = $request->validated();

        if (($data['is_default'] ?? false) === true) {
            $request->user()
                ->addresses()
                ->where('id', '!=', $address->id)
                ->update([
                    'is_default' => false,
                ]);
        }

        $address->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Address updated successfully.',
            'data' => [
                'address' => new AddressResource($address->fresh()),
            ],
        ]);
    }

    public function destroy(Request $request, Address $address)
    {
        abort_unless(
            $address->user_id === $request->user()->id,
            403
        );

        $address->delete();

        return response()->json([
            'success' => true,
            'message' => 'Address deleted successfully.',
        ]);
    }
}
