<?php

namespace App\Services;

use App\Models\Order;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class InvoiceService
{
    /**
     * Render PDF invoice untuk sebuah pesanan dan kembalikan lokasinya.
     */
    public function renderPdf(Order $order): string
    {
        $order->loadMissing(['items', 'user', 'coupon']);

        $pdf = Pdf::loadView('invoices.pdf', [
            'order' => $order,
        ]);

        $path = trim((string) config('kursus.invoice.path'), '/')
            .'/'.$order->code.'.pdf';

        Storage::disk((string) config('kursus.invoice.disk'))->put($path, $pdf->output());

        return $path;
    }

    public function contents(Order $order): string
    {
        $order->loadMissing(['items', 'user', 'coupon']);

        return Pdf::loadView('invoices.pdf', ['order' => $order])->output();
    }
}
