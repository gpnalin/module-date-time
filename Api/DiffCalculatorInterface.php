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
     * @return array
     */
    public function calculate(
        string $startDate,
        string $endDate,
        string $calculationType
    ): array;
}
