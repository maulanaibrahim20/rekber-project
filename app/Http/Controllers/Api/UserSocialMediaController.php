<?php

namespace App\Http\Controllers\Api;

use App\Facades\Message;
use App\Http\Controllers\Controller;
use App\Models\UserSocialMedia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class UserSocialMediaController extends Controller
{
    protected $userSocialMedia;

    public function __construct()
    {
        $this->userSocialMedia = new UserSocialMedia();
    }
    public function index()
    {
        $data = $this->userSocialMedia->Where('user_id', Auth::user()->id)->get();

        return Message::success('Success retrieving data social media', $data);
    }

    public function store(Request $request)
    {
        DB::beginTransaction();

        $validator = Validator::make($request->all(), [
            'link'      => 'required|string',
            'type'      => 'required|string',
            'path'      => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return Message::validator('Validation failed', $validator->errors());
        }

        try {
            $data = $this->userSocialMedia->create([
                'user_id'   => Auth::user()->id,
                'link'      => $request->link,
                'type'      => $request->type,
                'path'      => $request->path,
            ]);

            DB::commit();
            return Message::success('Success storing data social media', $data);
        } catch (\Throwable $th) {
            DB::rollBack();
            return Message::error('Error storing data social media' . $th->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        DB::beginTransaction();

        $validator = Validator::make($request->all(), [
            'link'      => 'required|string',
            'type'      => 'required|string',
            'path'      => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return Message::validator('Validation failed', $validator->errors());
        }
        $data = $this->userSocialMedia->where('id', $id)->first();

        try {
            $data->update([
                'user_id'   => Auth::user()->id,
                'link'      => $request->link,
                'type'      => $request->type,
                'path'      => $request->path,
            ]);

            DB::commit();
            return Message::success('Success updating data social media', $data);
        } catch (\Throwable $th) {
            DB::rollBack();
            return Message::error('Error updating data social media' . $th->getMessage());
        }
    }


    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $data = $this->userSocialMedia->where('id', $id)->delete();
            DB::commit();
            return Message::success('Success deleting data social media', $data);
        } catch (\Throwable $th) {
            DB::rollBack();
            return Message::error('Error deleting data social media' . $th->getMessage());
        }
    }
}
