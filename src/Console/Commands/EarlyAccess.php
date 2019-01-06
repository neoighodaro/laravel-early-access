<?php

namespace Neo\EarlyAccess\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\InteractsWithTime;
use Neo\EarlyAccess\Traits\InteractsWithEarlyAccess;
use Illuminate\Contracts\Filesystem\Filesystem as Storage;

class EarlyAccess extends Command
{
    use InteractsWithTime, InteractsWithEarlyAccess;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'early-access
                            {--a|activate : Activate early access}
                            {--d|deactivate : Deactivate early access}
                            {--allow=* : IP or networks allowed to access the application while in early access mode}
                            {status? : Get the status}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Activate, deactivate, or check if early access mode is enabled.';

    /**
     * @var \Illuminate\Contracts\Filesystem\Filesystem
     */
    private $storage;

    /**
     * Create a new command instance.
     *
     * @param \Illuminate\Contracts\Filesystem\Filesystem $storage
     */
    public function __construct(Storage $storage)
    {
        parent::__construct();

        $this->storage = $storage;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        if ($this->argument('status')) {
            return $this->getStatus();
        }

        $activate = $this->option('activate');

        $deactivate = $this->option('deactivate');

        if (! $activate && ! $deactivate) {
            $intent = $this->option('allow')
                ? 'activate'
                : $this->choice('What do you want to do', ['1' => 'activate', '2' => 'deactivate']);

            ${$intent} = true;
        }

        return $activate ? $this->activate() : $this->deactivate();
    }

    /**
     * Deactivate early access.
     */
    protected function activate()
    {
        $this->saveBeacon($this->option('allow') ?? [])
            ? $this->comment('Early access activated.')
            : $this->error('Unable to activate early access.');
    }

    /**
     * Activate early access.
     */
    protected function deactivate()
    {
        $this->deleteBeacon()
            ? $this->comment('Early access deactivated.')
            : $this->comment('Could not deactivate. Possibly not active.');

        if (config('early-access.enabled')) {
            $this->comment('Set EARLY_ACCESS_ENABLED to false in your .env to fully deactivate it.');
        }
    }

    /**
     * Gets the status of the early access mode.
     */
    protected function getStatus()
    {
        $allowedNetworks = with(($data = $this->getBeaconDetails()), function ($details) {
            return (isset($details['allowed']) && count($details['allowed']))
                ? implode(', ', $details['allowed'])
                : 'none';
        });

        $this->info($data ? "Active. Allowed networks: {$allowedNetworks}" : 'Not active');
    }
}
