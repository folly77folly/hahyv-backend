<?php

namespace App\Http\Controllers\API;

use App\User;
use App\Models\Post;
use Illuminate\Http\Request;
use App\Collections\StatusCodes;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
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
        //
    }

    public function usersPost($id)
    {
        $user = User::find($id);

        $post = Post::where('user_id', $user->id)->latest()->get();

        return response()->json([
            "status" => "success",
            "message" => "Preferences created successfully.",
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
        // $validatedData = $request->validated();
        $id = Auth()->user()->id;


        $post = new Post;

        $post->description = $request->input('description');
        $post->images = serialize($request->input('images'));
        $post->videos = serialize($request->input('videos')); 
        $post->user_id = $id;
        $post->poll = $request->input('poll');

        $post->save();

        return response()->json([
            "status" => "success",
            "message" => "Post created successfully.",
            "data" => array(
                "description" => $request->input('description'), 
                "images" => unserialize($post->images), 
                "videos" => unserialize($post->videos), 
                "poll" => $request->input('poll'),
                "created" => $post->created_at,
                "user" => User::find($post->user_id)
            )
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

        $user_id = Auth()->user()->id;


        if (!$post) {
            return response()->json([
                "status" => "failure",
                "message" => "Post not found."
            ], StatusCodes::UNPROCESSABLE);
        }

        $post->description = $request->input('description'); 
        $post->images = serialize($request->input('images'));
        $post->videos = serialize($request->input('videos'));
        $post->user_id = $user_id;
        $post->poll = $request->input('poll');
        $post->likesCount = $request->input('likesCount');
        $post->dislikesCount = $request->input('dislikesCount');

        $post->save();

        return response()->json([
            "status" => "success",
            "message" => "Post updated successfully.",
            "data" => array(
                "description" => $request->input('description'), 
                "images" => unserialize($post->images), 
                "videos" => unserialize($post->videos), 
                "poll" => $request->input('poll'),
                "updated" => $post->updated_at,
                "user" => User::find($post->user_id)
            )
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

    public function likePost(Request $request, $id)
    {
        // $user =  Auth()->user()->id;
        $user =  User::find($id);

        if(!$user) {
            return response()->json([
                "status" => "failure",
                "message" => "User not found."
            ], StatusCodes::UNPROCESSABLE);
        }

        $likedUser = $request->likedUser;

        $post = $request->post;

        
        $newUser = User::find($likedUser);
        
        if(!$newUser) {
            return response()->json([
                "status" => "failure",
                "message" => "Please who is liking."
            ], StatusCodes::UNPROCESSABLE);
        }
        
        $this->increasingLikes($user->id, $newUser->id, $post);
        

        return response()->json([
            "status" => "success",
            "message" => "Like successfully."
        ], StatusCodes::SUCCESS);
    }

    public function disLikePost(Request $request, $id)
    {
        // $user =  Auth()->user()->id;
        $user =  User::find($id);


        if(!$user) {
            return response()->json([
                "status" => "failure",
                "message" => "User not found."
            ], StatusCodes::UNPROCESSABLE);
        }

        $dislikedUser = $request->dislikedUser;

        $post = $request->post;


        $newUser = User::find($dislikedUser);

        if(!$newUser) {
            return response()->json([
                "status" => "failure",
                "message" => "Please who is disliking."
            ], StatusCodes::UNPROCESSABLE);
        }

        $this->increasingDisLikes($user->id, $newUser->id, $post);

        return response()->json([
            "status" => "success",
            "message" => "Dislike successfully."
        ], StatusCodes::SUCCESS);
    }


    public function increasingLikes($id_auth_user, $id_other_user, $post)
    {
        $authUser = User::find($id_auth_user);
 
        $newUser = User::find($id_other_user);

        $like = DB::table('likes')->where([['user_id', $authUser->id], ['liking_userId', $newUser->id]])->first();
        

        if (!$like) {
            DB::transaction(function ()  use ($newUser, $authUser, $post) {
                DB::table('likes')->insert(['user_id' => $authUser->id, 'liking_userId' => $newUser->id, 'created_at' => now(), 'updated_at' => now()]);
                DB::table('dislikes')->where([['user_id', $authUser->id], ['disliking_userId', $newUser->id]])->delete();
                $initialCount = DB::table('posts')->where('id', $post)->first();
                DB::table('posts')->where('id', $post)->update(['likesCount' => $initialCount->likesCount + 1, 'dislikesCount' => $initialCount->dislikesCount - 1]);
            });
        }
        
    }


    public function increasingDisLikes($id_auth_user, $id_other_user, $post)
    {
        $authUser = User::find($id_auth_user);

        $newUser = User::find($id_other_user);

        $dislike = DB::table('dislikes')->where([['user_id', $authUser->id], ['disliking_userId', $newUser->id]])->first();

        if (!$dislike) {
            DB::transaction(function ()  use ($newUser, $authUser, $post) {
                DB::table('dislikes')->insert(['user_id'=> $authUser->id, 'disliking_userId' => $newUser->id, 'created_at' => now(), 'updated_at' => now()]);
                DB::table('likes')->where([['user_id', $authUser->id], ['liking_userId', $newUser->id]])->delete();
                $initialCount = DB::table('posts')->where('id', $post)->first();
                DB::table('posts')->where('id', $post)->update(['likesCount' => $initialCount->likesCount - 1, 'dislikesCount' => $initialCount->dislikesCount + 1]);
            });
        }
    }
}
