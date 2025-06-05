<?php

namespace App\Http\Controllers\Web;

use App\Enum\Status;
use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class ProductController extends Controller
{
    protected $product;

    public function __construct()
    {
        $this->product = new Product();
    }

    public function index()
    {
        return view('admin.pages.product.index');
    }

    public function getData(Request $request)
    {
        if ($request->ajax()) {
            $data = $this->product->select([
                'id',
                'name',
                'price',
                'location',
                'priority',
                'status',
                'created_at',
                'uuid',
            ])->latest();

            return DataTables::of($data)
                ->addColumn('priority', function ($row) {
                    return $row->priority == 1 ? '<span class="badge text-white bg-success">Sticky</span>' : '<span class="badge text-white bg-secondary">Normal</span>';
                })
                ->editColumn('status', function ($row) {
                    $label = $row->status['value'];

                    $badgeClass = match ($label) {
                        'PUBLISHED' => 'bg-primary',
                        'DRAFT' => 'bg-warning text-dark',
                        'ARCHIVED' => 'bg-danger',
                        'BLOCKED' => 'bg-dark',
                        default => 'bg-secondary',
                    };

                    return '<span class="badge text-white ' . $badgeClass . '">' . ucfirst(strtolower($label)) . '</span>';
                })
                ->addColumn('action', function ($row) {
                    $url = route('product.show', $row->uuid);
                    return '
                    <div class="d-flex justify-content-center">
                        <a href="' . $url . '" class="btn btn-info" title="Show">
                            <i class="fas fa-eye"></i>
                        </a>
                    </div>';
                })

                ->rawColumns(['priority', 'status', 'action'])
                ->make(true);
        }
    }

    public function show($uuid)
    {
        $product = $this->product->with([
            'user',
            'images',
            'likes',
            'comments',
            'tags'
        ])->where('uuid', $uuid)->firstOrFail();

        return view('admin.pages.product.show', compact('product'));
    }

    public function updateStatus(Request $request, Product $product)
    {
        $request->validate([
            'status' => 'required|in:PUBLISHED,DRAFT,ARCHIVED,BLOCKED',
            'status_reason' => 'nullable|string|max:500'
        ]);

        $product->update([
            'status' => $request->status,
            'status_reason' => $request->status_reason
        ]);

        return redirect()->back()->with('success', 'Product status updated successfully!');
    }
}
