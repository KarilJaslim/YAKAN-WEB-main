<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\CustomOrder;
use App\Models\User;

class CustomOrderAnalyticsController extends Controller
{
    /**
     * Display analytics dashboard
     */
    public function index(Request $request)
    {
        $period = $request->get('period', 'month'); // week, month, quarter, year
        $startDate = $this->getStartDate($period);
        $endDate = Carbon::now();
        
        return view('admin.custom_orders.analytics', [
            'period' => $period,
            'analytics' => $this->getComprehensiveAnalytics($startDate, $endDate),
            'trends' => $this->getTrendsData($period),
            'insights' => $this->generateInsights($startDate, $endDate),
            'reports' => $this->getAvailableReports()
        ]);
    }

    /**
     * Get comprehensive analytics data
     */
    private function getComprehensiveAnalytics($startDate, $endDate)
    {
        return [
            'overview' => $this->getOverviewMetrics($startDate, $endDate),
            'revenue' => $this->getRevenueAnalytics($startDate, $endDate),
            'patterns' => $this->getPatternAnalytics($startDate, $endDate),
            'customers' => $this->getCustomerAnalytics($startDate, $endDate),
            'production' => $this->getProductionAnalytics($startDate, $endDate),
            'geographic' => $this->getGeographicAnalytics($startDate, $endDate),
            'seasonal' => $this->getSeasonalAnalytics($startDate, $endDate),
            'performance' => $this->getPerformanceMetrics($startDate, $endDate)
        ];
    }

    /**
     * Overview metrics
     */
    private function getOverviewMetrics($startDate, $endDate)
    {
        $orders = CustomOrder::whereBetween('created_at', [$startDate, $endDate]);
        
        return [
            'total_orders' => $orders->count(),
            'total_revenue' => $orders->sum('final_price'),
            'average_order_value' => $orders->avg('final_price'),
            'conversion_rate' => $this->calculateConversionRate($startDate, $endDate),
            'growth_rate' => $this->calculateGrowthRate($startDate, $endDate),
            'customer_retention' => $this->calculateCustomerRetention($startDate, $endDate)
        ];
    }

    /**
     * Revenue analytics
     */
    private function getRevenueAnalytics($startDate, $endDate)
    {
        $orders = CustomOrder::whereBetween('created_at', [$startDate, $endDate]);
        
        return [
            'revenue_by_month' => $orders->selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month, SUM(final_price) as revenue')
                ->groupBy('month')
                ->orderBy('month')
                ->get(),
            'revenue_by_complexity' => $orders->selectRaw('complexity, COUNT(*) as count, SUM(final_price) as revenue')
                ->groupBy('complexity')
                ->get(),
            'revenue_by_pattern' => $this->getRevenueByPattern($orders),
            'top_revenue_customers' => $orders->with('user')
                ->selectRaw('user_id, SUM(final_price) as total_revenue, COUNT(*) as order_count')
                ->groupBy('user_id')
                ->orderBy('total_revenue', 'desc')
                ->limit(10)
                ->get(),
            'payment_method_breakdown' => $orders->selectRaw('payment_method, COUNT(*) as count, SUM(final_price) as revenue')
                ->groupBy('payment_method')
                ->get()
        ];
    }

    /**
     * Pattern analytics
     */
    private function getPatternAnalytics($startDate, $endDate)
    {
        $orders = CustomOrder::whereBetween('created_at', [$startDate, $endDate]);
        
        return [
            'most_popular_patterns' => $this->getMostPopularPatterns($orders),
            'pattern_combinations' => $this->getPatternCombinations($orders),
            'pattern_trends' => $this->getPatternTrends($startDate, $endDate),
            'pattern_profitability' => $this->getPatternProfitability($orders),
            'seasonal_patterns' => $this->getSeasonalPatterns($startDate, $endDate)
        ];
    }

    /**
     * Customer analytics
     */
    private function getCustomerAnalytics($startDate, $endDate)
    {
        $orders = CustomOrder::whereBetween('created_at', [$startDate, $endDate]);
        
        return [
            'new_vs_returning_customers' => $this->getNewVsReturningCustomers($orders),
            'customer_demographics' => $this->getCustomerDemographics($orders),
            'customer_lifetime_value' => $this->calculateCustomerLifetimeValue(),
            'customer_segments' => $this->segmentCustomers($orders),
            'churn_analysis' => $this->analyzeCustomerChurn($startDate, $endDate)
        ];
    }

