<?php

namespace App\Http\Controllers\Web;

use App\Enum\Status;
use App\Facades\Message;
use App\Http\Controllers\Controller;
use App\Models\FaqCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Str;

class FaqCategoryController extends Controller
{
    protected $faqCategory;

    public function __construct()
    {
        $this->faqCategory = new FaqCategory();
    }
    public function index()
    {
        return view('admin.pages.content-management.faq-category.index');
    }

    public function getData(Request $request)
    {
        if ($request->ajax()) {
            $data = $this->faqCategory->select(['id', 'name', 'slug', 'created_at']);

            return DataTables::of($data)
                ->addColumn('faq_count', fn($row) => $row->faqs()->count())
                ->addColumn('action', function ($row) {
                    return '
                <div class="d-flex justify-content-center">
                    <a href="' . route('faq.category.show', $row->slug) . '" class="btn btn-success me-1" title="Lihat Detail">
                            <i class="fas fa-list"></i>
                    </a>
                    <a href="#" class="btn btn-primary me-1 open-global-modal" data-url="' . route('faq.category.edit', $row->slug) . '" title="Edit">
                        <i class="fas fa-edit"></i>
                    </a>
                    <a href="#" class="btn btn-danger btn-delete-category" data-id="' . $row->id . '" title="Delete">
                        <i class="fas fa-trash"></i>
                    </a>
                </div>';
                })
                ->editColumn('created_at', fn($row) => $row->created_at->format('Y-m-d H:i'))
                ->rawColumns(['action'])
                ->make(true);
        }
    }

    public function create()
    {
        return view('admin.pages.content-management.faq-category.create');
    }

    public function store(Request $request)
    {
        DB::beginTransaction();
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:faq_categories,name',
        ]);

        if ($validator->fails()) {
            return Message::validator($validator);
        }

        try {
            $slug = Str::slug($request->name);

            $originalSlug = $slug;
            $count = 1;
            while (FaqCategory::where('slug', $slug)->exists()) {
                $slug = $originalSlug . '-' . $count++;
            }

            FaqCategory::create([
                'name' => $request->name,
                'slug' => $slug,
            ]);


            DB::commit();

            return Message::success('FAQ Category berhasil ditambahkan!');
        } catch (\Exception $e) {
            DB::rollBack();
            return Message::error('Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function show($slug)
    {
        $faqCategory = FaqCategory::where('slug', $slug)->firstOrFail();
        $faqs = $faqCategory->faqs()->latest()->get();

        return view('admin.pages.content-management.faq.index', compact('faqCategory', 'faqs'));
    }

    public function edit($slug)
    {
        $faqCategory = FaqCategory::where('slug', $slug)->firstOrFail();
        return view('admin.pages.content-management.faq-category.edit', compact('faqCategory'));
    }

    public function update(Request $request, $id)
    {
        DB::beginTransaction();
        $faqCategory = FaqCategory::findOrFail($id);;

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:faq_categories,name,' . $faqCategory->id,
        ]);

        if ($validator->fails()) {
            return Message::validator($validator);
        }

        try {
            $faqCategory->name = $request->name;

            $newSlug = Str::slug($request->name);
            if ($newSlug !== $faqCategory->slug) {
                $originalSlug = $newSlug;
                $count = 1;
                while (FaqCategory::where('slug', $newSlug)->where('id', '!=', $faqCategory->id)->exists()) {
                    $newSlug = $originalSlug . '-' . $count++;
                }
                $faqCategory->slug = $newSlug;
            }

            $faqCategory->save();

            DB::commit();
            return Message::success('FAQ Category berhasil diperbarui!');
        } catch (\Exception $e) {
            DB::rollBack();
            return Message::error('Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $faqCategory = FaqCategory::findOrFail($id);
            $faqCategory->faqs()->delete();
            $faqCategory->delete();

            DB::commit();
            return Message::success('FAQ Category berhasil dihapus!');
        } catch (\Exception $e) {
            DB::rollBack();
            return Message::error('Gagal menghapus: ' . $e->getMessage());
        }
    }
}
