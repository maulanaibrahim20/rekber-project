<?php

namespace App\Http\Controllers\Api;

use App\Enum\Status;
use App\Facades\Message;
use App\Http\Controllers\Controller;
use App\Http\Resources\PostResource;
use App\Models\Post;
use App\Models\PostMedia;
use App\Models\Tag;
use App\Rules\ValidateStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;

class PostController extends Controller
{
    protected $post, $postMedia, $tag;

    public function __construct()
    {
        $this->post = new Post();
        $this->postMedia = new PostMedia();
        $this->tag = new Tag();
    }
    public function index(Request $request)
    {
        try {
            $posts = $this->post->with(['media', 'tags', 'likes', 'comments'])
                ->when($request->user_id, fn($q) =>
                $q->where('user_id', $request->user_id))
                ->where('status', Status::fromString('postStatus', $request->status ?? 'PUBLISHED'))
                ->latest()
                ->paginate($request->get('per_page', 10));

            return Message::paginate('Post list retrieved successfully', PostResource::collection($posts));
        } catch (\Throwable $th) {
            return Message::error('Failed to load posts: ' . $th->getMessage());
        }
    }


    public function store(Request $request)
    {
        DB::beginTransaction();

        $validator = Validator::make($request->all(), [
            'caption'   => 'nullable|string',
            'location'  => 'nullable|string|max:255',
            'status'    => ['nullable', 'string', new ValidateStatus('postStatus')],
            'tags'      => 'array',
            'tags.*'    => ['string', 'max:50', 'regex:/^[a-zA-Z0-9-_]+$/'],
            'media'     => 'required|array',
            'media.*'   => 'file|mimes:jpg,jpeg,png,mp4|max:51200',
        ]);

        if ($validator->fails()) {
            return Message::validator($validator->errors()->first(), isList: true);
        }

        try {
            $statusCode = Status::fromString('postStatus', $request->status ?? 'PUBLISHED') ?? 'published';

            $post = $this->post->create([
                'user_id'   => Auth::id(),
                'caption'   => $request->caption,
                'location'  => $request->location,
                'status'    => $statusCode,
            ]);

            if ($request->has('tags')) {
                $tagIds = collect($request->tags)->map(function ($name) {
                    return $this->tag->firstOrCreate([
                        'name' => $name,
                        'slug' => Str::slug($name)
                    ])->id;
                });
                $post->tags()->sync($tagIds);
            }

            if ($request->hasFile('media')) {
                foreach ($request->file('media') as $index => $file) {
                    $mime = $file->getMimeType();
                    $fileType = Str::startsWith($mime, 'video') ? 'video' : 'image';

                    $subfolder = $fileType === 'video' ? 'videos' : 'images';
                    $path = $file->store("posts/{$subfolder}", 'public');

                    $post->media()->create([
                        'file_url'    => $path,
                        'file_type'   => $fileType,
                        'media_order' => $index,
                    ]);
                }
            }

            DB::commit();
            return Message::success('Post created successfully', new PostResource($post->load(['media', 'tags'])));
        } catch (\Throwable $th) {
            DB::rollBack();
            return Message::error('An error occurred while processing your request: ' . $th->getMessage());
        }
    }

    public function show($uuid)
    {
        try {
            $post = $this->post->with(['media', 'tags'])->where('uuid', $uuid)->orWhere('id', $uuid)->first();

            if (!$post) {
                return Message::error('Post not found');
            }

            return Message::success('Post details retrieved successfully', new PostResource($post));
        } catch (\Throwable $th) {
            return Message::error('Post not found or error: ' . $th->getMessage());
        }
    }

    public function update(Request $request, $uuid)
    {
        DB::beginTransaction();

        $validator = Validator::make($request->all(), [
            'caption'     => 'nullable|string',
            'location'    => 'nullable|string|max:255',
            'status'      => ['nullable', 'string', new ValidateStatus('postStatus')],
            'tags'        => 'array',
            'tags.*'      => ['string', 'max:50', 'regex:/^[a-zA-Z0-9-_]+$/'],
        ]);

        if ($validator->fails()) {
            return Message::validator($validator->errors()->first(), isList: true);
        }

        try {
            $post = $this->post->where('uuid', $uuid)->orWhere('id', $uuid)->first();

            if (!$post) {
                return Message::error('Post not found');
            }

            $post->update([
                'caption'    => $request->caption ?? $post->caption,
                'location'   => $request->location ?? $post->location,
                'status'     => Status::fromString('postStatus', $request->status ?? $post->status),
            ]);

            if ($request->has('tags')) {
                $tagIds = collect($request->tags)->map(function ($name) {
                    return Tag::firstOrCreate([
                        'name' => $name,
                        'slug' => Str::slug($name)
                    ])->id;
                });
                $post->tags()->sync($tagIds);
            }

            DB::commit();
            return Message::success('Post updated successfully', new PostResource($post->load(['media', 'tags'])));
        } catch (\Throwable $th) {
            DB::rollBack();
            return Message::error('Failed to update post: ' . $th->getMessage());
        }
    }

    public function destroy($uuid)
    {
        DB::beginTransaction();

        try {
            $post = $this->post->with('media')->where('uuid', $uuid)->orWhere('id', $uuid)->first();

            if (!$post) {
                return Message::error('Post not found');
            }

            foreach ($post->media as $media) {
                if ($media->file_url && Storage::disk('public')->exists($media->file_url)) {
                    Storage::disk('public')->delete($media->file_url);
                }
            }

            $post->delete();

            DB::commit();
            return Message::success('Post deleted successfully');
        } catch (\Throwable $th) {
            DB::rollBack();
            return Message::error('Failed to delete post: ' . $th->getMessage());
        }
    }

    public function destroyMedia($id)
    {
        DB::beginTransaction();

        try {
            $media = $this->postMedia->findOrFail($id);

            if (Storage::disk('public')->exists($media->file_url)) {
                Storage::disk('public')->delete($media->file_url);
            }

            $media->delete();

            DB::commit();
            return Message::success('Media deleted successfully');
        } catch (\Throwable $th) {
            DB::rollBack();
            return Message::error('Failed to delete media: ' . $th->getMessage());
        }
    }
}
