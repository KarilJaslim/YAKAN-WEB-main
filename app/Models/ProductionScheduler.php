<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Artisan extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'specializations',
        'skill_level',
        'experience_years',
        'location',
        'availability_status',
        'max_orders_per_month',
        'current_orders_count',
        'average_production_days',
        'rating',
        'is_active',
        'notes'
    ];

    protected $casts = [
        'specializations' => 'array',
        'is_active' => 'boolean',
        'rating' => 'decimal:2'
    ];

    // Relationships
    public function orders()
    {
        return $this->hasMany(CustomOrder::class, 'assigned_artisan_id');
    }

    public function schedule()
    {
        return $this->hasMany(ArtisanSchedule::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeAvailable($query)
    {
        return $query->where('availability_status', 'available')
                    ->whereRaw('current_orders_count < max_orders_per_month');
    }

    public function scopeBySpecialization($query, $specialization)
    {
        return $query->whereJsonContains('specializations', $specialization);
    }

    // Methods
    public function canAcceptOrder($complexity = 'medium')
    {
        if (!$this->is_active || $this->availability_status !== 'available') {
            return false;
        }

        if ($this->current_orders_count >= $this->max_orders_per_month) {
            return false;
        }

        // Check if artisan has the required skill level for complexity
        $skillRequirements = [
            'low' => ['beginner', 'intermediate', 'expert'],
            'medium' => ['intermediate', 'expert'],
            'high' => ['expert']
        ];

        return in_array($this->skill_level, $skillRequirements[$complexity] ?? ['intermediate']);
    }

    public function getEstimatedCompletionDate($complexity = 'medium', $currentQueue = 0)
    {
        $baseDays = [
            'low' => 5,
            'medium' => 10,
            'high' => 15
        ];

        $productionDays = $baseDays[$complexity] ?? 10;
        $queueDays = $currentQueue * $this->average_production_days;
        
        return Carbon::now()->addDays($productionDays + $queueDays);
    }

    public function getCurrentWorkload()
    {
        return [
            'current_orders' => $this->current_orders_count,
            'max_orders' => $this->max_orders_per_month,
            'utilization_rate' => ($this->current_orders_count / $this->max_orders_per_month) * 100,
            'available_slots' => $this->max_orders_per_month - $this->current_orders_count
        ];
    }

    public function updateWorkload()
    {
        $this->current_orders_count = $this->orders()
            ->whereIn('status', ['approved', 'in_progress'])
            ->whereMonth('created_at', Carbon::now()->month)
            ->count();
        $this->save();
    }
}

class ArtisanSchedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'artisan_id',
        'order_id',
        'scheduled_start_date',
        'estimated_completion_date',
        'actual_completion_date',
        'status',
        'notes',
        'priority'
    ];

    protected $casts = [
        'scheduled_start_date' => 'datetime',
        'estimated_completion_date' => 'datetime',
        'actual_completion_date' => 'datetime'
    ];

    // Relationships
    public function artisan()
    {
        return $this->belongsTo(Artisan::class);
    }

    public function order()
    {
        return $this->belongsTo(CustomOrder::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->whereIn('status', ['scheduled', 'in_progress']);
    }

    public function scopeOverdue($query)
    {
        return $query->where('estimated_completion_date', '<', Carbon::now())
                    ->whereIn('status', ['scheduled', 'in_progress']);
    }

    public function scopeByPriority($query, $priority = 'normal')
    {
        return $query->where('priority', $priority);
    }
}

