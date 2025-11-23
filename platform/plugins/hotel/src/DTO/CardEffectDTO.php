<?php

namespace Botble\Hotel\DTO;

use Botble\Hotel\Enums\CustomerCardCoverageType;

class CardEffectDTO
{
    public function __construct(
        public readonly float $discountGross,
        public readonly int $unitsUsed,
        public readonly float $unitValueGross,
        public readonly CustomerCardCoverageType $coverageType
    ) {
    }
}
