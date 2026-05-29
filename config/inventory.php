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
     * Enable multi-warehouse support
     */
    'enable_multi_warehouse' => env('ENABLE_MULTI_WAREHOUSE', true),
];
