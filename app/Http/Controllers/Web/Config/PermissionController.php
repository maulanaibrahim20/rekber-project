<?php

namespace App\Http\Controllers\Web\Config;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Permission;
use Yajra\DataTables\DataTables;

class PermissionController extends Controller
{
    public function index()
    {
        return view('admin.pages.config.permission.index');
    }

    public function getData(Request $request)
    {
        if ($request->ajax()) {
            $data = Permission::select(['id', 'name', 'guard_name', 'created_at'])->latest();

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
        $validator = Validator::make($request->all(), [
            'name' => 'required|unique:permissions,name',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()->first()], 422);
        }

        Permission::create([
            'name' => $request->name,
            'guard_name' => 'admin'
        ]);

        return response()->json(['message' => 'Permission berhasil ditambahkan.']);
    }

    public function edit($id)
    {
        $permission = Permission::findOrFail($id);
        return view('admin.pages.config.permission.edit', compact('permission'));
    }

    public function update(Request $request, $id)
    {
        $permission = Permission::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name' => 'required|unique:permissions,name,' . $permission->id,
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()->first()], 422);
        }

        $permission->update($request->only('name'));

        return response()->json(['message' => 'Permission berhasil diperbarui.']);
    }

    public function destroy($id)
    {
        $permission = Permission::findOrFail($id);
        $permission->delete();

        return response()->json(['message' => 'Permission berhasil dihapus.']);
    }
}
