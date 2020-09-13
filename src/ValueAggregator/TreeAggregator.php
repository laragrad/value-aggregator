<?php

namespace Laragrad\ValueAggregator;

class TreeAggregator
{
    /**
     * Collection of grouping rules
     *
     * @var \Illuminate\Support\Collection
     */
    protected $groupingRules;

    /**
     * Collection of aggregation rules
     *
     * @var \Illuminate\Support\Collection
     */
    protected $aggregationRules;

    /**
     * Root aggregated group
     *
     * @var AggregatedGroup
     */
    protected $rootGroup;

    /**
     * Constructor
     *
     * \Laragrad\ValueAggregator\Aggregator
     */
    public function __construct()
    {
        $this->groupingRules = collect();
        $this->aggregationRules = collect();
    }

    /**
     * Sets grouping rules
     *
     * @param array|\Illuminate\Support\Collection|\Illuminate\Database\Eloquent\Collection $groups
     * @return TreeAggregator
     */
    public function setGroupingRules($groups)
    {
        $this->groupingRules = collect($groups);
        return $this;
    }

    /**
     * Gets grouping levels
     *
     * @return \Illuminate\Support\Collection
     */
    public function getGroupingRules()
    {
        return $this->groupingRules;
    }

    /**
     * Returns count of grouping levels
     *
     * @return integer
     */
    public function groupingCount()
    {
        return $this->groupingRules->count();
    }

    /**
     * Sets aggregation rules
     *
     * @param array|\Illuminate\Support\Collection|\Illuminate\Database\Eloquent\Collection $aggregation
     * @return \Laragrad\ValueAggregator\Aggregator
     */
    public function setAggregationRules($aggregation)
    {
        $this->aggregationRules = collect($aggregation);

        return $this;
    }

    /**
     * Gets aggregation rules
     *
     * @return \Illuminate\Support\Collection
     */
    public function getAggregationRules()
    {
        return $this->aggregationRules;
    }

    /**
     * Aggregates data
     *
     * @return \Laragrad\ValueAggregator\Aggregator
     */
    public function aggregate($data)
    {
        if (is_null($this->rootGroup)) {
            $this->rootGroup = new AggregatedGroup($this);
        }

        foreach ($data as $item) {
            $this->rootGroup->addItem($item);
        }

        return $this;
    }

    /**
     * Gets aggregation result
     *
     * @return \Laragrad\ValueAggregator\AggregatedGroup
     */
    public function get()
    {
        return $this->rootGroup;
    }

    /**
     * Resets aggregator state
     *
     * @return \App\Classes\Aggregator\Aggregator
     */
    public function reset()
    {
        $this->rootGroup = null;
        $this->groupingRules = collect();
        $this->aggregationRules = collect();

        return $this;
    }
}