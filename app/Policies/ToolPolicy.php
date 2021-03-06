<?php

namespace App\Policies;

use App\Enums\UserType;
use App\Tag;
use App\Tool;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ToolPolicy
{
	use HandlesAuthorization;

	/**
	 * Determine whether the user can view any models.
	 *
	 * @param \App\User $user
	 * @param mixed $faculty
	 *
	 * @return bool
	 */
	public function viewAny(User $user, $faculty)
	{
		if ($user->type === UserType::getTypeString(UserType::STUDENT))
		{
			if ($user->departmentFaculties->load('faculty')->first(function ($department_faculty) use ($faculty) {
				return $department_faculty->faculty->id == $faculty->id;
			}))
				return true;
		}
		else if($user->type === UserType::getTypeString(UserType::MODERATOR) ||
				$user->type === UserType::getTypeString(UserType::ADMIN))
			return true;

		return false;
	}

	/**
	 * Determine whether the user can view the model.
	 *
	 * @param \App\User $user
	 * @param \App\Tool $tool
	 *
	 * @return bool
	 */
	public function view(User $user, Tool $tool)
	{
		return $user->can('viewAny', [Tool::class, $tool->faculty]);
	}

	/**
	 * Determine whether the user can create models.
	 *
	 * @param \App\User $user
	 * @param mixed $faculty
	 *
	 * @return bool
	 */
	public function create(User $user, $faculty)
	{
		if ($user->type === UserType::getTypeString(UserType::STUDENT))
		{
			if ($user->departmentFaculties->load('faculty')->first(function ($department_faculty) use ($faculty) {
				return $department_faculty->faculty->id == $faculty->id;
			}))
				return true;
		}
		else if ($user->type === UserType::getTypeString(UserType::ADMIN))
			return true;

		return false;
	}

	/**
	 * Determine whether the user can update the model.
	 *
	 * @param \App\User $user
	 * @param \App\Tool $tool
	 *
	 * @return bool
	 */
	public function update(User $user, Tool $tool)
	{
		return $tool->user->id === $user->id ||
			   $user->type === UserType::getTypeString(UserType::ADMIN);
	}

	/**
	 * Determine whether the user can delete the model.
	 *
	 * @param \App\User $user
	 * @param \App\Tool $tool
	 *
	 * @return bool
	 */
	public function delete(User $user, Tool $tool)
	{
		if ($tool->user->id === $user->id ||
			$user->type === UserType::getTypeString(UserType::ADMIN))
			return true;
		else if ($user->type === UserType::getTypeString(UserType::MODERATOR))
		{
			if ($user->profileable->faculty->id === $tool->faculty->id)
				return true;
		}

		return false;
	}

	/**
	 * Determine whether the user can close the model.
	 *
	 * @param \App\User $user
	 * @param \App\Tool $tool
	 *
	 * @return bool
	 */
	public function close(User $user, Tool $tool)
	{
		return $tool->user->id === $user->id;
	}

	/**
	 * Determine whether the user can attach models.
	 *
	 * @param \App\User $user
	 * @param \App\Tool $tool
	 * @param \App\Tag $tag
	 *
	 * @return bool
	 */
	public function attach(User $user, Tool $tool, Tag $tag)
	{
		return $tool->user->id === $user->id ||
			   $user->type === UserType::getTypeString(UserType::ADMIN);
	}

	/**
	 * Determine whether the user can detach models.
	 *
	 * @param \App\User $user
	 * @param \App\Tool $tool
	 * @param \App\Tag $tag
	 *
	 * @return bool
	 */
	public function detach(User $user, Tool $tool, Tag $tag)
	{
		return $tool->user->id === $user->id ||
			   $user->type === UserType::getTypeString(UserType::ADMIN);
	}
}