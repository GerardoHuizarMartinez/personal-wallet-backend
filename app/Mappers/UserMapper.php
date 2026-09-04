<?php

namespace App\Mappers;

use App\Models\User;

class UserMapper
{
    public static function toArray(User $user): array
    {
        return [
            'id'               => $user->id,
            'name'             => $user->name,
            'first_last_name'  => $user->first_last_name,
            'second_last_name' => $user->second_last_name,
            'email'            => $user->email,
            'telephone'        => $user->telephone,
            'cellphone'        => $user->cellphone,
            'birthday'         => $user->birthday,
            'gender'           => $user->gender,
            'country'          => $user->country,
            'colony_id'        => $user->colony_id,
            'colony'           => $user->relationLoaded('colony') && $user->colony
                ? ColonyMapper::toArray($user->colony)
                : null,
            'street'           => $user->street,
            'no_ext'           => $user->no_ext,
            'no_int'           => $user->no_int,
            'status'           => $user->status,
            'url_image'        => self::resolveImageUrl($user->url_image),
        ];
    }

    public static function collection(iterable $users): array
    {
        return collect($users)
            ->map(fn(User $user) => self::toArray($user))
            ->values()
            ->all();
    }

    private static function resolveImageUrl(?string $value): ?string
    {
        if (!$value) {
            return null;
        }

        $marker = '/storage/';
        $position = strpos($value, $marker);

        // Es un archivo que nosotros guardamos: se reconstruye la URL con el host
        // de la petición actual, para que funcione sin importar desde qué
        // dispositivo (o IP de red local) se suba o se consulte la foto.
        if ($position !== false) {
            return url(substr($value, $position));
        }

        // URL externa (por ejemplo, una pegada manualmente antes de tener subida de archivos).
        return $value;
    }
}