<?php
class Custom
{
    public static function calculateNextDate($interval, $startDate)
    {
        switch ($interval) {
            case 'daily':
                return date('Y-m-d', strtotime("$startDate +1 day"));
            case 'weekly':
                return date('Y-m-d', strtotime("$startDate +1 week"));
            case 'monthly':
                return date('Y-m-d', strtotime("$startDate +1 month"));
            default:
                return $startDate;
        }
    }

    // You can define more custom functions here as needed
}