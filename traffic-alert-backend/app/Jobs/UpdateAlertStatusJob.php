<?php

namespace App\Jobs;

use App\Models\ThietLapCanhBao;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class UpdateAlertStatusJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $alertId;

    /**
     * Create a new job instance.
     */
    public function __construct($alertId)
    {
        $this->alertId = $alertId;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $alert = ThietLapCanhBao::find($this->alertId);

        if ($alert && $alert->trang_thai === 'active') {
            $alert->trang_thai = 'expired';
            $alert->kich_hoat = false;
            $alert->save();

            Log::info("🔔 Alert #{$this->alertId} has expired and status updated to 'expired'.");
        }
    }
}
