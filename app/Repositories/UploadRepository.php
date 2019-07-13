<?php
namespace App\Repositories;

use App\Upload;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class UploadRepository
{
    protected $upload;

    /**
     * Instantiate a new instance.
     *
     * @return void
     */
    public function __construct(Upload $upload)
    {
        $this->upload = $upload;
    }

    /**
     * Get Attachment(s) for given module.
     *
     * @param string $module
     * @param integer $module_id
     * @param string $attachment_uuid
     * @return Upload
     */

    public function getAttachment($module, $module_id, $attachment_uuid = null)
    {
        $attachments = $this->upload->filterByModule($module)->filterByModuleId($module_id)->filterByStatus(1);

        if ($attachment_uuid) {
            $attachment = $attachments->filterByUuid($attachment_uuid)->first();

            if (! $attachment) {
                throw ValidationException::withMessages(['message' => trans('general.invalid_link')]);
            }

            return $attachment;
        }

        return $attachments->get();
    }

    /**
     * Store upload to given module.
     *
     * @param string $module
     * @param integer $module_id
     * @param string $upload_token
     * @return null
     */

    public function store($module, $module_id, $upload_token)
    {
        $this->upload->filterByModule($module)->filterByUploadToken($upload_token)->update(['status' => 1,'module_id' => $module_id]);
    }

    /**
     * Update upload to given module.
     *
     * @param string $module
     * @param integer $module_id
     * @param string $upload_token
     * @return null
     */

    public function update($module, $module_id, $upload_token)
    {
        $old_uploads = $this->upload->filterByModule($module)->filterByModuleId($module_id)->filterByIsTempDelete(1)->get();

        foreach ($old_uploads as $old_upload) {
            \Storage::delete($old_upload->filename);
        }

        $this->upload->filterByModule($module)->filterByModuleId($module_id)->filterByIsTempDelete(1)->delete();

        $this->upload->filterByModule($module)->filterByUploadToken($upload_token)->update(['status' => 1,'module_id' => $module_id]);
    }

    /**
     * Delete upload of given module.
     *
     * @param string $module
     * @param integer $module_id
     * @return null
     */

    public function delete($module, $module_id)
    {
        $uploads = $this->upload->filterByModule($module)->filterByModuleId($module_id)->get();

        foreach ($uploads as $upload) {
            \Storage::delete($upload->filename);
        }

        $this->upload->filterByModule($module)->filterByModuleId($module_id)->delete();
    }

    /**
     * Bulk delete upload of given module.
     *
     * @param string $module
     * @param array $ids
     * @return null
     */

    public function bulkDelete($module, $ids = array())
    {
        $uploads = $this->upload->filterByModule($module)->whereIn('module_id', $ids)->get();
        foreach ($uploads as $upload) {
            \Storage::delete($upload->filename);
        }

        $this->upload->filterByModule($module)->whereIn('module_id', $ids)->delete();
    }

    /**
     * Get temporarily upload query.
     *
     * @return Upload query
     */
    public function getTempUploadQuery()
    {
        return $this->upload->where('updated_at', '<', Carbon::now()->subDays(1)->toDateTimeString())->where(function ($q) {
            $q->where('is_temp_delete', '=', 1)->orWhere('status', '=', 0);
        });
    }

    /**
     * Copy upload module.
     *
     * @param string $module
     * @param integer $module_id
     * @param string $new_upload_token
     * @param integer $new_module_id
     * @param integer $user_id
     * @return void
     */
    public function copy($module, $module_id, $new_upload_token, $new_module_id, $user_id = null)
    {
        foreach ($this->upload->filterByModule($module)->filterByModuleId($module_id)->get() as $upload) {
            $new_upload = $upload->replicate();
            $new_upload->uuid = Str::uuid();
            $new_upload->user_id = $user_id ? : (\Auth::check() ? \Auth::user()->id : null);
            $new_upload->module_id = $new_module_id;
            $new_upload->upload_token = $new_upload_token;

            $file = explode('.', basename($upload->filename));
            $filename = 'uploads/'.$module.'/'.$file[0].'_'.rand(100000, 999999).'.'.$file[1];
            \Storage::copy($upload->filename, $filename);
            $new_upload->filename = $filename;
            $new_upload->save();
        }
    }
}