    /**
     * Production analytics
     */
    private function getProductionAnalytics($startDate, $endDate)
    {
        return [
            'production_efficiency' => $this->calculateProductionEfficiency($startDate, $endDate),
            'artisan_performance' => $this->getArtisanPerformance($startDate, $endDate),
            'production_bottlenecks' => $this->identifyProductionBottlenecks($startDate, $endDate),
            'quality_metrics' => $this->getQualityMetrics($startDate, $endDate),
            'capacity_utilization' => $this->calculateCapacityUtilization($startDate, $endDate)
        ];
    }

    /**
     * Geographic analytics
     */
    private function getGeographicAnalytics($startDate, $endDate)
    {
        $orders = CustomOrder::whereBetween('created_at', [$startDate, $endDate])
            ->with('user');
        
        return [
            'orders_by_region' => $this->getOrdersByRegion($orders),
            'regional_preferences' => $this->getRegionalPreferences($orders),
            'shipping_efficiency' => $this->getShippingEfficiency($orders),
            'market_penetration' => $this->calculateMarketPenetration($orders)
        ];
    }

    /**
     * Seasonal analytics
     */
    private function getSeasonalAnalytics($startDate, $endDate)
    {
        return [
            'seasonal_demand' => $this->getSeasonalDemand($startDate, $endDate),
            'holiday_impact' => $this->analyzeHolidayImpact($startDate, $endDate),
            'weather_correlation' => $this->analyzeWeatherCorrelation($startDate, $endDate),
            'seasonal_patterns' => $this->getSeasonalPatternPreferences($startDate, $endDate)
        ];
    }

    /**
     * Performance metrics
     */
    private function getPerformanceMetrics($startDate, $endDate)
    {
        return [
            'order_fulfillment_time' => $this->calculateOrderFulfillmentTime($startDate, $endDate),
            'on_time_delivery_rate' => $this->calculateOnTimeDeliveryRate($startDate, $endDate),
            'customer_satisfaction' => $this->getCustomerSatisfactionMetrics($startDate, $endDate),
            'return_rates' => $this->calculateReturnRates($startDate, $endDate),
            'support_tickets' => $this->getSupportTicketMetrics($startDate, $endDate)
        ];
    }

    /**
     * Get trends data for charts
     */
    private function getTrendsData($period)
    {
        $startDate = $this->getStartDate($period);
        $endDate = Carbon::now();
        
        return [
            'order_trends' => $this->getOrderTrends($startDate, $endDate, $period),
            'revenue_trends' => $this->getRevenueTrends($startDate, $endDate, $period),
            'customer_trends' => $this->getCustomerTrends($startDate, $endDate, $period),
            'pattern_trends' => $this->getPatternTrendsData($startDate, $endDate, $period)
        ];
    }

    /**
     * Generate actionable insights
     */
    private function generateInsights($startDate, $endDate)
    {
        $insights = [];
        
        // Revenue insights
        $revenueGrowth = $this->calculateRevenueGrowth($startDate, $endDate);
        if ($revenueGrowth > 20) {
            $insights[] = [
                'type' => 'positive',
                'title' => 'Strong Revenue Growth',
                'description' => "Revenue has grown by {$revenueGrowth}% compared to the previous period.",
                'actionable' => true,
                'recommendation' => 'Consider investing in marketing to sustain this growth.'
            ];
        } elseif ($revenueGrowth < -10) {
            $insights[] = [
                'type' => 'warning',
                'title' => 'Revenue Decline',
                'description' => "Revenue has decreased by {$revenueGrowth}% compared to the previous period.",
                'actionable' => true,
                'recommendation' => 'Review pricing strategy and marketing campaigns.'
            ];
        }
        
        // Pattern insights
        $trendingPatterns = $this->getTrendingPatterns($startDate, $endDate);
        if (!empty($trendingPatterns)) {
            $insights[] = [
                'type' => 'info',
                'title' => 'Trending Patterns',
                'description' => 'The following patterns are gaining popularity: ' . implode(', ', $trendingPatterns),
                'actionable' => true,
                'recommendation' => 'Increase production capacity for these patterns.'
            ];
        }
        
        // Customer insights
        $churnRate = $this->calculateCustomerChurnRate($startDate, $endDate);
        if ($churnRate > 15) {
            $insights[] = [
                'type' => 'warning',
                'title' => 'High Customer Churn',
                'description' => "Customer churn rate is {$churnRate}%, which is above the target.",
                'actionable' => true,
                'recommendation' => 'Implement customer retention strategies.'
            ];
        }
        
        // Production insights
        $efficiency = $this->calculateProductionEfficiency($startDate, $endDate);
        if ($efficiency < 80) {
            $insights[] = [
                'type' => 'warning',
                'title' => 'Production Efficiency',
                'description' => "Production efficiency is at {$efficiency}%, below the target of 85%.",
                'actionable' => true,
                'recommendation' => 'Review artisan workload and production processes.'
            ];
        }
        
        return $insights;
    }

