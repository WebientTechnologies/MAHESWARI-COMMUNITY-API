<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Order;
use Carbon\Carbon;


class UpdateOrderLock extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'orders:update-order-lock';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update the order_lock column for orders that have been placed more than 1 minute ago';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $orders = Order::where('order_locked', 0)
        ->where('created_at', '<=', Carbon::now()->subMinutes(1))
        ->get();
        
        foreach ($orders as $order) {
        $order->order_locked = 1;
        $order->save();
        }

        $this->info(count($orders) . ' orders updated successfully');
        
    }
}
