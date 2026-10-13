<?php

namespace App\Actions\Payout;

use App\Enums\PayoutStatus;
use App\Events\PayoutRequested;
use App\Models\Payout;
use App\Models\User;
use App\Services\InstructorEarningsService;
use Illuminate\Validation\ValidationException;

class RequestPayout
{
    public function __construct(private readonly InstructorEarningsService $earnings) {}

    /**
     * Ajukan penarikan dana instruktur.
     *
     * @param  array{bank_name: string, bank_account_name: string, bank_account_number: string, note?: string|null}  $bank
     *
     * @throws ValidationException
     */
    public function handle(User $instructor, int $amount, array $bank): Payout
    {
        $available = $this->earnings->availableBalance($instructor);

        if ($amount <= 0 || $amount > $available) {
            throw ValidationException::withMessages([
                'amount' => 'Nominal penarikan melebihi saldo tersedia (Rp'.number_format($available, 0, ',', '.').').',
            ]);
        }

        $payout = Payout::create([
            'instructor_id' => $instructor->getKey(),
            'amount' => $amount,
            'status' => PayoutStatus::Requested,
            'bank_name' => $bank['bank_name'],
            'bank_account_name' => $bank['bank_account_name'],
            'bank_account_number' => $bank['bank_account_number'],
            'note' => $bank['note'] ?? null,
            'requested_at' => now(),
        ]);

        PayoutRequested::dispatch($payout);

        return $payout;
    }
}
