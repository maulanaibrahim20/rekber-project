<?php

namespace App\Http\Controllers\Web\Config;

use App\Facades\Message;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Permission;
use Yajra\DataTables\DataTables;

class PermissionController extends Controller
{
    protected $permission;

    public function __construct()
    {
        $this->permission = new Permission();
    }
    public function index()
    {
        return view('admin.pages.config.permission.index');
    }

    public function getData(Request $request)
    {
        if ($request->ajax()) {
            $data = $this->permission->select(['id', 'name', 'guard_name'])->latest();

            return DataTables::of($data)
                ->addColumn('action', function ($row) {
                    return '
                        <div class="d-flex justify-content-center">
                            <a href="#" class="btn btn-primary me-1 open-global-modal" title="Edit"
                                data-url="' . route('config.permission.edit', $row->id) . '">
                                <i class="fas fa-edit"></i>
                            </a>
                            <a href="javascript:void(0);" class="btn btn-danger btn-delete-permission" title="Delete"
                                data-id="' . $row->id . '">
                                <i class="fas fa-trash"></i>
                            </a>
                        </div>';
                })
                ->rawColumns(['action'])
                ->make(true);
        }
    }

    public function create()
    {
        return view('admin.pages.config.permission.create');
    }

    public function store(Request $request)
    {
        DB::beginTransaction();

        $validator = Validator::make($request->all(), [
            'name' => 'required|unique:permissions,name',
        ]);

        if ($validator->fails()) {
            return Message::validator($validator->errors()->first());
        }
        try {
            $this->permission->create([
                'name' => $request->name,
                'guard_name' => 'admin'
            ]);
            DB::commit();
            return Message::success('Permission berhasil ditambahkan.');
        } catch (\Throwable $e) {
            DB::rollBack();
            return Message::error('Terjadi kesalahan saat menyimpan permission: ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        $permission = $this->permission->findOrFail($id);
        return view('admin.pages.config.permission.edit', compact('permission'));
    }

    public function update(Request $request, $id)
    {
        DB::beginTransaction();
        $permission = $this->permission->findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name' => 'required|unique:permissions,name,' . $permission->id,
        ]);

        if ($validator->fails()) {
            return Message::validator($validator->errors()->first());
        }

        try {
            $permission->update($request->only('name'));

            DB::commit();
            return response()->json(['message' => 'Permission berhasil diperbarui.']);
        } catch (\Throwable $e) {
            DB::rollBack();
            return Message::error('Terjadi kesalahan saat memulai transaksi: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();

        $permission = $this->permission->findOrFail($id);
        try {
            $permission->delete();

            DB::commit();

            return Message::success('Permission berhasil dihapus.');
        } catch (\Throwable $e) {
            DB::rollBack();
            return Message::error('Terjadi kesalahan saat menghapus permission: ' . $e->getMessage());
        }
    }
}
