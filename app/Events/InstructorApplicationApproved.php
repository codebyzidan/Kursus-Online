<?php

namespace App\Events;

use App\Models\InstructorApplication;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class InstructorApplicationApproved
{
    use Dispatchable, SerializesModels;

    public function __construct(public InstructorApplication $application) {}
}
