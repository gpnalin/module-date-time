<?php
declare(strict_types=1);

namespace Aligent\DateTime\Api;

interface DiffCalculatorInterface
{
    /**
     * @param string $startDate
     * @param string $endDate
     * @param string $calculationType
     * @return int
     */
    public function calculate(
        string $startDate,
        string $endDate,
        string $calculationType
    ): int;
}
