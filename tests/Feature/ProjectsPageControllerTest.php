<?php

namespace Tests\Feature;

use KBox\User;
use KBox\Project;
use Tests\TestCase;
use KBox\Capability;
use KBox\Traits\Searchable;

/**
 * Test the Projects page for the Unified Search (routes documents.projects.*)
 */
class ProjectsPageControllerTest extends TestCase
{
    use Searchable;
    
    public function expected_routes_provider()
    {
        return [
            [ 'documents.projects.index', [] ],
            [ 'documents.projects.show', ['project' => 1] ],
        ];
    }
    
    public function routes_and_capabilities_provider()
    {
        return [
            [ Capability::$ADMIN, ['documents.projects.index' => 'documents.projects.projectspage', 'documents.projects.show' => 'documents.projects.detail'], 200 ],
            [ Capability::$PROJECT_MANAGER_LIMITED, ['documents.projects.index' => 'documents.projects.projectspage', 'documents.projects.show' => 'documents.projects.detail'], 200 ],
            [ Capability::$PROJECT_MANAGER, ['documents.projects.index' => 'documents.projects.projectspage', 'documents.projects.show' => 'documents.projects.detail'], 200 ],
            [ [Capability::MANAGE_KBOX], ['documents.projects.index' => 'documents.projects.projectspage', 'documents.projects.show' => 'documents.projects.detail'], 200 ],
            [ Capability::$PARTNER, ['documents.projects.index' => 'documents.projects.projectspage', 'documents.projects.show' => 'documents.projects.detail'], 200 ],
            [ [Capability::RECEIVE_AND_SEE_SHARE], ['documents.projects.index' => 'documents.projects.projectspage'], 403 ],
            [ [Capability::RECEIVE_AND_SEE_SHARE], ['documents.projects.show' => 'documents.projects.detail'], 200 ],
        ];
    }

    private function createUser($capabilities, $userParams = [])
    {
        return tap(User::factory()->create($userParams))->addCapabilities($capabilities);
    }

    /**
     * Test the expected project routes are available
     *
     * @dataProvider expected_routes_provider
     * @return void
     */
    public function test_route_exists($route_name, $parameters)
    {
        route($route_name, $parameters);
        
        // you will see InvalidArgumentException if the route is not defined
        $this->assertTrue(true, "Test complete without exceptions");
    }
    
    /**
     * Test if some routes browsed after login are viewable or not and shows the expected page and error code
     *
     * @dataProvider routes_and_capabilities_provider
     * @return void
     */
    public function test_user_can_access_the_projects_page($caps, $routes, $expected_return_code)
    {
        $this->withKlinkAdapterFake();
        
        $params = null;
        $user = null;
        
        foreach ($routes as $route => $viewname) {
            $user = $this->createUser($caps);
            
            if (strpos($route, 'show') !== false) {
                $project = Project::factory()->create(['user_id' => $user->id]);
                
                $params = ['project' => $project->id];
            } else {
                $params = [];
            }
            
            $generated_url = route($route, $params);

            $response = $this->actingAs($user)->get($generated_url);
            
            if ($expected_return_code === 200) {
                $response->assertStatus(200);
                $response->assertViewIs($viewname);
            } else {
                $response->assertStatus($expected_return_code);
            }
        }
    }

    public function test_search_is_available()
    {
        $this->withKlinkAdapterFake();

        $user = $this->createUser(Capability::$PROJECT_MANAGER_LIMITED);

        $generated_url = route('documents.projects.index');

        $response = $this->actingAs($user)->get($generated_url);

        $response->assertSee('search-form');
    }

    public function test_accessible_projects_are_listed()
    {
        $this->withKlinkAdapterFake();

        $user = $this->createUser(Capability::$PROJECT_MANAGER_LIMITED);

        $managed_project = Project::factory()->create(['user_id' => $user->id]);
        $project = Project::factory()->create();
        $project->users()->attach($user->id);

        $url = route('documents.projects.index');

        $response = $this->actingAs($user)->get($url);

        $response->assertViewHas('projects');

        $projects = $response->data('projects');

        $this->assertNotEmpty($projects, 'empty project list');
        $this->assertCount(2, $projects, 'project count');

        $this->assertContains($managed_project->id, array_values($projects->pluck('id')->toArray()));
        $this->assertContains($project->id, array_values($projects->pluck('id')->toArray()));

        $response->assertViewHas('pagetitle', trans('projects.page_title'));
        $response->assertViewHas('current_visibility', 'private');
        $response->assertViewHas('filter', trans('projects.all_projects'));
        $response->assertSee($managed_project->manager->name);
        $response->assertSee($project->manager->name);
        $response->assertSee($managed_project->getCreatedAt());
        $response->assertSee($project->getCreatedAt());
    }

    public function test_create_project_button_visible_if_user_can_create_projects()
    {
        $this->withKlinkAdapterFake();

        $user = $this->createUser(Capability::$PROJECT_MANAGER);

        $url = route('documents.projects.index');

        $response = $this->actingAs($user)->get($url);

        $response->assertSeeText(trans('projects.new_button'));
    }

    public function test_create_project_button_not_visible_if_user_cannot_create_projects()
    {
        $this->withKlinkAdapterFake();

        $user = $this->createUser(Capability::$PROJECT_MANAGER_LIMITED);

        $url = route('documents.projects.index');

        $response = $this->actingAs($user)->get($url);

        $response->assertDontSeeText(trans('projects.new_button'));
    }
}
