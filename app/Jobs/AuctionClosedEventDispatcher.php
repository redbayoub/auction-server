<?php

namespace App\Jobs;

use App\Events\AuctionClosedEvent;
use App\Models\DelayedJob;
use App\Models\Item;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class AuctionClosedEventDispatcher implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $deleteWhenMissingModels = true;

    public $delayedJobId;
    public $itemId;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(DelayedJob $delayedJob, Item $item)
    {
        $this->delayedJobId = $delayedJob->id;
        $this->itemId = $item->id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $delayedJob = DelayedJob::findOrFail($this->delayedJobId);

        AuctionClosedEvent::dispatch(Item::findOrFail($this->itemId));

        $delayedJob->delete();
    }
}
