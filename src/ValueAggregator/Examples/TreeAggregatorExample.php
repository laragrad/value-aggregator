<?php
namespace Laragrad\ValueAggregator\Examples;

use Laragrad\ValueAggregator\TreeAggregator;

class TreeAggregatorExample
{

    public static function run()
    {
        $data = [
            [
                'id' => 1,
                'period_code' => '01.2020',
                'kind' => 1,
                'vendor_id' => 'vois',
                'product_id' => 1,
                'sum' => 1000.00,
                'payed_sum' => 500.00
            ],
            [
                'id' => 2,
                'period_code' => '01.2020',
                'kind' => 1,
                'vendor_id' => 'vois',
                'product_id' => 2,
                'sum' => 1300.00,
                'payed_sum' => 650.00
            ],
            [
                'id' => 3,
                'period_code' => '01.2020',
                'kind' => 1,
                'vendor_id' => 'rao',
                'product_id' => 1,
                'sum' => 2000.00,
                'payed_sum' => 1000.00
            ],
            [
                'id' => 4,
                'period_code' => '01.2020',
                'kind' => 1,
                'vendor_id' => 'rao',
                'product_id' => 2,
                'sum' => 2600.00,
                'payed_sum' => 1300.00
            ],
            [
                'id' => 5,
                'period_code' => '02.2020',
                'kind' => 1,
                'vendor_id' => 'vois',
                'product_id' => 1,
                'sum' => 1200.00,
                'payed_sum' => 0.00
            ],
            [
                'id' => 6,
                'period_code' => '02.2020',
                'kind' => 1,
                'vendor_id' => 'vois',
                'product_id' => 2,
                'sum' => 1500.00,
                'payed_sum' => 0.00
            ],
            [
                'id' => 7,
                'period_code' => '02.2020',
                'kind' => 1,
                'vendor_id' => 'rao',
                'product_id' => 1,
                'sum' => 2400.00,
                'payed_sum' => 0.00
            ],
            [
                'id' => 8,
                'period_code' => '02.2020',
                'kind' => 1,
                'vendor_id' => 'rao',
                'product_id' => 2,
                'sum' => 3000.00,
                'payed_sum' => 0.00
            ]
        ];

        // Create aggregator
        $agg = new TreeAggregator();

        return $agg
            // Grouping rules
            ->setGroupingRules([
                [
                    'period_code',
                    'kind'
                ],
                [
                    'vendor_id',
                ]
            ])
            // Aggregation rules
            ->setAggregationRules([
                'agg_sum' => [
                    'initial' => 0.00,
                    'function' => function (&$value, &$item) {
                        $value += $item['sum'];
                    }
                ],
                'agg_payed_sum' => [
                    'initial' => 0.00,
                    'function' => function (&$value, &$item) {
                        $value += $item['payed_sum'];
                    }
                ],
                'agg_ids' => [
                    'initial' => [],
                    'function' => function (&$value, &$item) {
                        $value[] = $item['id'];
                    }
                ],
                'agg_items' => [
                    'initial' => [],
                    'function' => function (&$value, &$item) {
                        $value[] = &$item;
                    }
                ],
                'agg_count' => [
                    'initial' => 0,
                    'function' => function (&$value, &$item) {
                        $value ++;
                    }
                ]
            ])
            // Aggregation of data
            ->aggregate($data)
            // Get result
            ->get()->toArray();
    }
}