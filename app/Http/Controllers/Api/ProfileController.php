<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Profile;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function show(Request $request): JsonResponse
    {
        $profile = $request->user()->profile;

        abort_if(! $profile, 404, 'Profile has not been completed yet.');

        return $this->ok($profile);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'firstname' => ['required', 'string', 'max:255'],
            'middlename' => ['nullable', 'string', 'max:255'],
            'lastname' => ['required', 'string', 'max:255'],
            'gender' => ['required', 'string', 'max:255'],
            'birthday' => ['required', 'date'],
            'ssn' => ['required', 'digits:4'],
            'social_number' => ['required', 'digits:9'],
            'address1' => ['required', 'string', 'max:255'],
            'address2' => ['required', 'string', 'max:255'],
            'city' => ['required', 'string', 'max:255'],
            'state' => ['required', 'string', 'max:255'],
            'zipcode' => ['required', 'string', 'max:20'],
            'drivernumber' => ['required', 'string', 'max:255'],
            'driverstate' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:20'],
            'email' => ['required', 'string', 'email', 'max:255'],
            'organization' => ['nullable', 'string', 'max:255'],
            'emergencycontact' => ['required', 'string', 'max:255'],
            'emergencyphone' => ['required', 'string', 'max:20'],
            'relationship' => ['required', 'string', 'max:255'],
            'handguncaliber' => ['nullable', 'string', 'max:255'],
            'handguntype' => ['nullable', 'string', 'max:255'],
            'handgunrental' => ['nullable', 'string', 'max:255'],
            'shootingshotgun' => ['nullable', 'string', 'max:255'],
            'shotgungauce' => ['nullable', 'string', 'max:255'],
            'shotgunrental' => ['nullable', 'string', 'max:255'],
        ]);

        $profile = Profile::updateOrCreate(['user_id' => $request->user()->id], $validated);

        return $this->ok($profile);
    }

    private function ok(mixed $data, int $status = 200): JsonResponse
    {
        return response()->json(['success' => true, 'data' => $data], $status);
    }
}
