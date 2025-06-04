<?php

namespace App\Http\Controllers\Web;

use App\Facades\Message;
use App\Http\Controllers\Controller;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Str;

class AdministratorController extends Controller
{
    protected $admin;

    public function __construct()
    {
        $this->admin = new Admin();
    }
    public function index()
    {
        return view('admin.pages.administrator.index');
    }

    public function getData(Request $request)
    {
        if ($request->ajax()) {
            $data = $this->admin->select(['id', 'name', 'email', 'username', 'status', 'is_super_admin', 'created_at']);
            return DataTables::of($data)
                ->addColumn('action', function ($row) {
                    return '
                    <div class="d-flex justify-content-center">
                        <a href="#" class="btn btn-primary me-1 open-global-modal" title="Edit" data-url="' . route('administrator.edit', $row->id) . '">
                            <i class="fas fa-edit"></i>
                        </a>
                        <a href="javascript:void(0);" class="btn btn-danger btn-delete-admin" data-id="' . $row->id . '" title="Delete">
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
                return Message::validator(
                    $validator->errors()->first(),
                );
            }
            return back()->with('error', $validator->errors()->first());
        }

        try {

            $this->admin->create([
                'name' => $request->name,
                'email' => $request->email,
                'username' => $request->username,
                'password' => Hash::make($request->password),
                'is_super_admin' => $request->is_super_admin ?? false,
                'status' => $request->status,
                'email_verified_at' => now(),
                'remember_token' => Str::random(10),
            ]);
            DB::commit();

            if ($request->ajax()) {
                return Message::success('Administrator berhasil ditambahkan.');
            }
            return back()->with('success', 'Administrator berhasil ditambahkan.');
        } catch (\Throwable $th) {
            DB::rollBack();

            if ($request->ajax()) {
                return Message::error('Terjadi kesalahan saat menyimpan data.');
            }

            return back()->with('error', 'Something went wrong.');
        }
    }

    public function edit($id)
    {
        $data['admin'] = $this->admin->findOrFail($id);
        return view('admin.pages.administrator.edit', $data);
    }

    public function update(Request $request, $id)
    {
        DB::beginTransaction();
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:admins,email,' . $id,
            'username' => 'required|string|unique:admins,username,' . $id,
            'is_super_admin' => 'nullable|boolean',
            'status' => 'required|in:active,inactive',
        ]);

        if ($validator->fails()) {
            if ($request->ajax()) {
                return Message::validator($validator->errors()->first());
            }
            return back()->with('error', $validator->errors()->first());
        }

        try {

            $admin = $this->admin->findOrFail($id);

            if ($request->has('password') && $request->password) {
                $validator = Validator::make($request->all(), [
                    'password' => 'required|min:6|confirmed',
                ]);

                if ($validator->fails()) {
                    if ($request->ajax()) {
                        return Message::validator($validator->errors()->first());
                    }
                    return back()->with('error', $validator->errors()->first());
                }

                $admin->password = Hash::make($request->password);
            }
            $admin->update([
                'name' => $request->name,
                'email' => $request->email,
                'username' => $request->username,
                'is_super_admin' => $request->is_super_admin ?? false,
                'status' => $request->status,
            ]);
            DB::commit();

            if ($request->ajax()) {
                return Message::success('Administrator berhasil diupdate.');
            }
            return back()->with('success', 'Administrator berhasil diupdate.');
        } catch (\Throwable $th) {
            DB::rollBack();

            if ($request->ajax()) {
                return Message::error('Terjadi kesalahan saat mengupdate data.');
            }

            return back()->with('error', 'Something went wrong.');
        }
    }

    public function destroy($id)
    {
        try {
            DB::beginTransaction();

            $admin = $this->admin->findOrFail($id);

            if ($admin->is_super_admin) {
                return Message::warning('Tidak dapat menghapus administrator super.');
            }

            $admin->delete();

            DB::commit();
            return Message::success('Administrator berhasil dihapus.');
        } catch (\Exception $e) {
            DB::rollBack();
            return Message::error('Gagal menghapus administrator: ' . $e->getMessage());
        }
    }
}
