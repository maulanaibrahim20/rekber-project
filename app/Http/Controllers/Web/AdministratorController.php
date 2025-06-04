<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class AdministratorController extends Controller
{
    public function index()
    {
        return view('admin.pages.administrator.index');
    }

    public function getData(Request $request)
    {
        if ($request->ajax()) {
            $data = Admin::select(['id', 'name', 'email', 'username', 'status', 'is_super_admin', 'created_at']);
            return DataTables::of($data)
                ->addColumn('action', function ($row) {
                    return '
                    <div class="d-flex justify-content-center">
                    <a href="' . url('administrator.edit', $row->id) . '" class="btn btn-primary me-1" title="Edit">
                        <i class="fas fa-edit"></i>
                    </a>
                    <a href="javascript:void(0);" onclick="deleteAdmin(' . $row->id . ')" class="btn btn-danger" title="Delete">
                        <i class="fas fa-trash"></i>
                    </a>
                    </div>
                ';
                })
                ->editColumn('is_super_admin', fn($row) => $row->is_super_admin ? 'Yes' : 'No')
                ->editColumn('status', fn($row) => ucfirst($row->status))
                ->editColumn('created_at', fn($row) => $row->created_at->format('Y-m-d H:i'))
                ->rawColumns(['action'])
                ->make(true);
        }
    }

    public function create()
    {
        return view('admin.pages.administrator.create');
    }

    public function store(Request $request)
    {
        DB::beginTransaction();
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:admins,email',
            'username' => 'required|string|unique:admins,username',
            'password' => 'required|min:6|confirmed',
            'is_super_admin' => 'nullable|boolean',
            'status' => 'required|in:active,inactive',
        ]);

        if ($validator->fails()) {
            if ($request->ajax()) {
                return response()->json(['message' => $validator->errors()->first()], 422);
            }
            return back()->with('error', $validator->errors()->first());
        }

        try {

            Admin::create([
                'name' => $request->name,
                'email' => $request->email,
                'username' => $request->username,
                'password' => Hash::make($request->password),
                'is_super_admin' => $request->is_super_admin ?? false,
                'status' => $request->status,
            ]);
            DB::commit();

            if ($request->ajax()) {
                return response()->json(['message' => 'Administrator berhasil ditambahkan.']);
            }
            return back()->with('success', 'Administrator berhasil ditambahkan.');
        } catch (\Throwable $th) {
            DB::rollBack();

            if ($request->ajax()) {
                return response()->json(['message' => 'Terjadi kesalahan saat menyimpan data.'], 500);
            }

            return back()->with('error', 'Something went wrong.');
        }
    }
}
