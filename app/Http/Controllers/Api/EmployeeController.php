<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\EmployeeRequest;
use App\Models\Employee;
use Illuminate\Support\Facades\Hash;
use Illuminate\Testing\Fluent\Concerns\Has;
use function Laravel\Prompts\error;

class EmployeeController extends Controller
{
    public function show()
    {
        return Employee::all();
    }
    public function create(EmployeeRequest $request)
    {
        $email = $request->input('email');
        if((Employee::where('email',$email))->exists())
        {
            //Почта занята
            return -1;
        }
        else
        {
            $arr = $request->validated();
            $arr['password'] = Hash::make($arr['password']);
            return Employee::create($arr);
        }

    }

}
