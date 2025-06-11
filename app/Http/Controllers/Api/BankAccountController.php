<?php

namespace App\Http\Controllers\Api;

use App\Facades\Message;
use App\Http\Controllers\Controller;
use App\Models\Bank;
use App\Models\BankAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class BankAccountController extends Controller
{
    protected $bankAccount;

    public function __construct()
    {
        $this->bankAccount = new BankAccount();
    }

    public function index()
    {
        $data = Bank::all();

        return Message::success('Bank accounts fetched successfully', $data);
    }

    public function store(Request $request)
    {
        DB::beginTransaction();

        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'bank_id' => 'required|exists:banks,id',
            'account_name' => 'required|string',
            'account_number' => 'required|string',
        ]);

        if ($validator->fails()) {
            return Message::validator($validator->errors()->first(), isList: true);
        }

        $existing = $this->bankAccount->where('user_id', $request->user_id)
            ->where('bank_id', $request->bank_id)
            ->exists();

        if ($existing) {
            return Message::validator('Tidak dapat menambahkan akun bank yang sama.');
        }

        try {

            $data = $this->bankAccount->create([
                'user_id' => $request->user_id,
                'bank_id' => $request->bank_id,
                'account_name' => $request->account_name,
                'account_number' => $request->account_number
            ]);

            DB::commit();

            return Message::success('Bank account created successfully', $data);
        } catch (\Throwable $th) {
            DB::rollBack();
            return Message::error('An error occurred while processing your request: ' . $th->getMessage());
        }
    }
}
