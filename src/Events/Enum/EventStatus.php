<?php

namespace Dogstronauts\AstroBook\Events\Enum;

enum EventStatus: string
{
    case Draft = 'draft';
    case Published = 'published';
}
