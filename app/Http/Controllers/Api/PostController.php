<?php

namespace App\Http\Controllers\API;

use App\User;
use Exception;
use Validator;
use App\Models\Like;
use App\Models\Post;
use Illuminate\Http\Request;
use App\Collections\Constants;
use Illuminate\Support\Carbon;
use App\Collections\StatusCodes;
use App\Models\PostNotification;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Api\PostNotificationController;

class PostController extends Controller
{

    public function __construct(){
        $this->middleware('comment_like', ['only' => ['postLike']]);
        $this->middleware('post', ['only' => ['store']]);

    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
    }

    public function usersPost()
    {
        $id = Auth()->user()->id;

        // if($post = Redis::get('posts.'.$id)){
        //     return response()->json([
        //         "status" => "success",
        //         "message" => "All Posts fetched successfully.",
        //         "data" => json_decode($post)
        //     ], StatusCodes::SUCCESS);
        // }

        $post = Post::where('user_id', $id)->with(array('Comment', 'user'))
        ->with(['polls'=>function($query){
            return $query->with('votes');
        }])
        ->with(['likes' => function($query){
            return $query->where('liked', 1)->with('user');
        }])->latest()->paginate(Constants::PAGE_LIMIT);

        //store data in redis for 24hrs
        // Redis::setex('posts.'.$id, 60*60*24, $post);

        return response()->json([
            "status" => "success",
            "message" => "All Posts fetched successfully.",
            "data" => $post
        ], StatusCodes::SUCCESS);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'poll' => ['array'],
            'poll_duration' => ['int'],
            'description' => ['required_without_all:images,videos']

        ]);

        if ($validator->fails())
        {
            return response()->json([
                "status" => "failure",
                "message" => "Error.",
                "data" => $validator->errors(),
                "user" => $request->user()
            ], StatusCodes::BAD_REQUEST);
        }
        $images = [];
        $videos = [];
        $description = "";
        $id = Auth()->user()->id;
        $post = new Post;

        if (!empty($request->images)){
            $images = $request->input('images');
        }
        if (!empty($request->videos)){
            $videos = $request->input('videos');
        }
        if (!empty($request->description)){
            $description = $request->input('description');
        }
        $post->description = $request->description;
        $post->images = $images;
        $post->videos = $videos;
        $post->user_id = $id;
        $post->description = $description;
        $post->poll = $request->input('poll');

        if($request->input('height') != null ){
            $post->height = $request->input('height');
        }
        if($request->input('width') != null ){
            $post->width = $request->input('width');
        }
        if($request->input('orientation') != null ){
            $post->orientation = $request->input('orientation');
        }
        if($request->input('poll_duration') != null ){
            $post->poll_expiry = Carbon::now()->addDays($request->input('poll_duration'));
        }

        $post->save();
        if (!empty($request->poll)){

            $data = [];
            foreach ($request->poll as $choice){
                $the_choice = [
                    'post_id' => $post->id,
                    'choices' => $choice
                ];
                array_push($data, $the_choice);
            }
            $post->polls()->createMany($data);
        }

        return response()->json([
            "status" => "success",
            "message" => "Post created successfully.",
            "data" => Post::find($post->id)->load('user')->load('polls')
        ], StatusCodes::CREATED);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function show(Post $post)
    {
        try{

            $_post = $post->load(array('Comment', 'user'))->load(['likes' => function($query){
                return $query->where('liked', 1)->with('user');
            }])->load(['polls' => function($query){
                return $query->with('votes');
            }]);

            return response()->json([
                "status" => "success",
                "message" => "Post fetched successfully.",
                "data" => $_post
            ], StatusCodes::SUCCESS);
        }catch(Exception  $e){
            $commonFunction = new CommonFunctionsController;
            $array_json_return =$commonFunction->api_default_fail_response(__function__, $e);
            return response()->json($array_json_return, StatusCodes::BAD_REQUEST);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $post = Post::find($id);
        if (!$post) {
            return response()->json([
                "status" => "failure",
                "message" => "Post not found."
            ], StatusCodes::UNPROCESSABLE);
        }
        $input = $request->all();

        $post->fill($input)->save();
        // Post::where('id',$id)->update($request->all());
        // $post = Post::find($id);

        return response()->json([
            "status" => "success",
            "message" => "Post updated successfully.",
            "data" => $post
        ], StatusCodes::SUCCESS);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $post = Post::find($id);

        if (!$post) {
            return response()->json([
                "status" => "failure",
                "message" => "Post not found."
            ], StatusCodes::UNPROCESSABLE);
        }

        $post->delete();

        return response()->json([
            "status" => "success",
            "message" => "Post deleted successfully."
        ], StatusCodes::SUCCESS);
    }

    //user liking a post
    public function postLike(Request $request)
    {
        $post = Post::find($request->post_id);

        $id = Auth()->user()->id;
        $username = Auth()->user()->username;

        $like = Like::where([
            'post_id' => $request->post_id,
            'user_id' => $id
        ])->first();

        $data = [
            'user_id' => $id,
            'post_id' => $post->id
        ];

        if (!$like) {
            $createPost = Like::create($data);

            $this->initialCount($createPost->post_id);

            // insert into post notification for like
            $this->notify($post, $username, 'liked');

            return response()->json([
                "status" => "success",
                "message" => "Like successfully.",
                "data" => Like::find($createPost->id)->load('user')
            ], StatusCodes::SUCCESS);
        } else {
            if ($like->liked == 1) {
                $like->update(['liked' => 0]);

                $this->decreaseCount($like->post_id);

            // unlike a user post
            // $this->notify($post, $username, 'unlike');

                return response()->json([
                    "status" => "success",
                    "message" => "Unlike successfully",
                    "data" => $like->load('user')
                ], StatusCodes::SUCCESS);
            }
            $like->update(['liked' => 1]);

            $this->increaseCount($like->post_id);

            // Re-liking a post
            $this->notify($post, $username, 'like');

            return response()->json([
                "status" => "success",
                "message" => "Like successfully",
                "data" => $like->load('user')
            ], StatusCodes::SUCCESS);
        }
    }


    public function disLikePost(Request $request)
    {
        $post = Post::find($request->post_id);

        $postId = $request->post_id;

        $postUser = User::find($post->user_id);

        $dislikedUser = Auth()->user()->id;

        if (!$dislikedUser) {
            return response()->json([
                "status" => "failure",
                "message" => "You did not login."
            ], StatusCodes::UNPROCESSABLE);
        }

        $this->increasingDisLikes($dislikedUser, $postUser->id, $postId);

        return response()->json([
            "status" => "success",
            "message" => "Dislike successfully.",
        ], StatusCodes::SUCCESS);
    }


    public function increasingLikes($auth_user_id, $post_user_id, $post)
    {
        $authUser = User::find($auth_user_id);

        $postUser = User::find($post_user_id);

        // $like = DB::table('likes')->where([['liking_userId', $authUser->id], ['user_id', $postUser->id], ['post_id', $post]])->first();
        $like = DB::table('likes')->where([['user_id', $authUser->id], ['post_id', $post]])->first();

        if (!$like) {
            DB::transaction(function ()  use ($postUser, $authUser, $post) {
                DB::table('likes')->insert(['liking_userId' => $authUser->id, 'user_id' => $postUser->id, 'post_id' => $post, 'created_at' => now(), 'updated_at' => now()]);
                DB::table('dislikes')->where([['disliking_userId', $authUser->id], ['user_id', $postUser->id], ['post_id', $post]])->delete();
                $initialCount = DB::table('posts')->where('id', $post)->first();
                DB::table('posts')->where('id', $post)->update(['likesCount' => $initialCount->likesCount + 1, 'dislikesCount' => $initialCount->dislikesCount - 1]);
            });
        }
    }


    public function increasingDisLikes($auth_user_id, $post_user_id, $post)
    {
        $authUser = User::find($auth_user_id);

        $postUser = User::find($post_user_id);

        $dislike = DB::table('dislikes')->where([['disliking_userId', $authUser->id], ['user_id', $postUser->id], ['post_id', $post]])->first();

        if (!$dislike) {
            DB::transaction(function ()  use ($postUser, $authUser, $post) {
                DB::table('dislikes')->insert(['disliking_userId' => $authUser->id, 'user_id' => $postUser->id, 'post_id' => $post, 'created_at' => now(), 'updated_at' => now()]);
                DB::table('likes')->where([['liking_userId', $authUser->id], ['user_id', $postUser->id], ['post_id', $post]])->delete();
                $initialCount = DB::table('posts')->where('id', $post)->first();
                DB::table('posts')->where('id', $post)->update(['likesCount' => $initialCount->likesCount - 1, 'dislikesCount' => $initialCount->dislikesCount + 1]);
            });
        }
    }

    public function initialCount($post_id)
    {
        $post = Post::find($post_id);

        $post->update(['likesCount' => $post->likesCount + 1]);
    }

    public function increaseCount($post_id)
    {
        $post = Post::find($post_id);

        $post->update(['likesCount' => $post->likesCount + 1]);
    }

    public function decreaseCount($post_id)
    {
        $post = Post::find($post_id);

        $post->update(['likesCount' => $post->likesCount - 1]);
    }

    public function notify($post, $username, $type){
        $post_notify = new PostNotificationController();
        $result = $post_notify->store([
            'message'=> "$username $type your post",
            'post_id' => $post->id,
            'user_id' => Auth()->user()->id,
            'broadcast_id' => $post->user->id,
            'post_type_id' => 1,
        ]);
    }

    public function userPost(int $s_id)
    {
        $user = User::find($s_id);


        $post = Post::where('user_id', $user->id)->with('Comment')->with(['user' => function($query){
            $query->with([
                'monetizeBenefits:user_id,benefits',
                'subscriptionBenefits:user_id,benefits',
                ])->with([
                    'subscriptionRates' => function($query){
                        $query->with('subscription:id,name,period');
                    }]);
        }])
        ->with(['polls'=>function($query){
            return $query->with('votes');
        }])
        ->with(['likes' => function($query){
            return $query->where('liked', 1)->with('user');
        }])->latest()->paginate(Constants::PAGE_LIMIT);


        return response()->json([
            "status" => "success",
            "message" => "All Posts fetched successfully.",
            "data" => $post
        ], StatusCodes::SUCCESS);
    }

    public function post(Request $request)
    {

        $validator = Validator::make($request->all(),[
            'poll' => ['array'],
            'poll_duration' => ['int'],
            'videos.*' => ['mimes:mp4,,mov,ogg,qt','max:10000'],
            'images.*' => 'image|mimes:png,jpg|max:5000'

        ]);

        if ($validator->fails())
        {
            return response()->json([
                "status" => "failure",
                "message" => "Error.",
                "data" => $validator->errors()
            ], StatusCodes::BAD_REQUEST);
        }

        $id = Auth()->user()->id;
        $images = [];
        $videos = [];
        $post = new Post;
        if ($request->hasFile('images')){
            foreach($request->file('images') as $file){
                $name= time().'_'.$file->getClientOriginalName();

                $path = "/image/abc.jpg";
                // $path = Storage::disk('s3')->putFile('images', $file, 'public');
                // if (!$path){
                //     return response()->json([
                //         "status" => "failure",
                //         "message" => "File Upload Failed try again",
                //     ], StatusCodes::BAD_REQUEST);
                // }
                $post_data = (object)[];

                    $post_data->id = 1233;
                    $post_data->url = env('AWS_URL').$path;
                    $post_data->height = 4160;
                    $post_data->width = 3120;
                    $post_data->orientation = 'portrait';

                // $data[] = env('AWS_URL').$path;
                $data[] = ($post_data);
            }
            // dd($data);
            $images = ($data);
        }
        if ($request->hasFile('videos')){

            foreach($request->file('videos') as $file){
                $name= time().'_'.$file->getClientOriginalName();
                ini_set('max_execution_time', 300);
                $path = Storage::disk('s3')->putFile('videos', $file, 'public');
                if (!$path){
                    return response()->json([
                        "status" => "failure",
                        "message" => "File Upload Failed try again",
                    ], StatusCodes::BAD_REQUEST);
                }
                $videoData[] = env('AWS_URL').$path;
            }

            $videos = $videoData;
        }

        $post->description = $request->description;
        $post->images = $images;
        $post->videos = $videos;
        $post->user_id = $id;
        $post->poll = $request->input('poll');

        if($request->input('height') != null ){
            $post->height = $request->input('height');
        }
        if($request->input('width') != null ){
            $post->width = $request->input('width');
        }
        if($request->input('orientation') != null ){
            $post->orientation = $request->input('orientation');
        }
        if($request->input('poll_duration') != null ){
            $post->poll_expiry = Carbon::now()->addDays($request->input('poll_duration'));
        }

        $post->save();
        if (!empty($request->poll)){

            $data = [];
            foreach ($request->poll as $choice){
                $the_choice = [
                    'post_id' => $post->id,
                    'choices' => $choice
                ];
                array_push($data, $the_choice);
            }
            $post->polls()->createMany($data);
        }

        return response()->json([
            "status" => "success",
            "message" => "Post created successfully.",
            "data" => Post::find($post->id)->load('user')->load('polls')
        ], StatusCodes::CREATED);
    }

}

