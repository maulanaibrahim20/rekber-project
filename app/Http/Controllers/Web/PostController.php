<?php

namespace App\Http\Controllers\Web;

use App\Enum\Status;
use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Rules\ValidateStatus;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Str;

class PostController extends Controller
{
    public function index()
    {
        return view('admin.pages.post.index');
    }

    public function getData(Request $request)
    {
        $data = Post::with('user')->select([
            'id',
            'user_id',
            'caption',
            'location',
            'status',
            'created_at',
            'uuid'
        ])->latest();

        return DataTables::of($data)
            ->addIndexColumn()
            ->addColumn('user', function ($row) {
                return $row->user->name ?? '-';
            })
            ->addColumn('caption', function ($row) {
                $text = Str::limit(strip_tags($row->caption), 50);
                return '<span class="text-primary caption-clickable" style="cursor:pointer" data-id="' . $row->id . '">' . $text . '</span>';
            })
            ->editColumn('location', fn($row) => $row->location ?: '-')
            ->editColumn('status', function ($row) {
                $label = Status::label('productStatus', $row->status) ?? 'Unknown';
                $badgeClass = match ($label) {
                    'PUBLISHED' => 'bg-primary',
                    'DRAFT' => 'bg-warning text-dark',
                    'ARCHIVED' => 'bg-danger',
                    'BLOCKED' => 'bg-dark',
                    default => 'bg-secondary',
                };
                return '<span class="badge text-white ' . $badgeClass . '">' . ucfirst(strtolower($label)) . '</span>';
            })
            ->editColumn('created_at', fn($row) => Carbon::parse($row->created_at)->format('d M Y'))
            ->addColumn('action', function ($row) {
                $url = route('post.show', $row->uuid);
                return '<div class="d-flex justify-content-center">
                        <a href="' . $url . '" class="btn btn-info" title="Show">
                            <i class="fas fa-eye"></i>
                        </a>
                    </div>';
            })
            ->rawColumns(['caption', 'status', 'action'])
            ->make(true);
    }

    public function show($uuid)
    {
        $post = Post::with([
            'user',
            'media',
            'likes',
            'comments',
            'tags'
        ])->where('uuid', $uuid)->firstOrFail();

        return view('admin.pages.post.show', compact('post'));
    }

    public function showModal($id)
    {
        $post = Post::with([
            'user:id,name',
            'media:id,post_id,file_url,file_type',
            'likes',
            'comments.user:id,name'
        ])->findOrFail($id);
        return view('admin.pages.post.modal-show', compact('post'));
    }

    public function updateStatus(Request $request, $uuid)
    {
        $request->validate([
            'status'        => ['nullable', 'string', new ValidateStatus('postStatus')],
            'status_reason' => 'nullable|string|max:500'
        ]);

        $product = Post::where('uuid', $uuid)->firstOrFail();

        $product->update([
            'status' => $request->status,
            // 'reason' => $request->status_reason
        ]);

        return redirect()->back()->with('success', 'Product status updated successfully!');
    }
}
