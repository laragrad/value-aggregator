<?php

namespace Laragrad\ValueAggregator;

class AggregatedGroup
{
    /**
     * Aggregator
     *
     * @var \Laragrad\ValueAggregator\Aggregator
     */
    protected $aggregator;

    /**
     * Current grouping level
     *
     * @var integer
     */
    protected $level = 0;

    /**
     * Current grouping level is last
     *
     * @var boolean
     */
    protected $lastLevel = false;

    /**
     * Current group key
     *
     * @var string
     */
    protected $key = null;

    /**
     * Child aggregated groups
     *
     * @var collection
     */
    protected $childGroups;

    /**
     * Values of grouping keys
     *
     * @var array
     */
    protected $groupingValues = [];

    /**
     * Aggregated values
     *
     * @var array
     */
    protected $aggregatedValues = [];

    /**
     * Constructor
     *
     * @param Aggregator $aggregator
     * @param string|null $key
     * @param int|null $level
     */
    public function __construct(Aggregator $aggregator, string $key = null, int $level = null) {
        $this->aggregator = $aggregator;
        $this->key = $key ?? null;
        $this->level = $level ?? 0;
        $this->lastLevel = $this->isLastLevel();
        $this->childGroups = collect();
    }

    /**
     * Puts item into
     *
     * @param mixed $item
     * @return bool
     */
    public function addItem($item)
    {

        // Making grouping key for item
        $itemKey = "{$this->key}/{$this->makeKey($item)}";

        if (!$this->lastLevel) {
            if ($this->childGroups->has($itemKey)) {
                // Get child group if exists
                $childGroup = $this->childGroups->get($itemKey);
            } else {
                // else create new child group
                $childGroup = new self($this->aggregator, $itemKey, $this->level + 1);
                $this->childGroups->put($itemKey, $childGroup);
            }
            // Add item into child group in recursion
            $childGroup->addItem($item);
        }

        $this->fillGroupingValues($item);

        $this->aggregateItem($item);

        return true;
    }

    /**
     * Check for this level is last
     *
     * @return boolean
     */
    protected function isLastLevel()
    {
        return ($this->level == $this->aggregator->groupingCount());
    }

    /**
     * Makes item grouping key
     *
     * @param mixed $item
     * @return string
     */
    protected function makeKey($item) {

        return collect($this->aggregator->getGroupingRules()[$this->level] ?? [])
        ->map(function ($field) use ($item) {
            return $this->getItemValue($item, $field);
        })
        ->toJson();
    }

    /**
     * Aggregate item into state
     *
     * @param mixed $item
     */
    protected function aggregateItem($item)
    {
        foreach ($this->aggregator->getAggregationRules() as $field => $aggregationRule) {

            if (!isset($this->aggregatedValues[$field])) {
                $this->aggregatedValues[$field] = $aggregationRule['initial'];
            }

            $aggregationRule['function']($this->aggregatedValues[$field], $item);

        }
    }

    /**
     * Fills grouping values (once time when fist time called)
     *
     * @param mixed $item
     */
    protected function fillGroupingValues($item)
    {
        // Check for grouping values non filled
        if (!empty($this->groupingValues)) {
            return;
        }

        $group = $this->aggregator->getGroupingRules()->get($this->level-1, []);
        foreach ($group as $groupingField) {
            $this->groupingValues[$groupingField] = $this->getItemValue($item, $groupingField);
        }
    }

    /**
     * Gets item's attribute
     *
     * @param array|object $item
     * @param string $field
     * @return mixed
     */
    protected function getItemValue($item, string $field)
    {
        return is_array($item) ? $item[$field] : $item->{$field};
    }

    /**
     * Convert result to array
     *
     * @return array
     */
    public function toArray()
    {
        $result = array_merge($this->groupingValues, $this->aggregatedValues);

        $children = $this->childGroups->map(function ($item) {
            return $item->toArray();
        });

            if (!$children->isEmpty()) {
                $result['children'] = $children->toArray();
            }
            return $result;
    }
}