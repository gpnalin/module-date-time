<?php
declare(strict_types=1);

namespace Aligent\DateTime\Api;

interface DiffCalculatorInterface
{
    /**
     * Calculate the difference between two dates
     * @param string $startDate
     * @param string $endDate
     * @param string $calculationType
     * @return int|false|float
     */
    public function calculate(
        string $startDate,
        string $endDate,
        string $calculationType
    ): int|false|float;
}
