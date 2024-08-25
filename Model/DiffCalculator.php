<?php
declare(strict_types=1);

namespace Aligent\DateTime\Model;

use Aligent\DateTime\Api\DiffCalculatorInterface;
use DateInterval;
use DateTime;
use Exception;
use Magento\Framework\Intl\DateTimeFactory;
use Magento\Framework\Validation\ValidationException;

class DiffCalculator implements DiffCalculatorInterface
{

    /**
     * Allowed calculation types
     */
    public const array CALCULATION_TYPES = ['days', 'weekdays', 'weeks', 'seconds', 'minutes', 'hours', 'years'];

    /**
     * @param DateTimeFactory $dateTimeFactory
     */
    public function __construct(public DateTimeFactory $dateTimeFactory)
    {
    }

    /**
     * @param string $startDate
     * @param string $endDate
     * @param string $calculationType
     * @return int
     * @throws ValidationException|Exception
     */
    public function calculate(string $startDate, string $endDate, string $calculationType): int
    {
        $this->validateInputs($startDate, $endDate, $calculationType);

        $start = $this->dateTimeFactory->create($startDate);
        $end = $this->dateTimeFactory->create($endDate);

        if ($end < $start) {
            throw new ValidationException(__('End date must be greater than or equal to start date.'));
        }

        /** @var DateInterval $interval */
        $interval = $start->diff($end);

        $result = 0;

        switch ($calculationType) {
            case 'days':
                $result = $interval->days;
                break;
            case 'weekdays':
                $result = $this->calculateWeekdays($start, $end);
                break;
            case 'weeks':
                $result = floor($interval->days / 7);
                break;
            case 'hours':
                $result = $interval->days * 24 + $interval->h;
                break;
            case 'minutes':
                $result = ($interval->days * 24 + $interval->h) * 60 + $interval->i;
                break;
            case 'seconds':
                $result = (($interval->days * 24 + $interval->h) * 60 + $interval->i) * 60 + $interval->s;
                break;
            case 'years':
                $result = $interval->y;
                break;
        }

        return (int)$result;
    }

    /**
     * @param string $startDate
     * @param string $endDate
     * @param string $calculationType
     * @return void
     * @throws ValidationException
     */
    protected function validateInputs(string $startDate, string $endDate, string $calculationType): void
    {
        if (!$this->isValidDateTime($startDate)) {
            throw new ValidationException(__('Invalid start date format. Use a valid datetime format (e.g., "2023-01-01T00:00:00+00:00").'));
        }

        if (!$this->isValidDateTime($endDate)) {
            throw new ValidationException(__('Invalid end date format. Use a valid datetime format (e.g., "2023-01-01T00:00:00+00:00").'));
        }

        if (!in_array($calculationType, self::CALCULATION_TYPES)) {
            throw new ValidationException(__('Invalid calculation type. Allowed types are: %1', implode(', ', self::CALCULATION_TYPES)));
        }
    }

    /**
     * @param string $dateTIme
     * @return bool
     */
    public function isValidDateTime(string $dateTIme): bool
    {
        return strtotime($dateTIme) > 0;
    }

    /**
     * @param DateTime $start
     * @param DateTime $end
     * @return float
     */
    protected function calculateWeekdays(DateTime $start, DateTime $end): float
    {
        $totalDays = $start->diff($end)->days;
        $weekdays = 0;

        // Calculate complete weeks and remaining days
        $completeWeeks = floor($totalDays / 7);
        $remainingDays = $totalDays % 7;

        // Count weekdays in complete weeks
        $weekdays += $completeWeeks * 5;

        // Add weekdays from the remaining days
        for ($i = 0; $i < $remainingDays; $i++) {
            $dayOfWeek = (clone $start)->modify("+{$i} days")->format('N');
            if ($dayOfWeek <= 5) { // Monday(1) to Friday(5)
                $weekdays++;
            }
        }

        return $weekdays;
    }
}
