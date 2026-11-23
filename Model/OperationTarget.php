<?php declare(strict_types=1);

namespace SamJUK\VerboseDBStatus\Model;

use SamJUK\VerboseDBStatus\Model\Verbosity;
use SamJUK\VerboseDBStatus\Model\OperationChangeFactory;
use Magento\Framework\Setup\Declaration\Schema\Dto\ElementInterface;
use Magento\Framework\Setup\Declaration\Schema\Dto\TableElementInterface;
use Magento\Framework\Setup\Declaration\Schema\Dto\ElementDiffAwareInterface;

// phpcs:disable Generic.PHP.NoSilencedErrors.Discouraged
class OperationTarget
{
    public const INDENT = 4;

    /** @var array<string, mixed> */
    private array $oldData = [];
    /** @var array<string, mixed> */
    private array $newData = [];

    private Verbosity $verbosity;
    private OperationChangeFactory $operationChangeFactory;

    public function __construct(
        Verbosity $verbosity,
        OperationChangeFactory $operationChangeFactory
    ) {
        $this->verbosity = $verbosity;
        $this->operationChangeFactory = $operationChangeFactory;
    }

    public function setOldData(?ElementInterface $element): OperationTarget
    {
        $this->oldData = $this->extractData($element);
        return $this;
    }

    public function setNewData(?ElementInterface $element): OperationTarget
    {
        $this->newData = $this->extractData($element);
        return $this;
    }

    public function getTitle(): string
    {
        return sprintf(
            '%s (%s)',
            $this->newData['name'],
            trim(implode(' ', [
                $this->newData['type'],
                $this->newData['element_type'],
                $this->newData['table']
            ]))
        );
    }

    /**
     * @return OperationChange[]
     */
    public function getChanges(): array
    {
        $return = [];
        if ($this->oldData && $this->newData) {
            foreach ($this->newData as $k => $v) {
                if (!array_key_exists($k, $this->oldData) || $this->oldData[$k] !== $v) {
                    $return[] = $this->operationChangeFactory->create()
                        ->setKey($k)
                        ->setOldValue(@$this->oldData[$k])
                        ->setNewValue(@$this->newData[$k]);
                }
            }
        }
        
        if ($this->verbosity->isExtraVerbose()) {
            [$oldData, $newData] = $this->shakeMutualData($this->oldData, $this->newData);
            $return[] = $this->operationChangeFactory->create()
                ->setKey('data')
                ->setOldValue(json_encode($oldData))
                ->setNewValue(json_encode($newData));
        }
            
        return $return;
    }

    // @TODO: Revist this, currently its late and it works...
    /**
     * @param array<string, mixed> $oldData
     * @param array<string, mixed> $newData
     * @return array{array<string, mixed>, array<string, mixed>}
     */
    private function shakeMutualData(array $oldData, array $newData): array
    {
        $combinedKeys = array_unique([
            ...array_keys($oldData),
            ...array_keys($newData)
        ]);

        $diffKeys = array_filter(
            $combinedKeys,
            function ($k) use ($oldData, $newData) {
                return !(array_key_exists($k, $oldData)
                    && array_key_exists($k, $newData)
                    && $oldData[$k] === $newData[$k]);
            }
        );

        $filterCallback = static function ($k) use ($diffKeys) {
            return in_array($k, $diffKeys, true);
        };

        return [
            array_filter($oldData, $filterCallback, ARRAY_FILTER_USE_KEY),
            array_filter($newData, $filterCallback, ARRAY_FILTER_USE_KEY),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function extractData(?ElementInterface $element): array
    {
        if ($element == null) {
            return [];
        }
        $params = $element instanceof ElementDiffAwareInterface ? $element->getDiffSensitiveParams() : [];
        $params['name'] = $element->getName();
        $params['type'] = $element->getType();
        $params['element_type'] = $element->getElementType();
        $params['table'] = $element instanceof TableElementInterface ? $element->getTable()->getName() : '';
        return $params;
    }

    public function __toString()
    {
        return str_repeat(' ', self::INDENT)
            . $this->getTitle()
            . array_reduce($this->getChanges(), function ($carry, $change) {
                return $carry . PHP_EOL . $change;
            }, '');
    }
}
