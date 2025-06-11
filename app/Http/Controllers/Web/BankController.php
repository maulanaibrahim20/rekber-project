<?php

namespace App\Http\Controllers\Web;

use App\Enum\Status;
use App\Facades\Message;
use App\Http\Controllers\Controller;
use App\Models\Bank;
use App\Models\BankAccount;
use App\Rules\ValidateStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\DataTables;

class BankController extends Controller
{
    protected $bank;
    public function __construct()
    {
        $this->bank = new Bank();
    }
    public function index()
    {
        return view('admin.pages.bank.index');
    }
    public function getData(Request $request)
    {
        if ($request->ajax()) {
            $data = $this->bank->select('*')->latest();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    return '
                    <div class="d-flex justify-content-center">
                        <a href="javascript:void(0);" class="btn btn-danger btn-delete-bank" title="Delete"
                            data-id="' . $row->id . '">
                            <i class="fas fa-trash"></i>
                        </a>
                    </div>';
                })
                ->editColumn('status', function ($row) {
                    return '
                    <select class="form-select form-select-sm status-select" data-id="' . $row->id . '">
                        <option value="1" ' . ($row->status == 1 ? 'selected' : '') . '>Active</option>
                        <option value="0" ' . ($row->status == 0 ? 'selected' : '') . '>Inactive</option>
                    </select>';
                })
                ->rawColumns(['action', 'status'])
                ->make(true);
        }
    }

    public function create()
    {
        $response = Http::withBasicAuth(env('XENDIT_SECRET_KEY'), '')
            ->get('https://api.xendit.co/available_disbursements_banks');

        $banksFromAPI = $response->json();
        $existingCodes = Bank::pluck('code')->toArray();

        $availableBanks = collect($banksFromAPI)->filter(function ($bank) use ($existingCodes) {
            return !in_array($bank['code'], $existingCodes);
        })->values();

        return view('admin.pages.bank.create', [
            'banks' => $availableBanks,
        ]);
    }

    public function store(Request $request)
    {
        DB::beginTransaction();

        $validator = Validator::make($request->all(), [
            'bank_code' => 'required|string|max:255',
            'bank_name' => 'required|string|max:255',
            'can_disburse' => 'required|boolean',
            'can_name_check' => 'required|boolean',
            'status' => ['required', 'string', new ValidateStatus('bankStatus')],
        ]);

        if ($validator->fails()) {
            return $request->ajax()
                ? Message::validator($validator->errors()->first())
                : back()->with('error', $validator->errors()->first());
        }

        try {
            $this->bank->create([
                'code' => $request->bank_code,
                'name' => $request->bank_name,
                'can_disburse' => $request->can_disburse,
                'can_name_check' => $request->can_name_check,
                'status' => $request->status,
            ]);

            DB::commit();
            return Message::success('Bank berhasil ditambahkan!');
        } catch (\Throwable $th) {
            DB::rollBack();
            return Message::error($th->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            DB::beginTransaction();

            $bank = $this->bank->findOrFail($id);

            $usedInBankAccounts = BankAccount::where('bank_id', $id)->exists();

            if ($usedInBankAccounts) {
                return Message::error('Bank tidak dapat dihapus karena sedang digunakan oleh pengguna lain.');
            }

            $bank->delete();

            DB::commit();
            return Message::success('Bank berhasil dihapus.');
        } catch (\Exception $e) {
            DB::rollBack();
            return Message::error('Gagal menghapus bank. ' . $e->getMessage());
        }
    }

    public function updateStatus(Request $request)
    {
        DB::beginTransaction();
        $validator = Validator::make($request->all(), [
            'id' => 'required|exists:banks,id',
            'status' => 'required|in:0,1',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'Invalid data'], 422);
        }

        try {
            $bank = $this->bank->findOrFail($request->id);
            $bank->status = $request->status;
            $bank->save();

            DB::commit();

            return response()->json(['message' => 'Status updated successfully']);
        } catch (\Throwable $th) {
            DB::commit();
            return response()->json(['message' => 'Error: ' . $th->getMessage()], 500);
        }
    }
}
