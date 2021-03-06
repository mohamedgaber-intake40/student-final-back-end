<?php

namespace App\Http\Controllers\API\v1\Auth;

use App\CompanyProfile;
use App\DepartmentFaculty;
use App\Enums\UserGender;
use App\Enums\UserType;
use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Resources\UserResource;
use App\StudentProfile;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\NewAccessToken;

class AuthController extends Controller
{
    public function login(LoginRequest $request)
    {
        $user = User::where('email', $request->email)->first();
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response(['Message' => 'The provided credentials are incorrect.'], 422);
        }
        if ($token = $user->tokens()->where('name', $request->device_name)) {
            $token->delete();
        }

        if ($user->type === UserType::getTypeString(UserType::STUDENT) ||
            $user->type === UserType::getTypeString(UserType::TEACHING_STAFF) ||
            $user->type === UserType::getTypeString(UserType::COMPANY))
            $user->load('profileable');

        if ($user->type === UserType::getTypeString(UserType::MODERATOR))
            $user->load('profileable.faculty.university');

        $token = $user->createToken($request->device_name);
        $response_data['data']['token']['access_token'] = $token->plainTextToken;
        $response_data['data']['token']['expired_at'] = $this->getTokenExpirationTime($token);
        $response_data['data']['user'] = new UserResource($user);

        return response($response_data);
    }

    private function getTokenExpirationTime(NewAccessToken $token)
    {
        return Carbon::parse($token->accessToken->create_at)
            ->addMinutes(config('sanctum.expiration'))
            ->toDateTimeString();
    }

    public function register(RegisterRequest $request)
    {
        if ($request->type == UserType::STUDENT) // student
            $profile = StudentProfile::create($request->only(['birthdate', 'year']));
        else                                     // company
            $profile = CompanyProfile::create($request->only(['fax', 'description', 'website']));

        $user = $profile->user()->create($request->only([
            'name',
            'email',
            'password',
            'gender',
            'address',
            'mobile'
        ]) + [
            'avatar' => 'images/users/' . ($request->gender == UserGender::MALE ? 'default_male.png' : 'default_female.png'),
        ]);

        if ($request->type == UserType::STUDENT) // student
        {
            $department_facultites = DepartmentFaculty::whereIn('department_id', $request->departments)->where('faculty_id', $request->faculty)->get();
            $user->departmentFaculties()->attach($department_facultites);
        }

        if ($user->type === UserType::getTypeString(UserType::STUDENT) ||
            $user->type === UserType::getTypeString(UserType::TEACHING_STAFF) ||
            $user->type === UserType::getTypeString(UserType::COMPANY))
            $user->load('profileable');

        if ($user->type === UserType::getTypeString(UserType::MODERATOR))
            $user->load('profileable.faculty.university');

        $token = $user->createToken($request->device_name);
        $response_data['data']['token']['access_token'] = $token->plainTextToken;
        $response_data['data']['token']['expired_at'] = $this->getTokenExpirationTime($token);
        $response_data['data']['user'] = new UserResource($user);

        $user->sendEmailVerificationNotification();

        return response($response_data, 201);
    }

    public function logout(Request $request)
    {
        if ($token = $request->user()->tokens()->where('name', $request->device_name)) {
            $token->delete();
            return response([]);
        }

        return response([], 204);
    }
}
