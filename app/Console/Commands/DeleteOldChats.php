<?php

namespace App\Console\Commands;

use App\Models\Chat;
use Carbon\Carbon;
use Illuminate\Console\Command;

class DeleteOldChats extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'chats:delete-old';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete chats older than 24 hours';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $cutoffTime = Carbon::now()->subHours(24);

        // Delete chats older than 24 hours
        $deletedChats = Chat::where('created_at', '<', $cutoffTime)->delete();

        $this->info("Deleted $deletedChats old chats.");

        return 0;
    }
}
