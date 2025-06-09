<?php

namespace App\Http\Controllers\Web;

use App\Enum\Status;
use App\Facades\Message;
use App\Http\Controllers\Controller;
use App\Models\Faq;
use App\Models\FaqCategory;
use App\Rules\ValidateStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\DataTables;

class FaqController extends Controller
{
    protected $faq, $faqCategory;

    public function __construct()
    {
        $this->faq = new Faq();
        $this->faqCategory = new FaqCategory();
    }

    public function getData(Request $request, $slug)
    {
        $faqCategory = $this->faqCategory->where('slug', $slug)->firstOrFail();

        if ($request->ajax()) {
            $data = $faqCategory->faqs()->select(['id', 'question', 'answer', 'status']);

            return DataTables::of($data)
                ->addColumn('action', function ($row) {
                    return '
                    <div class="d-flex justify-content-center">
                        <a href="#" class="btn btn-primary me-1 open-global-modal"
                            data-url="' . route('faq.edit', $row->id) . '" title="Edit">
                            <i class="fas fa-edit"></i>
                        </a>
                        <a href="#" class="btn btn-danger btn-delete-faq" data-id="' . $row->id . '" title="Delete">
                            <i class="fas fa-trash"></i>
                        </a>
                    </div>';
                })
                ->editColumn('status', function ($row) {
                    $label = Status::label('faqStatus', $row->status);
                    $class = $row->status == 1 ? 'badge bg-success text-white' : 'badge bg-danger text-white';
                    return "<span class='{$class}'>{$label}</span>";
                })
                ->rawColumns(['action', 'status'])
                ->make(true);
        }
    }

    public function create($slug)
    {
        $category = $this->faqCategory->where('slug', $slug)->firstOrFail();
        $data = [
            'status' => Status::options('faqStatus'),
            'category' => $category,
        ];
        return view('admin.pages.content-management.faq.create', $data);
    }


    public function store(Request $request, $slug)
    {
        DB::beginTransaction();
        $validator = Validator::make($request->all(), [
            'question'      => 'required|string|max:255',
            'answer'        => 'required|string',
            'category_id'   => 'required|exists:faq_categories,id',
            'status'        => ['required', 'string', new ValidateStatus('faqStatus')],
        ]);

        if ($validator->fails()) {
            return Message::validator(
                $validator->errors()->first(),
            );
        }

        $faqCategory = $this->faqCategory->where('slug', $slug)->firstOrFail();

        try {
            $this->faq->create([
                'question'      => $request->question,
                'answer'        => $request->answer,
                'status'        => $request->status,
                'category_id'   => $faqCategory->id
            ]);

            DB::commit();

            return Message::success('FAQ Category berhasil ditambahkan!');
        } catch (\Exception $e) {
            DB::rollBack();
            return Message::error('Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        $faq = $this->faq->findOrFail($id);
        $category = $faq->category;
        $status = Status::options('faqStatus');

        return view('admin.pages.content-management.faq.edit', compact('faq', 'category', 'status'));
    }

    public function update(Request $request, $id)
    {
        DB::beginTransaction();

        $validator = Validator::make($request->all(), [
            'question' => 'required|string|max:255',
            'answer'   => 'required|string',
            'status'   => ['required', 'string', new ValidateStatus('faqStatus')],
        ]);

        if ($validator->fails()) {
            return Message::validator(
                $validator->errors()->first(),
            );
        }

        $faq = $this->faq->findOrFail($id);

        try {
            $faq->update([
                'question' => $request->question,
                'answer'   => $request->answer,
                'status'   => $request->status,
            ]);

            DB::commit();
            return Message::success('FAQ berhasil diupdate!');
        } catch (\Exception $e) {
            DB::rollBack();
            return Message::error('Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $faq = $this->faq->findOrFail($id);

        try {
            $faq->delete();
            return response()->json(['message' => 'FAQ berhasil dihapus.'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
    }
}
