<?php
namespace App\Repositories;

use App\Announcement;
use Illuminate\Support\Str;
use App\Repositories\UserRepository;
use App\Repositories\UploadRepository;
use App\Repositories\LocationRepository;
use App\Repositories\DesignationRepository;
use Illuminate\Validation\ValidationException;

class AnnouncementRepository
{
    protected $announcement;
    protected $user;
    protected $upload;
    protected $location;
    protected $designation;
    protected $module = 'announcement';

    /**
     * Instantiate a new instance.
     *
     * @return void
     */
    public function __construct(
        Announcement $announcement,
        UserRepository $user,
        UploadRepository $upload,
        LocationRepository $location,
        DesignationRepository $designation
    ) {
        $this->announcement = $announcement;
        $this->user = $user;
        $this->upload = $upload;
        $this->location = $location;
        $this->designation = $designation;
    }

    /**
     * Get announcement query
     *
     * @return Announcement query
     */
    public function getQuery()
    {
        return $this->announcement;
    }

    /**
     * Count announcement
     *
     * @return integer
     */
    public function count()
    {
        return $this->announcement->count();
    }

    /**
     * List all announcement by title & id
     *
     * @return array
     */
    public function listAll()
    {
        return $this->announcement->all()->pluck('title', 'id')->all();
    }

    /**
     * List all announcement by title & id for select option
     *
     * @return array
     */
    public function selectAll()
    {
        return $this->announcement->all(['title', 'id']);
    }

    /**
     * Get all announcements
     *
     * @return array
     */
    public function getAll()
    {
        return $this->announcement->all();
    }

    public function getUserAnnouncement()
    {
        $announcements = \App\Announcement::with('designation', 'location', 'user', 'user.profile', 'user.profile.designation', 'user.profile.designation.department', 'userAdded', 'userAdded.profile', 'userAdded.profile.designation', 'userAdded.profile.designation.department')->where('start_date', '<=', date('Y-m-d'))->where('end_date', '>=', date('Y-m-d'))->where(function ($q) {
            $q->whereIn('user_id', $this->user->getAccessibleUserId())
                ->orWhere('is_public', 1)
                ->orWhere(function ($q1) {
                    $q1->where('restricted_to', 'designation')->whereHas('designation', function ($q11) {
                        $q11->where('designation_id', \Auth::user()->Profile->designation_id);
                    });
                })->orWhere(function ($q2) {
                    $q2->where('restricted_to', 'location')->whereHas('location', function ($q21) {
                        $q21->where('location_id', \Auth::user()->Profile->location_id);
                    });
                })->orWhere(function ($q3) {
                    $q3->where('restricted_to', 'user')->whereHas('user', function ($q31) {
                        $q31->where('user_id', \Auth::user()->id);
                    });
                });
        })->get();

        return $announcements;
    }

    /**
     * Find announcement with given id or throw an error.
     *
     * @param integer $id
     * @return Announcement
     */
    public function findOrFail($id)
    {
        $announcement = $this->announcement->with('userAdded', 'userAdded.profile', 'userAdded.profile.designation', 'userAdded.profile.designation.department', 'user', 'user.profile', 'user.profile.designation', 'user.profile.designation.department', 'designation', 'designation.department', 'location')->find($id);

        if (! $announcement) {
            throw ValidationException::withMessages(['message' => trans('announcement.could_not_find')]);
        }

        return $announcement;
    }

    /**
     * Paginate all designations using given params.
     *
     * @param array $params
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function paginate($params)
    {
        $sort_by     = isset($params['sort_by']) ? $params['sort_by'] : 'created_at';
        $order       = isset($params['order']) ? $params['order'] : 'desc';
        $page_length = isset($params['page_length']) ? $params['page_length'] : config('config.page_length');

        $accessible_users = $this->user->getAccessibleUserId();

        return $this->announcement->with('designation', 'location', 'user', 'user.profile', 'userAdded', 'userAdded.profile')->whereIn('user_id', $accessible_users)->orderBy($sort_by, $order)->paginate($page_length);
    }

    /**
     * Check an announcement is accessible or not.
     *
     * @param Announcement $announcement
     * @return void
     */
    public function accessible(Announcement $announcement)
    {
        /**
         * Announcement will be accessible if 
         *
         * owner of announcement is accessible user of logged in user
         * announcement is public
         * announcement is restricted for designation and logged in location's designation is listed in designation list of announcement
         * announcement is restricted for location and logged in user's location is listed in designation list of announcement
         * announcement is restricted for user and logged in user's id is listed in user list of announcement
         */

        if (in_array($announcement->user_id, $this->user->getAccessibleUserId()) || $announcement->is_public || ($announcement->restricted_to == 'designation' && in_array(\Auth::user()->Profile->designation_id, $announcement->designation()->pluck('designation_id')->all())) || ($announcement->restricted_to == 'location' && in_array(\Auth::user()->Profile->location_id, $announcement->location()->pluck('location_id')->all())) || ($announcement->restricted_to == 'user' && in_array(\Auth::user()->id, $announcement->user()->pluck('user_id')->all()))) {
        } else {
            throw ValidationException::withMessages(['message' => trans('general.permission_denied')]);
        }
    }

    /**
     * Create a new announcement.
     *
     * @param array $params
     * @return Announcement
     */
    public function create($params)
    {
        $this->validateInputId($params);

        $announcement = $this->announcement->forceCreate($this->formatParams($params));

        $this->processUpload($announcement, $params);

        $this->sync($announcement, $params);

        return $announcement;
    }

    /**
     * Validate input ids.
     *
     * @param array $params
     * @return null
     */

