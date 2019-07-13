<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Repositories\UploadRepository;
use App\Repositories\ConfigurationRepository;

class DeleteTempUpload extends Command
{
    protected $config;
    protected $upload;

    /**
     *  This command is used delete temporarily uploads
     */

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'delete-temp-upload';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete temporarily uploaded files older than 24 hours';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(ConfigurationRepository $config, UploadRepository $upload)
    {
        $this->config = $config;
        $this->upload = $upload;
        
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->config->setDefault();

        $query = $this->upload->getTempUploadQuery();

        $temp_uploads = $query->get();

        foreach ($temp_uploads as $temp_upload) {
            \Storage::delete($temp_upload->filename);
        }

        $query->delete();

        $this->info('Temporary files deleted.');
    }
}
