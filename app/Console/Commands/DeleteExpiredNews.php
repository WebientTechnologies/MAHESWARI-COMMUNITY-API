<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;
use App\Models\News;

class DeleteExpiredNews extends Command
{
    protected $signature = 'news:delete-expired';

    protected $description = 'Delete expired news';

    public function handle()
    {
        $thirtyDaysAgo = Carbon::now()->subDays(30);

        News::where('created_at', '<=', $thirtyDaysAgo)->delete();

        $this->info('Expired news deleted successfully!');
    }
}

