<?php

namespace App\Policies;

use App\Models\PerencanaanPerjalananPermanentStock;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class PerencanaanPerjalananPermanentStockPolicy
{
  /**
   * Determine whether the user can view any models.
   */
  public function viewAny(User $user): bool
  {
    return $user->isAdmin() || $user->isLeader() || $user->isSpg();
  }

  /**
   * Determine whether the user can view the model.
   */
  public function view(User $user, PerencanaanPerjalananPermanentStock $perencanaanPerjalananPermanentStock): bool
  {
    return $user->isAdmin() || $user->isLeader() || $user->isSpg();
  }

  /**
   * Determine whether the user can create models.
   */
  public function create(User $user): bool
  {
    return $user->isAdmin() || $user->isLeader();
  }

  /**
   * Determine whether the user can update the model.
   */
  public function update(User $user, PerencanaanPerjalananPermanentStock $perencanaanPerjalananPermanentStock): bool
  {
    return $user->isAdmin() || $user->isLeader();
  }

  /**
   * Determine whether the user can delete the model.
   */
  public function delete(User $user, PerencanaanPerjalananPermanentStock $perencanaanPerjalananPermanentStock): bool
  {
    return $user->isAdmin() || $user->isLeader();
  }

  /**
   * Determine whether the user can restore the model.
   */
  public function restore(User $user, PerencanaanPerjalananPermanentStock $perencanaanPerjalananPermanentStock): bool
  {
    return $user->isAdmin() || $user->isLeader();
  }

  /**
   * Determine whether the user can permanently delete the model.
   */
  public function forceDelete(User $user, PerencanaanPerjalananPermanentStock $perencanaanPerjalananPermanentStock): bool
  {
    return $user->isAdmin() || $user->isLeader();
  }
}
