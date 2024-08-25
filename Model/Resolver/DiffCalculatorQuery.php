<?php
declare(strict_types=1);

namespace Aligent\DateTime\Model\Resolver;

use Aligent\DateTime\Api\DiffCalculatorInterface;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\Framework\Validation\ValidationException;

class DiffCalculatorQuery implements ResolverInterface
{
    /**
     * @param DiffCalculatorInterface $diffCalculator
     */
    public function __construct(private readonly DiffCalculatorInterface $diffCalculator)
    {
    }

    /**
     * @param Field $field
     * @param $context
     * @param ResolveInfo $info
     * @param array|null $value
     * @param array|null $args
     * @return array
     * @throws GraphQlInputException
     */
    public function resolve(
        Field $field,
              $context,
        ResolveInfo $info,
        array $value = null,
        array $args = null
    ): array
    {
        try {
            $startDate = $args['startDate'];
            $endDate = $args['endDate'];
            $calculationType = strtolower($args['calculationType']);
            return ['result' => $this->diffCalculator->calculate($startDate, $endDate, $calculationType)];
        } catch (ValidationException $e) {
            throw new GraphQlInputException(__($e->getMessage()));
        }
    }
}
