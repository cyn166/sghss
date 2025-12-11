<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Support\Facades\DB;
use App\Models\Doctor;
use App\Models\Patient;
use App\Models\Nurse;
use Throwable;
use Carbon\Carbon;

class RegisteredUserController extends Controller
{
    /**
     * Handle an incoming registration request and return a Personal Access Token.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
       public function store(Request $request)
    {
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'password' => ['required', Rules\Password::defaults()],
            'role' => ['required', 'string', 'max:50', 'in:admin,medico,paciente,enfermeiro'],
            'cpf' => ['required', 'string', 'max:14', 'unique:' . User::class],
        ];

        $role = $request->role;

        switch ($role) {
            case 'paciente':
                $rules = array_merge($rules, [
                    'birth_date' => ['required', 'string', 'date_format:d-m-Y'],
                    'phone' => ['required', 'string', 'max:20'],
                    'address' => ['required', 'string', 'max:255'],
                    'emergency_contact' => ['nullable', 'string', 'max:20'],
                    'blood_type' => ['nullable', 'string', 'max:5'],
                ]);
                break;

            case 'medico':
                $rules = array_merge($rules, [
                    'crm' => ['required', 'string', 'max:10', 'unique:doctors,crm'],
                    'specialty' => ['required', 'string', 'max:100'],
                    'phone' => ['nullable', 'string', 'max:20'],
                    'available_hours' => ['nullable', 'json'],
                ]);
                break;

            case 'enfermeiro':
                $rules = array_merge($rules, [
                    'license_number' => ['required', 'string', 'max:50', 'unique:nurses,license_number'],
                    'specialty' => ['required', 'string', 'max:100'],
                    'phone' => ['nullable', 'string', 'max:20'],
                    'license_expiry_date' => ['required', 'string', 'date_format:d-m-Y'],
                    'available_hours' => ['nullable', 'json'],
                ]);
                break;
        }

        $request->validate($rules);

        $role = $request->role;
        $token = null;
        $user = null;

        try {
            DB::transaction(function () use ($request, $role, &$user, &$token) {

                $user = User::create([
                    'name' => $request->name,
                    'email' => $request->email,
                    'password' => Hash::make($request->string('password')),
                    'role' => $role,
                    'cpf' => $request->cpf,
                ]);

                switch ($role) {
                    case 'paciente':
                        $birthDate = Carbon::createFromFormat('d-m-Y', $request->birth_date);
                        Patient::create([
                            'user_id' => $user->id,
                            'phone' => $request->phone,
                            'birth_date' => $birthDate,
                            'address' => $request->address,
                            'emergency_contact' => $request->emergency_contact,
                            'blood_type' => $request->blood_type,
                        ]);
                        break;
                    case 'medico':
                        Doctor::create([
                            'user_id' => $user->id,
                            'crm' => $request->crm,
                            'specialty' => $request->specialty,
                            'phone' => $request->phone,
                            'available_hours' => $request->available_hours,
                        ]);
                        break;
                    case 'enfermeiro':
                        $licenseExpire = Carbon::createFromFormat('d-m-Y', $request->license_expiry_date);

                        Nurse::create([
                            'user_id' => $user->id,
                            'license_number' => $request->license_number,
                            'specialty' => $request->specialty,
                            'license_expiry_date' => $licenseExpire,
                            'phone' => $request->phone,
                            'available_hours' => $request->available_hours,
                        ]);
                        break;
                }

                event(new Registered($user));

                $token = $user->createToken('auth-token')->plainTextToken;
            });

        } catch (Throwable $e) {
            throw $e;
        }

        return response()->json([
            'token' => $token,
            'message' => 'Usuário e perfil registrados com sucesso.'
        ], 201);
    }
}
