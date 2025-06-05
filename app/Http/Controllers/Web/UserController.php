<?php

namespace App\Http\Controllers\Web;

use App\Enum\Status;
use App\Facades\Message;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Rules\ValidateStatus;
use App\Trait\AvatarInitialTrait;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Str;

class UserController extends Controller
{
    protected $user;

    public function __construct()
    {
        $this->user = new User();
    }

    use AvatarInitialTrait;
    public function index()
    {
        return view('admin.pages.user.index');
    }

    public function getData(Request $request)
    {
        if ($request->ajax()) {
            $data = $this->user->select([
                'id',
                'name',
                'email',
                'username',
                'phone',
                'gender',
                'status',
                'is_private',
                'profile_picture',
                'created_at'
            ])->orderByDesc('created_at');

            return DataTables::of($data)
                ->addColumn('action', function ($row) {
                    return '
                    <div class="d-flex justify-content-center">
                        <a href="#" class="btn btn-primary me-1 open-global-modal" title="Edit" data-url="' . route('user.edit', $row->id) . '">
                            <i class="fas fa-edit"></i>
                        </a>
                        <a href="javascript:void(0);" class="btn btn-danger btn-delete-user" data-id="' . $row->id . '" title="Delete">
                            <i class="fas fa-trash"></i>
                        </a>
                    </div>
                ';
                })
                ->editColumn('profile_picture', function ($row) {
                    return $this->generateAvatarHtml($row->profile_picture, $row->name);
                })
                ->editColumn('gender', fn($row) => $row->gender ? ucfirst($row->gender) : '-')
                ->editColumn('is_private', fn($row) => $row->is_private ? 'Yes' : 'No')
                ->editColumn('status', function ($row) {
                    return $row->status['value'];
                })
                ->editColumn('created_at', fn($row) => $row->created_at->format('Y-m-d H:i'))
                ->rawColumns(['action', 'profile_picture'])
                ->make(true);
        }
    }

    public function create()
    {
        $data['status'] = Status::options('userStatus');
        unset($data['status'][3]);
        return view('admin.pages.user.create', $data);
    }

    public function store(Request $request)
    {
        DB::beginTransaction();

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'username' => [
                'required',
                'string',
                'min:3',
                'max:30',
                'unique:users,username',
                'regex:/^(?!.*\.\.)(?!.*\.$)(?!^\.)[a-zA-Z0-9._]+$/',
            ],
            'phone' => 'nullable|string|min:10|max:15',
            'gender' => 'nullable|in:male,female',
            'password' => 'required|min:6|confirmed',
            'is_private' => 'nullable|boolean',
            'status'      => ['required', 'string', new ValidateStatus('userStatus')],
        ], [
            'username.regex' => 'Username hanya boleh huruf, angka, titik, dan underscore, tidak boleh diawali/diakhiri titik atau mengandung dua titik berturut-turut.',
        ]);

        if ($validator->fails()) {
            if ($request->ajax()) {
                return Message::validator(
                    $validator->errors()->first(),
                );
            }

            return back()->withErrors($validator)->withInput();
        }

        try {
            $user = $this->user->create([
                'name' => $request->name,
                'email' => $request->email,
                'username' => $request->username,
                'linkname' => Str::slug($request->username),
                'phone' => $request->phone,
                'gender' => $request->gender,
                'status' => $request->status,
                'is_private' => $request->is_private ?? false,
                'password' => Hash::make($request->password),
                'email_verified_at' => Carbon::now(),
                'remember_token' => Str::random(10),
            ]);

            $user->forceFill([
                'email_verified_at' => Carbon::now(),
                'remember_token' => Str::random(10),
            ])->save();


            DB::commit();

            if ($request->ajax()) {
                return Message::success('User berhasil ditambahkan.');
            }

            return redirect()->route('user')->with('success', 'User berhasil ditambahkan.');
        } catch (\Throwable $th) {
            DB::rollBack();

            if ($request->ajax()) {
                return Message::error('Terjadi kesalahan saat menyimpan data.');
            }

            return back()->with('error', 'Something went wrong.');
        }
    }

    public function edit($id)
    {
        $data['user'] = $this->user->findOrFail($id);
        $data['status'] = Status::options('userStatus');
        return view('admin.pages.user.edit', $data);
    }

    public function update(Request $request, $id)
    {
        DB::beginTransaction();
        $user = $this->user->findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'username' => [
                'required',
                'string',
                'min:3',
                'max:30',
                'unique:users,username,' . $id,
                'regex:/^(?!.*\.\.)(?!.*\.$)(?!^\.)[a-zA-Z0-9._]+$/',
            ],
            'phone' => 'nullable|string|min:10|max:15',
            'gender' => 'nullable|in:male,female',
            'password' => 'nullable|min:6|confirmed',
            'is_private' => 'nullable|boolean',
            'status'      => ['nullable', 'string', new ValidateStatus('userStatus')],
        ], [
            'username.regex' => 'Username hanya boleh huruf, angka, titik, dan underscore, tidak boleh diawali/diakhiri titik atau mengandung dua titik berturut-turut.',
        ]);

        if ($validator->fails()) {
            return Message::validator(
                $validator->errors()->first(),
            );
        }

        try {
            $user->update([
                'name' => $request->name,
                'email' => $request->email,
                'username' => $request->username,
                'linkname' => Str::slug($request->username),
                'phone' => $request->phone,
                'gender' => $request->gender,
                'status' => $request->status,
                'is_private' => $request->is_private ?? false,
                'password' => $request->filled('password') ? Hash::make($request->password) : $user->password,
            ]);
            DB::commit();

            return Message::success('User berhasil diperbarui.');
        } catch (\Throwable $th) {
            DB::rollBack();
            return Message::error('Terjadi kesalahan saat memperbarui data user.');
        }
    }

    public function destroy($id)
    {
        $user = $this->user->findOrFail($id);

        try {
            $user->delete();

            return Message::success('User berhasil dihapus.');
        } catch (\Throwable $th) {
            return Message::error('Terjadi kesalahan saat menghapus user.');
        }
    }
}
