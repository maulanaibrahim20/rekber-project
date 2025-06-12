<?php

namespace App\Http\Controllers\Api;

use App\Facades\Message;
use App\Http\Controllers\Controller;
use App\Models\ProductComments;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CommentController extends Controller
{
    protected $productComment;

    public function __construct()
    {
        $this->productComment = new ProductComments();
    }

    public function index(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product_id'    => 'required|exists:products,id',
            'per_page'      => 'nullable',
        ]);

        if ($validator->fails()) {
            return Message::validator('Validation failed', $validator->errors());
        }

        $data = $this->productComment->where('product_id', $request->product_id)->paginate($request->per_page ?? 10);

        return Message::paginate('Comments retrieved successfully', $data);
    }
}
