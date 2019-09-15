<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        'App\Todo'         => 'App\Policies\TodoPolicy',
        'App\User'         => 'App\Policies\UserPolicy',
        'App\Department'   => 'App\Policies\DepartmentPolicy',
        'App\Client'        => 'App\Policies\ClientPolicy',
        'App\Contractor'   => 'App\Policies\ContractorPolicy',        
        'App\Designation'  => 'App\Policies\DesignationPolicy',
        'App\Asset'         => 'App\Policies\AssetPolicy',
        'App\Project'         => 'App\Policies\ProjectPolicy',
        'App\Location'     => 'App\Policies\LocationPolicy',
        'App\Task'         => 'App\Policies\TaskPolicy',
        'App\SubTask'      => 'App\Policies\SubTaskPolicy',
        'App\Announcement' => 'App\Policies\AnnouncementPolicy'
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        //
    }
}
