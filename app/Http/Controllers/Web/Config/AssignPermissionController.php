<?php

namespace App\Http\Controllers\Web\Config;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use Yajra\DataTables\DataTables;

class AssignPermissionController extends Controller
{
    protected $admin, $permissions;

    public function __construct()
    {
        $this->admin = new Admin();
        $this->permissions = new Permission();
    }
    public function index()
    {
        return view('admin.pages.config.assign.index');
    }

    public function getData(Request $request)
    {
        if ($request->ajax()) {
            $data = $this->admin->select(['id', 'name', 'email', 'username', 'status', 'created_at']);
            return DataTables::of($data)
                ->addColumn('action', function ($row) {
                    return '
                    <div class="d-flex justify-content-center">
                        <a href="' . route('config.assign.create', $row->id) . '" class="btn btn-info me-1" title="Lihat Detail">
                            <i class="fas fa-eye"></i>
                        </a>
                    </div>
                    ';
                })
                ->editColumn('status', fn($row) => ucfirst($row->status))
                ->rawColumns(['action'])
                ->make(true);
        }
    }

    public function create($id)
    {
        $admin = $this->admin->findOrFail($id);
        $allPermissions = $this->permissions->all();
        $assignedPermissions = $admin->getAllPermissions();
        $assignedIds = $assignedPermissions->pluck('id')->toArray();

        $availablePermissions = $allPermissions->whereNotIn('id', $assignedIds);
        return view('admin.pages.config.assign.create', compact('admin', 'assignedPermissions', 'availablePermissions'));
    }

    public function assignPermission(Request $request)
    {
        DB::beginTransaction();
        $user = $this->admin->findOrFail($request->id);

        try {
            $user->givePermissionTo($request->permissions);
            DB::commit();
            return response()->json(['success' => true]);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $th->getMessage()], 500);
        }
    }

    public function revokePermission(Request $request)
    {
        DB::beginTransaction();
        $user = $this->admin->findOrFail($request->id);

        try {
            $user->revokePermissionTo($request->permissions);
            DB::commit();
            return response()->json(['success' => true]);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $th->getMessage()], 500);
        }
    }
}
