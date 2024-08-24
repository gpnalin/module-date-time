<?php
declare(strict_types=1);

namespace Aligent\DateTime\Model;

use Aligent\DateTime\Api\DiffCalculatorInterface;
use DateInterval;
use DateTime;
use DateTimeZone;
use Exception;
use Magento\Framework\Validation\ValidationException;

class DiffCalculator implements DiffCalculatorInterface
{

    /**
     * @param string $startDate
     * @param string $endDate
     * @param string $calculationType
     * @return array|string[]
     * @throws ValidationException
     */
    public function calculate(
        string $startDate,
        string $endDate,
        string $calculationType
    ): array {

        $this->validateInputs($startDate, $endDate);

        try {

            $start = new DateTime($startDate);
            $end = new DateTime($endDate);

            /** @var DateInterval $interval */
            $interval = $start->diff($end);

            $result = $interval->days;

            return ['result' => $result];
        } catch (Exception $e) {
            return ['error' => 'An unexpected error occurred: ' . $e->getMessage()];
        }
    }

    /**
     * @param string $startDate
     * @param string $endDate
     * @return void
     * @throws ValidationException
     */
    protected function validateInputs(
        string $startDate,
        string $endDate
    ): void {
        if (!$this->isValidDateTime($startDate)) {
            throw new ValidationException(__('Invalid start date format. Use a valid datetime format (e.g., "2023-01-01T00:00:00+00:00").'));
        }

        if (!$this->isValidDateTime($endDate)) {
            throw new ValidationException(__('Invalid end date format. Use a valid datetime format (e.g., "2023-01-01T00:00:00+00:00").'));
        }
    }

    protected function isValidDateTime(string $dateTIme): bool
    {
        return strtotime($dateTIme) > 0;
    }
}
