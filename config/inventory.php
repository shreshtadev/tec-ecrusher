<?php

return [
    /**
     * Default warehouse ID for stock operations
     */
    'default_warehouse_id' => env('DEFAULT_WAREHOUSE_ID', 1),

    /**
     * Low stock threshold percentage (0-100)
     * Items below this percentage of average stock will be flagged
     */
    'low_stock_threshold_percent' => env('LOW_STOCK_THRESHOLD_PERCENT', 10),

    /**
     * Default stock valuation method: 'FIFO' or 'LIFO'
     */
    'stock_valuation_method' => env('STOCK_VALUATION_METHOD', 'FIFO'),

    /**
     * Enable multi-warehouse support
     */
    'enable_multi_warehouse' => env('ENABLE_MULTI_WAREHOUSE', true),
];
