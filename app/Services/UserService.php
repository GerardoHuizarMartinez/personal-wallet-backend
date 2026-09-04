<?php

namespace App\Services;

use App\Models\User;
use App\Mappers\UserMapper;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class UserService
{
    public function list(): array
    {
        return UserMapper::collection(
            User::orderBy('name')->get()
        );
    }

    public function find(int $id): array
    {
        return UserMapper::toArray(User::with('colony')->findOrFail($id));
    }

    public function create(array $data): array
    {
        $data['password'] = Hash::make($data['password']);

        $user = User::create($data);

        return UserMapper::toArray($user);
    }

    public function update(int $id, array $data): array
    {
        $user = User::findOrFail($id);

        if (!empty($data['password'])) {
            if (empty($data['current_password']) || !Hash::check($data['current_password'], $user->password)) {
                throw ValidationException::withMessages([
                    'current_password' => ['La contraseña actual no es correcta.'],
                ]);
            }

            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        unset($data['current_password']);

        $user->update($data);

        return UserMapper::toArray($user);
    }

    public function delete(int $id): void
    {
        $user = User::findOrFail($id);
        $user->delete();
    }

    public function uploadPhoto(int $id, UploadedFile $photo): array
    {
        $user = User::findOrFail($id);

        $this->deleteStoredPhoto($user->url_image);

        $path = $photo->store('avatars', 'public');
        $user->url_image = '/storage/' . $path;
        $user->save();

        return UserMapper::toArray($user);
    }

    public function deletePhoto(int $id): array
    {
        $user = User::findOrFail($id);

        $this->deleteStoredPhoto($user->url_image);

        $user->url_image = null;
        $user->save();

        return UserMapper::toArray($user);
    }

    private function deleteStoredPhoto(?string $url): void
    {
        if (!$url) {
            return;
        }

        $marker = '/storage/';
        $position = strpos($url, $marker);

        if ($position === false) {
            return;
        }

        $path = substr($url, $position + strlen($marker));
        Storage::disk('public')->delete($path);
    }
}