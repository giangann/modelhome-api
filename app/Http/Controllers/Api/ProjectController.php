<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ProjectController extends Controller
{
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
        $folder_name = 'project/' . $this->generateKey();
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
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $listProject = Project::orderBy('is_main','desc')->with('tags')->get();
        return $listProject;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $data = $request->all();
        $data['thumb'] = $this->uploadImage($data);

        if(isset($data['is_main'])){
            $this->resetMainProject();
            $data['is_main'] = $data['is_main']?true:false;
        }

        $project = Project::query()->create($data);
        $project->save();

        if (isset($data['content'])){
//            store content, postable_id, postable_type to post table
            $postData = [
                'content'=>$data['content'],
                'postable_type'=>get_class($project),
                'postable_id'=>intval($project['id'])
            ];
            $post = new Post();
            $post->create($postData);
        }

        return response()->json([
            'data'=>$project,
            'status'=>200
        ]);
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
        $project = Project::find($id);
        $post = $project->post;

        $projectTag = [];
        foreach ($project->tags as $tag){
            array_push($projectTag,intval($tag['id']));
        }
        $project['tag_id']=$projectTag;

        if($post){
            $project['content'] = $post->content;
        }

        return $project;
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
        $project = Project::find($id);

        if(isset($data['is_main'])){
            $this->resetMainProject();
            $data['is_main'] = (bool)$data['is_main'];
        }

        $data['thumb'] = $this->uploadImage($data);
        $project->update(collect($data)
            ->only((new Project())->getFillable())->all());

        // update post of project
        $post = $project->post;
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
                    'postable_type'=>get_class($project),
                    'postable_id'=>intval($id)
                ];
                $post = new Post();
                $post->create($postData);
            }
        }

        // update tags of project
        // delete all old tags
        $tag = $project->tags()->detach();

        // add new tags to pivot
        $tagArr = json_decode($data['tag_id']);
        $tagIdArr=collect($tagArr)->map(function($tag) use ($tagArr){
            return $tag->id;
        });
        $project->tags()->attach($tagIdArr);

        return $project;
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

    public function resetMainProject(){
        DB::table('projects')->update(['is_main'=>false]);
    }

    public function getBySlug($slug){
        $project = Project::firstWhere('slug',$slug);

        $post = $project->post;

        // didn't get content because content data size is too big (> 1Mb)
        unset($post['content']);

        if($post){
            $project['post_id'] = $post->id;
        }

        return $project;
    }

}
