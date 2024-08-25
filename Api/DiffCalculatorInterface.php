<?php
declare(strict_types=1);

namespace Aligent\DateTime\Api;

use Aligent\DateTime\Api\Data\ResultInterface;
use Magento\Framework\Validation\ValidationException;
use Exception;

interface DiffCalculatorInterface
{
    /**
     * Calculate the difference between two dates
     * @param string $startDate
     * @param string $endDate
     * @param string $calculationType
     * @return ResultInterface
     * @throws ValidationException|Exception
     */
    public function calculate(
        string $startDate,
        string $endDate,
        string $calculationType
    ): ResultInterface;
}
