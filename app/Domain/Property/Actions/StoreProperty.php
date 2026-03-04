<?php

namespace App\Domain\Property\Actions;

use App\Domain\Identity\Models\User;
use App\Domain\Property\Models\Property;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class StoreProperty
{
    /**
     * @param  array<string, mixed>  $data
     */
    public function execute(User $user, array $data): Property
    {
        $validator = Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            'address' => ['required', 'string'],
            'price' => ['required', 'numeric', 'min:0'],
            'type' => ['required', 'string', 'max:50'],
            'description' => ['nullable', 'string'],
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        return Property::create([
            'user_id' => $user->id,
            'name' => $data['name'],
            'address' => $data['address'],
            'price' => $data['price'],
            'type' => $data['type'],
            'description' => $data['description'] ?? null,
            'status' => \App\Domain\Property\States\Available::class,
        ]);
    }
}
