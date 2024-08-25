<?php
declare(strict_types=1);

namespace Aligent\DateTime\Api\Data;

interface ResultInterface
{
    /**
     * Get result
     *
     * @return int
     */
    public function getResult(): int;

    /**
     * Set result
     *
     * @param int $result
     * @return void
     */
    public function setResult(int $result): ResultInterface;
}