class ProductionScheduler
{
    /**
     * Find the best available artisan for a custom order
     */
    public static function findBestArtisan($order)
    {
        $complexity = $order->complexity ?? 'medium';
        $patterns = $order->patterns ?? [];
        
        // Get available artisans with required skills
        $artisans = Artisan::active()
            ->available()
            ->where(function ($query) use ($complexity) {
                $skillRequirements = [
                    'low' => ['beginner', 'intermediate', 'expert'],
                    'medium' => ['intermediate', 'expert'],
                    'high' => ['expert']
                ];
                
                $query->whereIn('skill_level', $skillRequirements[$complexity] ?? ['intermediate']);
            })
            ->get();

        // Filter by specialization if patterns are specified
        if (!empty($patterns)) {
            $artisans = $artisans->filter(function ($artisan) use ($patterns) {
                return !empty(array_intersect($artisan->specializations, $patterns));
            });
        }

        // Score artisans based on multiple factors
        $scoredArtisans = $artisans->map(function ($artisan) use ($order) {
            return [
                'artisan' => $artisan,
                'score' => self::calculateArtisanScore($artisan, $order)
            ];
        });

        // Sort by score (highest first) and return the best
        return $scoredArtisans->sortByDesc('score')->first()['artisan'] ?? null;
    }

    /**
     * Calculate artisan score for order assignment
     */
    private static function calculateArtisanScore($artisan, $order)
    {
        $score = 0;

        // Base rating score (40% weight)
        $score += $artisan->rating * 40;

        // Availability score (30% weight)
        $workload = $artisan->getCurrentWorkload();
        $availabilityScore = (100 - $workload['utilization_rate']) * 0.3;
        $score += $availabilityScore;

        // Experience score (20% weight)
        $experienceScore = min($artisan->experience_years * 2, 20);
        $score += $experienceScore;

        // Specialization match (10% weight)
        $patterns = $order->patterns ?? [];
        if (!empty($patterns) && !empty(array_intersect($artisan->specializations, $patterns))) {
            $score += 10;
        }

        return $score;
    }

    /**
     * Schedule an order for production
     */
    public static function scheduleOrder($order, $artisan = null)
    {
        // Find best artisan if not provided
        if (!$artisan) {
            $artisan = self::findBestArtisan($order);
            
            if (!$artisan) {
                throw new \Exception('No available artisan found for this order');
            }
        }

        // Calculate completion date
        $currentQueue = $artisan->schedule()->active()->count();
        $completionDate = $artisan->getEstimatedCompletionDate(
            $order->complexity ?? 'medium',
            $currentQueue
        );

        // Create schedule entry
        $schedule = ArtisanSchedule::create([
            'artisan_id' => $artisan->id,
            'order_id' => $order->id,
            'scheduled_start_date' => Carbon::now(),
            'estimated_completion_date' => $completionDate,
            'status' => 'scheduled',
            'priority' => self::determinePriority($order)
        ]);

        // Update order
        $order->update([
            'assigned_artisan_id' => $artisan->id,
            'status' => 'in_progress',
            'estimated_completion_date' => $completionDate
        ]);

        // Update artisan workload
        $artisan->updateWorkload();

        // Send notifications
        self::sendSchedulingNotifications($order, $artisan, $schedule);

        return $schedule;
    }

    /**
     * Determine order priority
     */
    private static function determinePriority($order)
    {
        // High priority for urgent orders or high-value orders
        if ($order->is_urgent ?? false) {
            return 'high';
        }

        if (($order->final_price ?? $order->estimated_price ?? 0) > 10000) {
            return 'high';
        }

        // Medium priority for complex orders
        if ($order->complexity === 'high') {
            return 'medium';
        }

        return 'normal';
    }

    /**
     * Get production analytics
     */
    public static function getProductionAnalytics($startDate = null, $endDate = null)
    {
        $startDate = $startDate ?: Carbon::now()->startOfMonth();
        $endDate = $endDate ?: Carbon::now()->endOfMonth();

        return [
            'total_orders' => CustomOrder::whereBetween('created_at', [$startDate, $endDate])->count(),
            'completed_orders' => CustomOrder::whereBetween('created_at', [$startDate, $endDate])
                ->where('status', 'completed')->count(),
            'average_production_time' => self::calculateAverageProductionTime($startDate, $endDate),
            'artisan_utilization' => self::getArtisanUtilization(),
            'bottlenecks' => self::identifyBottlenecks(),
            'efficiency_metrics' => self::calculateEfficiencyMetrics($startDate, $endDate)
        ];
    }

