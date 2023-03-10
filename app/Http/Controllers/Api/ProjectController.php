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
        //
        return Project::all();
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

        if($post){
            $project['content'] = $post->content;
        }

        return $project;
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
        $project = Project::find($id);

        if($data['is_main']){
            $this->resetMainProject();
            $data['is_main'] = (bool)$data['is_main'];
        }

        $project->update(collect($data)
            ->only((new Project())->getFillable())->all());

        $post = $project->post;

        if($post){
            $post->update([
                'content'=>$data['content'],
            ]);
        }else{
            $postData = [
                'content'=>$data['content'],
                'postable_type'=>get_class($project),
                'postable_id'=>intval($id)
            ];
            $post = new Post();
            $post->create($postData);
        }


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


}
