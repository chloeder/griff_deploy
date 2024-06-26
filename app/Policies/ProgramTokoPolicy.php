<?php

namespace App\Policies;

use App\Models\ProgramToko;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ProgramTokoPolicy
{
  /**
   * Determine whether the user can view any models.
   */
  public function viewAny(User $user): bool
  {
    return $user->isAdmin() || $user->isLeader() || $user->isSesm() || $user->isSpg();
  }

  /**
   * Determine whether the user can view the model.
   */
  public function view(User $user, ProgramToko $programToko): bool
  {
    return $user->isAdmin() || $user->isLeader() || $user->isSesm() || $user->isSpg();
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
  public function update(User $user, ProgramToko $programToko): bool
  {
    return $user->isAdmin() || $user->isLeader();
  }

  /**
   * Determine whether the user can delete the model.
   */
  public function delete(User $user, ProgramToko $programToko): bool
  {
    return $user->isAdmin() || $user->isLeader();
  }

  /**
   * Determine whether the user can restore the model.
   */
  public function restore(User $user, ProgramToko $programToko): bool
  {
    return $user->isAdmin() || $user->isLeader();
  }

  /**
   * Determine whether the user can permanently delete the model.
   */
  public function forceDelete(User $user, ProgramToko $programToko): bool
  {
    return $user->isAdmin() || $user->isLeader();
  }
}
