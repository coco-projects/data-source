<?php

    require '../vendor/autoload.php';

    function formatBytes($size, $precision = 2)
    {
        $units = [
            'B',
            'KB',
            'MB',
            'GB',
            'TB',
        ];

        $unit = floor(log($size, 1024));
        $size = $size / pow(1024, $unit);

        return round($size, $precision) . ' ' . $units[$unit];
    }
