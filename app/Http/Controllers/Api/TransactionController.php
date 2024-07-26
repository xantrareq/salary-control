<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\TransactionRequest;
use App\Models\Employee;
use App\Models\Transaction;

class TransactionController extends Controller
{
    public function show()
    {
        return Transaction::all();
    }
    public function unpaid()
    {
        $rate = 500;
        $employee_ids = Transaction::distinct()->pluck('employee_id')->toArray();
        $output = [];
        foreach ($employee_ids as $key => $id)
        {
            $hours = Transaction::where('employee_id',$id)->where('paid',false)->pluck('hours')->toArray();
            $sum = 0;
            foreach ($hours as $keys => $hour)
            {
                $sum += $hour * $rate;
            }
            $new_record = [$id => $sum];
            if($sum!=0)
            {
                $output[] = $new_record;
            }
        }
        $out = json_encode($output,JSON_PRETTY_PRINT);
        return ($out);
    }
    public function pay()
    {
        $rate = 500;
        $tr_ids = Transaction::where('paid',false)->pluck('id')->toArray();
        foreach ($tr_ids as $key => $id)
        {
            $transaction = Transaction::where('id',$id)->first();
            $transaction->update(['paid'=>1]);
        }
        return 1;
    }

    public function create(TransactionRequest $request)
    {
        $employee_id = $request->input('employee_id');
        if((Employee::where('id',$employee_id))->exists())
        {
            return Transaction::create($request->validated());

        }
        else
        {
            //Нет пользователя с таким айди
            return -1;
        }
    }
}