    /**
     * Get available reports
     */
    private function getAvailableReports()
    {
        return [
            'monthly_report' => [
                'name' => 'Monthly Performance Report',
                'description' => 'Comprehensive monthly analysis of all metrics',
                'format' => 'PDF',
                'scheduled' => true
            ],
            'pattern_analysis' => [
                'name' => 'Pattern Analysis Report',
                'description' => 'Detailed analysis of pattern popularity and trends',
                'format' => 'Excel',
                'scheduled' => false
            ],
            'customer_insights' => [
                'name' => 'Customer Insights Report',
                'description' => 'Customer behavior and segmentation analysis',
                'format' => 'PDF',
                'scheduled' => true
            ],
            'production_report' => [
                'name' => 'Production Efficiency Report',
                'description' => 'Artisan performance and production metrics',
                'format' => 'Excel',
                'scheduled' => false
            ],
            'revenue_analysis' => [
                'name' => 'Revenue Analysis Report',
                'description' => 'Revenue breakdown and growth analysis',
                'format' => 'PDF',
                'scheduled' => true
            ]
        ];
    }

    /**
     * Helper methods for specific calculations
     */
    private function getStartDate($period)
    {
        switch ($period) {
            case 'week':
                return Carbon::now()->subWeek();
            case 'month':
                return Carbon::now()->subMonth();
            case 'quarter':
                return Carbon::now()->subQuarter();
            case 'year':
                return Carbon::now()->subYear();
            default:
                return Carbon::now()->subMonth();
        }
    }

    private function calculateConversionRate($startDate, $endDate)
    {
        // Calculate from quote requests to actual orders
        $quotes = DB::table('custom_order_quotes')->whereBetween('created_at', [$startDate, $endDate])->count();
        $orders = CustomOrder::whereBetween('created_at', [$startDate, $endDate])->count();
        
        return $quotes > 0 ? ($orders / $quotes) * 100 : 0;
    }

    private function calculateGrowthRate($startDate, $endDate)
    {
        $currentPeriodRevenue = CustomOrder::whereBetween('created_at', [$startDate, $endDate])->sum('final_price');
        
        $previousStartDate = (new Carbon($startDate))->subDays($startDate->diffInDays($endDate));
        $previousPeriodRevenue = CustomOrder::whereBetween('created_at', [$previousStartDate, $startDate])->sum('final_price');
        
        return $previousPeriodRevenue > 0 ? (($currentPeriodRevenue - $previousPeriodRevenue) / $previousPeriodRevenue) * 100 : 0;
    }

    private function calculateCustomerRetention($startDate, $endDate)
    {
        $currentPeriodCustomers = CustomOrder::whereBetween('created_at', [$startDate, $endDate])
            ->distinct('user_id')
            ->pluck('user_id');
        
        $previousPeriodCustomers = CustomOrder::whereBetween('created_at', [
                (new Carbon($startDate))->subDays($startDate->diffInDays($endDate)),
                $startDate
            ])
            ->distinct('user_id')
            ->pluck('user_id');
        
        $returningCustomers = $currentPeriodCustomers->intersect($previousPeriodCustomers)->count();
        $totalCurrentCustomers = $currentPeriodCustomers->count();
        
        return $totalCurrentCustomers > 0 ? ($returningCustomers / $totalCurrentCustomers) * 100 : 0;
    }

    private function getMostPopularPatterns($orders)
    {
        $patternCounts = [];
        
        foreach ($orders->get() as $order) {
            $patterns = json_decode($order->patterns, true) ?? [];
            foreach ($patterns as $pattern) {
                $patternCounts[$pattern] = ($patternCounts[$pattern] ?? 0) + 1;
            }
        }
        
        arsort($patternCounts);
        
        return array_slice($patternCounts, 0, 10, true);
    }

