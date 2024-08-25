<?php
declare(strict_types=1);

namespace Aligent\DateTime\Model;

use Aligent\DateTime\Api\Data\ResultInterface;
use Magento\Framework\DataObject;


class Result extends DataObject implements ResultInterface
{
    public const string RESULT = 'result';

    /**
     * Get result
     *
     * @return int
     */
    public function getResult(): int
    {
        return $this->getData(self::RESULT);
    }

    /**
     * Set result
     *
     * @param int $result
     * @return ResultInterface
     */
    public function setResult(int $result): ResultInterface
    {
        $this->setData(self::RESULT, $result);
        return $this;
    }
}
