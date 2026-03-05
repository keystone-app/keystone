<?php

namespace App\Domain\Property\Actions;

use App\Domain\Identity\Models\User;
use App\Domain\Legal\Models\Document;
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
            'images.*' => ['nullable', 'file', 'image', 'max:10240'],
            'videos.*' => ['nullable', 'file', 'mimetypes:video/mp4,video/quicktime', 'max:51200'],
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        $property = Property::create([
            'user_id' => $user->id,
            'name' => $data['name'],
            'address' => $data['address'],
            'price' => $data['price'],
            'type' => $data['type'],
            'description' => $data['description'] ?? null,
            'status' => \App\Domain\Property\States\Available::class,
        ]);

        if (isset($data['images']) && is_array($data['images'])) {
            foreach ($data['images'] as $image) {
                if ($image instanceof \Illuminate\Http\UploadedFile) {
                    $path = $image->store('properties/images', 'public');
                    Document::create([
                        'user_id' => $user->id,
                        'documentable_type' => Property::class,
                        'documentable_id' => $property->id,
                        'name' => $image->getClientOriginalName(),
                        'path' => $path,
                        'type' => 'property_image',
                    ]);
                }
            }
        }

        if (isset($data['videos']) && is_array($data['videos'])) {
            foreach ($data['videos'] as $video) {
                if ($video instanceof \Illuminate\Http\UploadedFile) {
                    $path = $video->store('properties/videos', 'public');
                    Document::create([
                        'user_id' => $user->id,
                        'documentable_type' => Property::class,
                        'documentable_id' => $property->id,
                        'name' => $video->getClientOriginalName(),
                        'path' => $path,
                        'type' => 'property_video',
                    ]);
                }
            }
        }

        return $property->load('media');
    }
}
