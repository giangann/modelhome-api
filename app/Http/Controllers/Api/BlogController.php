<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use App\Models\Post;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BlogController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $blog = Blog::orderBy('created_at')->with('tags')->get();

        return $blog;
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // upload thumb
        $data = $request->all();
        $data['thumb'] = $this->uploadImage($data);

        $blog = Blog::query()->create($data);
        $blog->save();

        if (isset($data['content'])){
        // store content, postable_id, postable_type to post table
            $postData = [
                'content'=>$data['content'],
                'postable_type'=>get_class($blog),
                'postable_id'=>intval($blog['id'])
            ];
            $post = new Post();
            $post->create($postData);
        }

        // add new tags to pivot
        $tagArr = json_decode($data['tag_id']);
        $tagIdArr=collect($tagArr)->map(function($tag) use ($tagArr){
            return $tag->id;
        });
        $blog->tags()->attach($tagIdArr);

        return response()->json([
            'data'=>$blog,
            'status'=>200
        ]);
    }

    public function generateKey()
    {
        $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';

        $pin = mt_rand(1000000, 9999999)
            . mt_rand(1000000, 9999999)
            . $characters[rand(0, strlen($characters) - 1)];

        return str_shuffle($pin);
    }

    public function uploadImage($project)
    {
        $filename = $project['thumb'];
        $folder_name = 'blog/' . $this->generateKey();
        if (request()->hasFile('thumb')) {
            Storage::disk('public')->delete($filename);
            $thumb =$project['thumb'];
            $filename = Storage::disk('public')->put(
                $folder_name,
                $thumb
            );
        }

        return $filename;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
        $blog = Blog::find($id);
        $post = $blog->post;

        $blogTag = [];
        foreach ($blog->tags as $tag){
            array_push($blogTag,intval($tag['id']));
        }
        $blog['tag_id']=$blogTag;

        if($post){
            $blog['content'] = $post->content;
        }

        return $blog;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
        $data = $request->all();
        $blog = Blog::find($id);

        $data['thumb'] = $this->uploadImage($data);
        $blog->update(collect($data)->only((new Blog())->getFillable())->all());

        $post = $blog->post;
        if($post){
            if(isset($data['content'])){
                $post->update([
                    'content'=>$data['content'],
                ]);
            }
            else {
                $post->update([
                    'content'=>null,
                ]);
            }
        }else{
            if(isset($data['content'])){
                $postData = [
                    'content'=>$data['content'],
                    'postable_type'=>get_class($blog),
                    'postable_id'=>intval($id)
                ];
                $post = new Post();
                $post->create($postData);
            }
        }

        // update tags of project
        if(isset($data['tag_id'])){
            $tagArr = json_decode($data['tag_id']);
            $tagIdArr=collect($tagArr)->map(function($tag) use ($tagArr){
                return $tag->id;
            });

            // delete all old tags
            $blog->tags()->detach();

            // add new tags to pivot
            $blog->tags()->attach($tagIdArr);
        } else {
            if($blog->tags){
                // delete all old tags
                $blog->tags()->detach();
            }
        }

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
