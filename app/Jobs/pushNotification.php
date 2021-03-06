<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Http\Controllers\V1\Api\NotificationController;
use App\Traits\SendNotification;
class pushNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels,SendNotification;
    private $message,$user_id;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($message,$user_id)
    {
        $this->message = $message;
        $this->user_id = $user_id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->push('Waza',$this->message,$this->user_id);
        // $this->sendNotificationForNewCreatedGroup($this->message,$this->user_id);
    }
}