    /**
     * Calculate average production time
     */
    private static function calculateAverageProductionTime($startDate, $endDate)
    {
        $completedOrders = CustomOrder::whereBetween('created_at', [$startDate, $endDate])
            ->where('status', 'completed')
            ->whereNotNull('estimated_completion_date')
            ->whereNotNull('actual_completion_date')
            ->get();

        if ($completedOrders->isEmpty()) {
            return 0;
        }

        $totalDays = $completedOrders->sum(function ($order) {
            return $order->actual_completion_date->diffInDays($order->created_at);
        });

        return $totalDays / $completedOrders->count();
    }

    /**
     * Get artisan utilization rates
     */
    private static function getArtisanUtilization()
    {
        return Artisan::active()->get()->map(function ($artisan) {
            $workload = $artisan->getCurrentWorkload();
            
            return [
                'artisan_id' => $artisan->id,
                'name' => $artisan->name,
                'utilization_rate' => $workload['utilization_rate'],
                'current_orders' => $workload['current_orders'],
                'max_orders' => $workload['max_orders'],
                'efficiency_score' => self::calculateArtisanEfficiency($artisan)
            ];
        });
    }

    /**
     * Calculate artisan efficiency
     */
    private static function calculateArtisanEfficiency($artisan)
    {
        $completedOrders = $artisan->orders()
            ->where('status', 'completed')
            ->whereNotNull('estimated_completion_date')
            ->whereNotNull('actual_completion_date')
            ->get();

        if ($completedOrders->isEmpty()) {
            return 100; // Default efficiency for new artisans
        }

        $onTimeDeliveries = $completedOrders->filter(function ($order) {
            return $order->actual_completion_date <= $order->estimated_completion_date;
        })->count();

        return ($onTimeDeliveries / $completedOrders->count()) * 100;
    }

    /**
     * Identify production bottlenecks
     */
    private static function identifyBottlenecks()
    {
        $bottlenecks = [];

        // Check overdue orders
        $overdueOrders = ArtisanSchedule::overdue()->count();
        if ($overdueOrders > 0) {
            $bottlenecks[] = [
                'type' => 'overdue_orders',
                'count' => $overdueOrders,
                'severity' => $overdueOrders > 5 ? 'high' : 'medium'
            ];
        }

        // Check over-utilized artisans
        $overUtilizedArtisans = Artisan::active()->get()->filter(function ($artisan) {
            $workload = $artisan->getCurrentWorkload();
            return $workload['utilization_rate'] > 90;
        })->count();

        if ($overUtilizedArtisans > 0) {
            $bottlenecks[] = [
                'type' => 'over_utilized_artisans',
                'count' => $overUtilizedArtisans,
                'severity' => 'high'
            ];
        }

        // Check pattern-specific bottlenecks
        $patternDemand = self::getPatternDemand();
        foreach ($patternDemand as $pattern => $data) {
            if ($data['demand'] > $data['capacity']) {
                $bottlenecks[] = [
                    'type' => 'pattern_bottleneck',
                    'pattern' => $pattern,
                    'demand' => $data['demand'],
                    'capacity' => $data['capacity'],
                    'severity' => 'medium'
                ];
            }
        }

        return $bottlenecks;
    }

