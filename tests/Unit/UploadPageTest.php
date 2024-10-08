<?php

namespace Tests\Unit;

use Tests\TestCase;
use DmsRouting;
use KBox\Capability;
use KBox\Group;
use KBox\Project;
use KBox\User;

class UploadPageTest extends TestCase
{
    public function test_upload_page_shows_private_target()
    {
        $user = User::factory()->partner()->create();

        $response = $this->actingAs($user)->get('/uploads');

        $response->assertStatus(200);

        $response->assertViewIs('upload.index');
        $response->assertViewHas('context', 'uploads');
        $response->assertViewHas('target', trans('upload.target.personal', ['link' => route('documents.index')]));
        $response->assertViewHas('target_collection', null);
    }
    
    public function test_upload_page_shows_project_target()
    {
        $user = User::factory()->create();
        $user->addCapabilities(Capability::$ADMIN);

        $project = Project::factory()->create();
        
        $collection_id = $project->collection->id;

        $response = $this->actingAs($user)->get("/uploads?c=$collection_id");

        $response->assertStatus(200);

        $response->assertViewIs('upload.index');
        $response->assertViewHas('context', 'uploads');
        $response->assertViewHas('target', trans('upload.target.project', ['name' => e($project->name), 'link' => DmsRouting::group($collection_id)]));
        $response->assertViewHas('target_collection', $collection_id);
    }
    
    public function test_upload_page_shows_project_collection_target()
    {
        $user = User::factory()->create();
        $user->addCapabilities(Capability::$ADMIN);

        $project = Project::factory()->create();

        $service = app('KBox\Documents\Services\DocumentsService');
        
        $collection = $service->createGroup($project->manager, 'That exact collection', null, $project->collection, false);
        
        $collection_id = $collection->id;
        $project_id = $project->collection->id;

        $response = $this->actingAs($user)->get("/uploads?c=$collection_id");

        $response->assertStatus(200);

        $response->assertViewIs('upload.index');
        $response->assertViewHas('context', 'uploads');
        $response->assertViewHas('target', trans('upload.target.project_collection', [
            'name' => e('That exact collection'),
            'link' => DmsRouting::group($collection_id),
            'project_name' => e($project->name),
            'project_link' => DmsRouting::group($project_id),
        ]));
        $response->assertViewHas('target_collection', $collection_id);
    }
    
    public function test_upload_page_shows_collection_target()
    {
        $user = User::factory()->create();

        $user->addCapabilities(Capability::$ADMIN);

        $collection = $user->groups()->create([
            'name' => 'That exact collection',
            'type' => Group::TYPE_PERSONAL,
            'color' => '16a085',
        ]);
        
        $collection_id = $collection->id;

        $response = $this->actingAs($user)->get("/uploads?c=$collection_id");

        $response->assertStatus(200);

        $response->assertViewIs('upload.index');
        $response->assertViewHas('context', 'uploads');
        $response->assertViewHas('target', trans('upload.target.collection', [
            'name' => e($collection->name),
            'link' => DmsRouting::group($collection_id),
        ]));
        $response->assertViewHas('target_collection', $collection_id);
    }
    
    public function test_upload_page_shows_target_error()
    {
        $user = User::factory()->partner()->create();

        $project = Project::factory()->create();
        
        $collection_id = $project->collection->id;

        $response = $this->actingAs($user)->get("/uploads?c=$collection_id");

        $response->assertStatus(200);

        $response->assertViewIs('upload.index');
        $response->assertViewHas('context', 'uploads');
        $response->assertViewHas('target', trans('upload.target.personal', ['link' => route('documents.index')]));
        $response->assertViewHas('target_collection', null);
        $response->assertViewHas('target_error', trans('upload.target.error'));
    }
}
