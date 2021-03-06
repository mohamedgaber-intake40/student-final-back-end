<?php

namespace App\Enums;

class UserType
{
	const STUDENT = 0;
	const TEACHING_STAFF = 1;
	const COMPANY = 2;
	const MODERATOR = 3;
	const ADMIN = 4;

	/**
	 * The user available types.
	 *
	 * @var array
	 */
	public static $types = [
		'Student',
		'TeachingStaff',
		'Company',
		'Moderator',
		'Admin'
	];

	/**
	 * Get the type from an integer value.
	 *
	 * @param int $value the type value equivalent.
	 *
	 * @return string|null
	 */
	public static function getTypeString(int $value)
	{
		if ($value >= count(static::$types))
			return null;

		return static::$types[$value];
	}

	/**
	 * Get the type from an integer value as **App\ModelProfile**.
	 *
	 * @param int $value the type value equivalent.
	 *
	 * @return string|null
	 */
	public static function getTypeModel(int $value)
	{
		if ($type = static::getTypeString($value))
			return 'App\\' . $type . 'Profile';

		return null;
	}
}