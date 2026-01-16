<?php

namespace App\Enums;

enum JobStatus: string
{
    case PENDING = 'pending';
    case PROCESSING = 'processing';
    case COMPLETED = 'completed';
    case APPROVED = 'approved';
    case REJECTED = 'rejected';
}
