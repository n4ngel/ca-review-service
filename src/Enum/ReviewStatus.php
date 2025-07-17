<?php

declare(strict_types=1);

namespace App\Enum;

enum ReviewStatus: string
{
    case DRAFT = 'draft';
    case PUBLISHED = 'published';
    case SPAM = 'spam';
    case REMOVED = 'removed';
}
