<?php

// app/Policies/ItemPolicy.php
namespace App\Policies;

use App\Models\Item;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ItemPolicy
{
    use HandlesAuthorization;

    /**
     * Determina si el usuario puede ver cualquier modelo.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determina si el usuario puede ver el modelo.
     */
    public function view(User $user, Item $item): bool
    {
        return $user->id === $item->user_id;
    }

    /**
     * Determina si el usuario puede crear modelos.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Determina si el usuario puede actualizar el modelo.
     */
    public function update(User $user, Item $item): bool
    {
        return $user->id === $item->user_id;
    }

    /**
     * Determina si el usuario puede eliminar el modelo.
     */
    public function delete(User $user, Item $item): bool
    {
        return $user->id === $item->user_id;
    }
}
