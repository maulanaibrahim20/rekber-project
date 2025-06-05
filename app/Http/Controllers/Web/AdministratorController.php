<?php

namespace App\Http\Controllers\Web;

use App\Enum\Status;
use App\Facades\Message;
use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Rules\ValidateStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

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
            $data = $this->admin->with('roles')->select(['id', 'name', 'email', 'username', 'status', 'created_at']);

            return DataTables::of($data)
                ->addColumn('role', function ($row) {
                    return $row->roles->map(function ($role) {
                        $color = $role->name === 'Super Admin' ? 'primary' : 'warning';
                        return '<span class=" text-white badge bg-' . $color . '">' . e($role->name) . '</span>';
                    })->implode(' ');
                })
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
                ->editColumn('status', function ($row) {
                    $label = Status::label('adminStatus', $row->status);
                    $class = $row->status == 1 ? 'badge bg-success text-white' : 'badge bg-danger text-white';
                    return "<span class='{$class}'>{$label}</span>";
                })
                ->editColumn('created_at', fn($row) => $row->created_at->format('Y-m-d H:i'))
                ->rawColumns(['action', 'status', 'role'])
                ->make(true);
        }
    }

    public function create()
    {
        $data['status'] = Status::options('adminStatus');
        unset($data['status'][3]);
        $data['role'] = Role::all();

        return view('admin.pages.administrator.create', $data);
    }

    public function store(Request $request)
    {
        DB::beginTransaction();
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:admins,email',
            'username' => 'required|string|unique:admins,username',
            'password' => 'required|min:6|confirmed',
            'status'      => ['required', 'string', new ValidateStatus('adminStatus')],
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
            $admin = $this->admin->create([
                'name' => $request->name,
                'email' => $request->email,
                'username' => $request->username,
                'password' => Hash::make($request->password),
                'status' => $request->status,
                'email_verified_at' => now(),
                'remember_token' => Str::random(10),
            ]);
            if ($request->has('is_super_admin')) {
                $admin->assignRole('Super Admin');
            } else {
                $admin->assignRole('Admin');
            }

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
        $data['status'] = Status::options('adminStatus');

        return view('admin.pages.administrator.edit', $data);
    }

    public function update(Request $request, $id)
    {
        DB::beginTransaction();
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:admins,email,' . $id,
            'username' => 'required|string|unique:admins,username,' . $id,
            'status'      => ['required', 'string', new ValidateStatus('adminStatus')],
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
                'status' => $request->status,
            ]);

            if ($request->has('is_super_admin')) {
                $admin->syncRoles(['Super Admin']);
            } else {
                $admin->syncRoles(['Admin']);
            }

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
            if ($admin->hasRole('Super Admin')) {
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