    private function getPatternCombinations($orders)
    {
        $combinations = [];
        
        foreach ($orders->get() as $order) {
            $patterns = json_decode($order->patterns, true) ?? [];
            if (count($patterns) > 1) {
                sort($patterns);
                $combination = implode(' + ', $patterns);
                $combinations[$combination] = ($combinations[$combination] ?? 0) + 1;
            }
        }
        
        arsort($combinations);
        
        return array_slice($combinations, 0, 10, true);
    }

    private function getRevenueByPattern($orders)
    {
        $revenueByPattern = [];
        
        foreach ($orders->get() as $order) {
            $patterns = json_decode($order->patterns, true) ?? [];
            $revenuePerPattern = count($patterns) > 0 ? $order->final_price / count($patterns) : 0;
            
            foreach ($patterns as $pattern) {
                $revenueByPattern[$pattern] = ($revenueByPattern[$pattern] ?? 0) + $revenuePerPattern;
            }
        }
        
        arsort($revenueByPattern);
        
        return $revenueByPattern;
    }

    /**
     * Export analytics data
     */
    public function export(Request $request)
    {
        $type = $request->get('type', 'overview');
        $format = $request->get('format', 'csv');
        $period = $request->get('period', 'month');
        
        $startDate = $this->getStartDate($period);
        $endDate = Carbon::now();
        
        $data = $this->getExportData($type, $startDate, $endDate);
        
        switch ($format) {
            case 'csv':
                return $this->exportCSV($data, $type);
            case 'excel':
                return $this->exportExcel($data, $type);
            case 'pdf':
                return $this->exportPDF($data, $type);
            default:
                return response()->json($data);
        }
    }

    private function getExportData($type, $startDate, $endDate)
    {
        switch ($type) {
            case 'overview':
                return $this->getOverviewMetrics($startDate, $endDate);
            case 'patterns':
                return $this->getPatternAnalytics($startDate, $endDate);
            case 'customers':
                return $this->getCustomerAnalytics($startDate, $endDate);
            case 'revenue':
                return $this->getRevenueAnalytics($startDate, $endDate);
            case 'production':
                return $this->getProductionAnalytics($startDate, $endDate);
            default:
                return $this->getComprehensiveAnalytics($startDate, $endDate);
        }
    }

    private function exportCSV($data, $type)
    {
        $filename = "custom_orders_{$type}_" . date('Y-m-d') . ".csv";
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\""
        ];
        
        $callback = function() use ($data) {
            $file = fopen('php://output', 'w');
            
            // Header row
            if (is_array($data) && !empty($data)) {
                $firstRow = is_array($data[array_key_first($data)]) ? array_values($data)[0] : $data;
                
                if (is_object($firstRow)) {
                    fputcsv($file, array_keys(get_object_vars($firstRow)));
                } elseif (is_array($firstRow)) {
                    fputcsv($file, array_keys($firstRow));
                }
            }
            
            // Data rows
            foreach ($data as $row) {
                if (is_object($row)) {
                    fputcsv($file, array_values(get_object_vars($row)));
                } elseif (is_array($row)) {
                    fputcsv($file, array_values($row));
                }
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }

    private function exportExcel($data, $type)
    {
        // Implementation would use a library like Laravel Excel
        // For now, return CSV as fallback
        return $this->exportCSV($data, $type);
    }

    private function exportPDF($data, $type)
    {
        // Implementation would use a library like DomPDF
        // For now, return JSON as fallback
        return response()->json($data);
    }

    /**
     * API endpoints for real-time analytics
     */
    public function realTimeMetrics(Request $request)
    {
        $metrics = [
            'active_users' => $this->getActiveUsersCount(),
            'pending_orders' => CustomOrder::where('status', 'pending')->count(),
            'today_revenue' => CustomOrder::whereDate('created_at', today())->sum('final_price'),
            'conversion_rate_today' => $this->getTodayConversionRate(),
            'average_order_value_today' => CustomOrder::whereDate('created_at', today())->avg('final_price')
        ];
        
        return response()->json($metrics);
    }

    private function getActiveUsersCount()
    {
        return User::where('last_activity_at', '>=', now()->subMinutes(30))->count();
    }

    private function getTodayConversionRate()
    {
        $quotesToday = DB::table('custom_order_quotes')->whereDate('created_at', today())->count();
        $ordersToday = CustomOrder::whereDate('created_at', today())->count();
        
        return $quotesToday > 0 ? ($ordersToday / $quotesToday) * 100 : 0;
    }
}