    /**
     * Get pattern demand vs capacity
     */
    private static function getPatternDemand()
    {
        $patterns = ['sussuh', 'banga', 'kabkab', 'custom'];
        $demand = [];

        foreach ($patterns as $pattern) {
            $monthlyDemand = CustomOrder::whereMonth('created_at', Carbon::now()->month)
                ->whereJsonContains('patterns', $pattern)
                ->count();

            $capacity = Artisan::active()
                ->whereJsonContains('specializations', $pattern)
                ->sum('max_orders_per_month');

            $demand[$pattern] = [
                'demand' => $monthlyDemand,
                'capacity' => $capacity,
                'utilization' => $capacity > 0 ? ($monthlyDemand / $capacity) * 100 : 0
            ];
        }

        return $demand;
    }

    /**
     * Calculate efficiency metrics
     */
    private static function calculateEfficiencyMetrics($startDate, $endDate)
    {
        $totalOrders = CustomOrder::whereBetween('created_at', [$startDate, $endDate])->count();
        
        if ($totalOrders === 0) {
            return [
                'on_time_delivery_rate' => 0,
                'quality_score' => 0,
                'productivity_score' => 0
            ];
        }

        $onTimeDeliveries = CustomOrder::whereBetween('created_at', [$startDate, $endDate])
            ->where('status', 'completed')
            ->where(function ($query) {
                $query->whereNull('actual_completion_date')
                      ->orWhere('actual_completion_date', '<=', \DB::raw('estimated_completion_date'));
            })->count();

        // Calculate quality score based on customer ratings (if available)
        $qualityScore = CustomOrder::whereBetween('created_at', [$startDate, $endDate])
            ->whereNotNull('customer_rating')
            ->avg('customer_rating') ?? 80; // Default score if no ratings

        // Calculate productivity (orders per artisan per month)
        $activeArtisans = Artisan::active()->count();
        $productivityScore = $activeArtisans > 0 ? ($totalOrders / $activeArtisans) * 10 : 0;

        return [
            'on_time_delivery_rate' => ($onTimeDeliveries / $totalOrders) * 100,
            'quality_score' => $qualityScore,
            'productivity_score' => min($productivityScore, 100)
        ];
    }

    /**
     * Send scheduling notifications
     */
    private static function sendSchedulingNotifications($order, $artisan, $schedule)
    {
        // Notify artisan
        if ($artisan->email) {
            \Mail::to($artisan->email)->send(
                new \App\Mail\ArtisanOrderAssignment($order, $artisan, $schedule)
            );
        }

        // Notify customer
        if ($order->user && $order->user->email) {
            \Mail::to($order->user->email)->send(
                new \App\Mail\OrderProductionStarted($order, $artisan, $schedule)
            );
        }

        // Create in-app notifications
        $order->user?->notifications()->create([
            'type' => 'production_started',
            'title' => 'Production Started',
            'message' => "Your custom order #{$order->id} has been assigned to {$artisan->name} and production has begun.",
            'data' => [
                'order_id' => $order->id,
                'artisan_name' => $artisan->name,
                'estimated_completion' => $schedule->estimated_completion_date
            ]
        ]);
    }

    /**
     * Reschedule order if needed
     */
    public static function rescheduleOrder($order, $reason = '')
    {
        $artisan = $order->assignedArtisan;
        
        if (!$artisan) {
            throw new \Exception('No artisan assigned to this order');
        }

        // Calculate new completion date
        $currentQueue = $artisan->schedule()->active()->count();
        $newCompletionDate = $artisan->getEstimatedCompletionDate(
            $order->complexity ?? 'medium',
            $currentQueue
        );

        // Update schedule
        $schedule = $order->schedule;
        if ($schedule) {
            $schedule->update([
                'estimated_completion_date' => $newCompletionDate,
                'notes' => ($schedule->notes ?? '') . "\nRescheduled: " . $reason
            ]);
        }

        // Update order
        $order->update([
            'estimated_completion_date' => $newCompletionDate
        ]);

        // Send notifications
        if ($order->user && $order->user->email) {
            \Mail::to($order->user->email)->send(
                new \App\Mail\OrderRescheduled($order, $newCompletionDate, $reason)
            );
        }

        return $newCompletionDate;
    }
}
