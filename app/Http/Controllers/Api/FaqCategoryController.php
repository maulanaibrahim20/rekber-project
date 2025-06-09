<?php

namespace App\Http\Controllers\Api;

use App\Facades\Message;
use App\Http\Controllers\Controller;
use App\Http\Resources\FaqCategoryResource;
use App\Models\FaqCategory;
use Illuminate\Http\Request;

class FaqCategoryController extends Controller
{
    protected $category;

    public function __construct()
    {
        $this->category = new FaqCategory();
    }

    public function index(Request $request)
    {
        $categories = $this->category->with(['faqs' => function ($query) {
            $query->where('status', 1);
        }])->get();

        return Message::success('FAQ categories retrieved successfully', FaqCategoryResource::collection($categories));
    }

    public function show($slug)
    {
        $category = $this->category->where('slug', $slug)
            ->with(['faqs' => function ($query) {
                $query->where('status', 1);
            }])->first();

        if (!$category) {
            return Message::error('FAQ category not found.');
        }

        return Message::success('FAQ category retrieved successfully', new FaqCategoryResource($category));
    }
}
