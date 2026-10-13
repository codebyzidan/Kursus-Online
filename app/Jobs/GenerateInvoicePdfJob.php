<?php

namespace App\Jobs;

use App\Models\Order;
use App\Services\InvoiceService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class GenerateInvoicePdfJob implements ShouldQueue
{
    use Queueable;

    public function __construct(public Order $order) {}

    public function handle(InvoiceService $invoices): void
    {
        $invoices->renderPdf($this->order);
    }
}
