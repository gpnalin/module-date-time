<?php
declare(strict_types=1);

namespace Aligent\DateTime\Api;

use Magento\Framework\Validation\ValidationException;
use Exception;

interface DiffCalculatorInterface
{
    /**
     * Calculate the difference between two dates
     * @param string $startDate
     * @param string $endDate
     * @param string $calculationType
     * @return int
     * @throws ValidationException|Exception
     */
    public function calculate(
        string $startDate,
        string $endDate,
        string $calculationType
    ): int;
}
