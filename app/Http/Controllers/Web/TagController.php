<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Tag;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class TagController extends Controller
{
    protected $tag;

    public function __construct()
    {
        $this->tag = new Tag();
    }
    public function index()
    {
        return view('admin.pages.tag.index');
    }

    public function getData(Request $request)
    {
        if ($request->ajax()) {
            $data = $this->tag->select([
                'id',
                'name',
            ])->latest();

            return DataTables::of($data)
                ->addColumn('action', function ($row) {
                    return '
                        <div class="d-flex justify-content-center">
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
}
