<?php

namespace App\Http\Controllers\Web\Config;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class AssignPermissionController extends Controller
{
    protected $admin;

    public function __construct()
    {
        $this->admin = new Admin();
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
                        <a href="#" class="btn btn-primary me-1 open-global-modal" title="Edit" data-url="' . route('administrator.edit', $row->id) . '">
                            <i class="fas fa-edit"></i>
                        </a>
                    </div>
                    ';
                })
                ->editColumn('status', fn($row) => ucfirst($row->status))
                ->rawColumns(['action'])
                ->make(true);
        }
    }
}
