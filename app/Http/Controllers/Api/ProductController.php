<?php

namespace App\Http\Controllers\Api;

use App\Enum\Status;
use App\Facades\Message;
use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use App\Models\Tag;
use App\Rules\ValidateStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    protected $product;

    public function __construct()
    {
        $this->product = new Product();
    }

    public function index(Request $request)
    {
        $query = $this->product;

        if (Auth::check()) {
            $query = $query->where('user_id', Auth::user()->id);
        } else {
            $status = Status::fromString('productStatus', 'PUBLISHED') ?? 1;
            $query = $query->where('status', $status);
        }

        $data = $query->with(['images', 'tags'])->paginate($request->per_page ?? 10);

        return Message::paginate('Products retrieved successfully', new ProductResource($data));
    }

    public function store(Request $request)
    {
        DB::beginTransaction();

        $validator = Validator::make($request->all(), [
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string',
            'location'    => 'nullable|string|max:255',
            'price'       => 'required|numeric|min:0',
            'status'      => ['nullable', 'string', new ValidateStatus('productStatus')],
            'priority'    => 'nullable|integer',
            'tags'        => 'array',
            'tags.*'      => ['string', 'max:50', 'regex:/^[a-zA-Z0-9-_]+$/'],
            'images'      => 'required|array',
            'images.*'    => 'image|max:2048'
        ]);

        if ($validator->fails()) {
            return Message::validator($validator->errors()->first(), isList: true);
        }

        try {
            $statusCode = Status::fromString('productStatus', $request->status ?? 'PUBLISHED') ?? 1;

            $product = Product::create([
                'user_id'       => Auth::user()->id,
                'name'          => $request->name,
                'description'   => $request->description,
                'location'      => $request->location,
                'price'         => $request->price,
                'status'        => $statusCode,
                'priority'      => $request->priority ?? 0,
            ]);

            if ($request->has('tags') && is_array($request->tags)) {
                $tagIds = collect($request->tags)->map(function ($name) {
                    return Tag::firstOrCreate(['name' => $name])->id;
                });
                $product->tags()->sync($tagIds);
            }

            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $file) {
                    $path = $file->store('products', 'public');
                    $product->images()->create([
                        'image' => $path,
                    ]);
                }
            }

            DB::commit();

            return Message::success('Product created successfully', new ProductResource($product->load(['images', 'tags'])));
        } catch (\Throwable $th) {
            DB::rollBack();
            return Message::error('An error occurred while processing your request: ' . $th->getMessage());
        }
    }

    public function show($uuid)
    {
        try {
            $product = $this->product->with(['images', 'tags', 'likes', 'comments.user'])
                ->where('uuid', $uuid)
                ->orWhere('id', $uuid)
                ->firstOrFail();

            return Message::success('Product detail retrieved successfully', new ProductResource($product));
        } catch (\Throwable $th) {
            return Message::error('Product not found or an error occurred' . $th->getMessage());
        }
    }

    public function edit($uuid)
    {
        try {
            $product = $this->product->with(['images', 'tags'])
                ->where('uuid', $uuid)
                ->orWhere('id', $uuid)
                ->firstOrFail();

            if ($product->user_id !== Auth::user()->id) {
                return Message::error('Unauthorized access to edit this product');
            }

            return Message::success('Product ready for edit', $product);
        } catch (\Throwable $th) {
            return Message::error('Product not found or an error occurred');
        }
    }

    public function update(Request $request, $uuid)
    {
        $validator = Validator::make($request->all(), [
            'name'        => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'location'    => 'nullable|string|max:255',
            'price'       => 'sometimes|required|numeric|min:0',
            'status'      => ['nullable', 'string', new ValidateStatus('productStatus')],
            'priority'    => 'nullable|integer',
            'tags'        => 'array',
            'tags.*'      => ['string', 'max:50', 'regex:/^[a-zA-Z0-9-_]+$/'],
            'images'      => 'nullable|array',
            'images.*'    => 'image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($validator->fails()) {
            return Message::validator($validator->errors()->first(), isList: true);
        }

        DB::beginTransaction();

        try {
            $product = $this->product->where('uuid', $uuid)
                ->orWhere('id', $uuid)
                ->firstOrFail();

            if ($product->user_id !== Auth::user()->id) {
                return Message::error('Unauthorized access to update this product');
            }

            $product->update([
                'name'        => $request->name ?? $product->name,
                'description' => $request->description ?? $product->description,
                'location'    => $request->location ?? $product->location,
                'price'       => $request->price ?? $product->price,
                'status'      => $request->has('status')
                    ? (Status::fromString('productStatus', $request->status) ?? $product->status)
                    : $product->status,
                'priority'    => $request->priority ?? $product->priority,
            ]);

            if ($request->has('tags') && is_array($request->tags)) {
                $tagIds = collect($request->tags)->map(function ($name) {
                    return Tag::firstOrCreate(['name' => $name])->id;
                });
                $product->tags()->sync($tagIds);
            }

            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $file) {
                    $path = $file->store('products', 'public');
                    $product->images()->create([
                        'image' => $path,
                    ]);
                }
            }

            DB::commit();

            return Message::success('Product updated successfully', new ProductResource($product->load(['images', 'tags'])));
        } catch (\Throwable $th) {
            DB::rollBack();
            return Message::error('An error occurred while updating product: ' . $th->getMessage());
        }
    }

    public function destroy($uuid)
    {
        try {
            $product = $this->product->where('uuid', $uuid)
                ->orWhere('id', $uuid)
                ->firstOrFail();

            if ($product->user_id !== Auth::user()->id) {
                return Message::error('Unauthorized access to delete this product');
            }

            $product->tags()->detach();
            $product->images()->delete();
            $product->likes()->delete();
            $product->comments()->delete();
            $product->delete();

            return Message::success('Product deleted successfully');
        } catch (\Throwable $th) {
            return Message::error('An error occurred while deleting product: ' . $th->getMessage());
        }
    }
}
