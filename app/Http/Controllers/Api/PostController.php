<?php

namespace App\Http\Controllers\API;

use App\User;
use App\Models\Post;
use Illuminate\Http\Request;
use App\Collections\StatusCodes;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\Like;
use Illuminate\Support\Facades\Auth;

class PostController extends Controller
{
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
        $post = Post::where('user_id', $id)->with(array('Comment'))->with('user')->latest()->get();

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
        $id = Auth()->user()->id;
        $post = new Post;

        $post->description = $request->input('description');
        $post->images = $request->input('images');
        $post->videos = $request->input('videos');
        $post->user_id = $id;
        $post->poll = $request->input('poll');

        $post->save();

        return response()->json([
            "status" => "success",
            "message" => "Post created successfully.",
            "data" => Post::find($post->id)->load('user')
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
        //
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

        $post->description = $request->input('description');
        $post->images = $request->input('images');
        $post->videos = $request->input('videos');
        $post->poll = $request->input('poll');
        $post->likesCount = $request->input('likesCount');
        $post->dislikesCount = $request->input('dislikesCount');

        $post->save();

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

    public function postLike(Request $request)
    {
        $post = Post::find($request->post_id);

        $id = Auth()->user()->id;

        $like = Like::where([
            'post_id' => $request->post_id,
            'liking_userId' => $id
        ])->first();

        $data = [
            'liking_userId' => $id, 
            'user_id' => $post->user_id, 
            'post_id' => $post->id, 
            'liked' => 1,
            'created_at' => now(), 
            'updated_at' => now()];

        if(!$like) {
            $createPost = Like::create($data);

            return response()->json([
                "status" => "success",
                "message" => "Like successfully.",
                "data" => Like::find($createPost->id)
            ], StatusCodes::SUCCESS);
        } else {
            if($like->liked == 1) {
                $like->update(['liked' => 0]);
                return response()->json([
                    "status" => "success",
                    "message" => "Unlike successfully",
                    "data" => $like
                ], StatusCodes::SUCCESS);
            }
            $like->update(['liked' => 1]);
            return response()->json([
                "status" => "success",
                "message" => "Like successfully",
                "data" => $like
            ], StatusCodes::SUCCESS);
        }

    }

    public function likePost(Request $request)
    {

        $post = Post::find($request->post_id);

        $postId = $request->post_id;

        $postUser = User::find($post->user_id);

        $likedUser = Auth()->user()->id;

        if (!$likedUser) {
            return response()->json([
                "status" => "failure",
                "message" => "You did not login."
            ], StatusCodes::UNPROCESSABLE);
        }

        $this->increasingLikes($likedUser, $postUser->id, $postId);

        return response()->json([
            "status" => "success",
            "message" => "Like successfully."
        ], StatusCodes::SUCCESS);
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

        $like = DB::table('likes')->where([['liking_userId', $authUser->id], ['user_id', $postUser->id], ['post_id', $post]])->first();

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
}
