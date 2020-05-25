<?php

use App\Post;
use App\User;
use Illuminate\Database\Seeder;

class PostSeeder extends Seeder
{
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		User::whereIn('profileable_type', [
			'student',
			'teaching_staff'
		])->get()->each(function ($user) {
			$department_faculty = $user->departmentFaculties()->inRandomOrder()->first();
			factory(Post::class, 5)->create([
				'user_id' => $user->id,
				'department_faculty_id' => $department_faculty->id
			]);
		});
	}
}