    public function validateInputId($params)
    {
        $designation_ids = $this->designation->listId();
        $location_ids    = $this->location->listId();
        $user_ids       = $this->user->listId();

        $designation_id = isset($params['designation_id']) ? $params['designation_id'] : null;
        $location_id    = isset($params['location_id']) ? $params['location_id'] : null;
        $user_id        = isset($params['user_id']) ? $params['user_id'] : [];

        if ($designation_id && (! is_array($designation_id) || count(array_diff($designation_id, $designation_ids)))) {
            throw ValidationException::withMessages(['message' => trans('designation.could_not_find')]);
        }

        if ($location_id && (! is_array($location_id) || count(array_diff($location_id, $location_ids)))) {
            throw ValidationException::withMessages(['message' => trans('location.could_not_find')]);
        }

        if ($user_id && (! is_array($user_id) || count(array_diff($user_id, $user_ids)))) {
            throw ValidationException::withMessages(['message' => trans('user.could_not_find')]);
        }
    }

    /**
     * Syncing all the relationships.
     *
     * @param Announcement $announcement
     * @param array $array
     * @return void
     */
    private function sync(Announcement $announcement, $params = array())
    {
        $this->syncDesignation($announcement, $params);

        $this->syncLocation($announcement, $params);

        $this->syncUser($announcement, $params);
    }

    /**
     * Syncing designation relationship.
     *
     * @param Announcement $announcement
     * @param array $array
     * @return void
     */
    private function syncDesignation(Announcement $announcement, $params = array())
    {
        $designation_id = isset($params['designation_id']) ? $params['designation_id'] : [];

        $announcement->designation()->sync((! $announcement->is_public && $announcement->restricted_to === 'designation') ? $designation_id : []);
    }

    /**
     * Syncing location relationship.
     *
     * @param Announcement $announcement
     * @param array $array
     * @return void
     */
    private function syncLocation(Announcement $announcement, $params = array())
    {
        $location_id = isset($params['location_id']) ? $params['location_id'] : [];

        $announcement->location()->sync((! $announcement->is_public && $announcement->restricted_to === 'location') ? $location_id : []);
    }

    /**
     * Syncing user relationship.
     *
     * @param Announcement $announcement
     * @param array $array
     * @return void
     */
    private function syncUser(Announcement $announcement, $params = array())
    {
        $user_id = isset($params['user_id']) ? $params['user_id'] : [];

        $announcement->user()->sync((! $announcement->is_public && $announcement->restricted_to === 'user') ? $user_id : []);
    }

    /**
     * Fix announcement attachments
     *
     * @param Announcement $announcement
     * @param array $params
     * @param string $action
     * @return void
     */
    public function processUpload(Announcement $announcement, $params = array(), $action = 'create')
    {
        $upload_token = isset($params['upload_token']) ? $params['upload_token'] : null;

        if ($action === 'create') {
            $this->upload->store($this->module, $announcement->id, $upload_token);
        } else {
            $this->upload->update($this->module, $announcement->id, $upload_token);
        }
    }

    /**
     * Prepare given params for inserting into database.
     *
     * @param array $params
     * @param string $type
     * @return array
     */
    private function formatParams($params, $action = 'create')
    {
        $formatted = [
            'title'       => isset($params['title']) ? $params['title'] : null,
            'description' => isset($params['description']) ? $params['description'] : null,
            'start_date'  => isset($params['start_date']) ? $params['start_date'] : null,
            'end_date'    => isset($params['end_date']) ? $params['end_date'] : null,
            'is_public' => (isset($params['is_public']) && $params['is_public']) ? 1 : 0
        ];

        $formatted['restricted_to'] = (! $formatted['is_public']) ? (isset($params['restricted_to']) ? $params['restricted_to'] : null) : null;

        if ($action === 'create') {
            $formatted['user_id'] = \Auth::user()->id;
            $formatted['uuid'] = Str::uuid();
            $formatted['upload_token'] = isset($params['upload_token']) ? $params['upload_token'] : null;
        }

        return $formatted;
    }

    /**
     * Update given announcement.
     *
     * @param Announcement $announcement
     * @param array $params
     *
     * @return Announcement
     */
    public function update(Announcement $announcement, $params)
    {
        $this->validateInputId($params);
        
        $announcement->forceFill($this->formatParams($params, 'update'))->save();

        $this->processUpload($announcement, $params, 'update');

        $this->sync($announcement, $params);

        return $announcement;
    }

    /**
     * Check the authenticated user is author of the announcement or not.
     *
     * @param Announcement $announcement
     * @return void
     */
    public function isNotAuthor(Announcement $announcement)
    {
        if ($announcement->user_id != \Auth::user()->id) {
            throw ValidationException::withMessages(['message' => trans('general.permission_denied')]);
        }
    }

    /**
     * Delete announcement.
     *
     * @param integer $id
     * @return bool|null
     */
    public function delete(Announcement $announcement)
    {
        return $announcement->delete();
    }

    /**
     * Delete multiple announcements.
     *
     * @param array $ids
     * @return bool|null
     */
    public function deleteMultiple($ids)
    {
        return $this->announcement->whereIn('id', $ids)->delete();
    }

    /**
     * Toggle given announcement status.
     *
     * @param Announcement $announcement
     * @param array $params
     *
     * @return Announcement
     */
    public function toggle(Announcement $announcement)
    {
        $announcement->forceFill([
            'completed_at' => (! $announcement->status) ? Carbon::now() : null,
            'status'       => ! $announcement->status
        ])->save();

        return $announcement;
    }
}
