<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Google\Analytics\Data\V1beta\Client\BetaAnalyticsDataClient;
use Google\Analytics\Data\V1beta\DateRange;
use Google\Analytics\Data\V1beta\Metric;
use Google\Analytics\Data\V1beta\Dimension;
use Google\Analytics\Data\V1beta\RunReportRequest;
use Google\Analytics\Data\V1beta\Filter;
use Google\Analytics\Data\V1beta\FilterExpression;
use Google\Analytics\Data\V1beta\Filter\StringFilter;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    protected $client;
    protected $propertyId;

    public function __construct()
    {
        $this->propertyId = env('GA4_PROPERTY_ID');
        $this->client = new BetaAnalyticsDataClient([
            'credentials' => storage_path('app/ga-credentials.json'),
        ]);
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = $this->dashboardMetrics();
        return view('dashboard.index');
    }

    public function dashboardMetrics()
    {
        // Dates
        $today = now()->toDateString();
        $last30 = now()->subDays(30)->toDateString();

        // -----------------------
        // 1. Core Metrics
        // -----------------------
        $activeUsers = $this->getMetric('activeUsers', $last30, $today);
        $downloads = $this->getMetric('newUsers', '2020-01-01', $today); // lifetime users = proxy for downloads
        $revenue = $this->getMetric('purchaseRevenue', $last30, $today);
        $premiumConversions = $this->getMetric('ecommercePurchases', $last30, $today);

        // -----------------------
        // 2. Engagement
        // -----------------------
        $sessions = $this->getMetric('sessions', $last30, $today);
        $avgSessionDuration = $this->getMetric('averageSessionDuration', $last30, $today);
        $engagementDuration = $this->getMetric('userEngagementDuration', $last30, $today);

        // Feature usage (requires custom events, example: flashcards_opened)
        $featureUsage = $this->getCustomEventCounts(['flashcards_opened', 'purchase_clicked'], $last30, $today);

        // -----------------------
        // 3. Retention
        // -----------------------
        $retention = $this->getRetention($last30, $today);

        // -----------------------
        // 4. Demographics & Devices
        // -----------------------
        $countryBreakdown = $this->getDimensionMetric('country', 'activeUsers', $last30, $today);
        $deviceBreakdown = $this->getDimensionMetric('deviceCategory', 'activeUsers', $last30, $today);

        // -----------------------
        // 5. Marketing
        // -----------------------
        $referrals = $this->getDimensionMetric('sessionSource', 'sessions', $last30, $today);

        return response()->json([
            'core_metrics' => [
                'active_users_last30' => $activeUsers,
                'downloads_lifetime' => $downloads,
                'revenue_last30' => $revenue,
                'premium_conversions' => $premiumConversions,
            ],
            'engagement' => [
                'sessions' => $sessions,
                'avg_session_duration' => $avgSessionDuration,
                'engagement_duration' => $engagementDuration,
                'feature_usage' => $featureUsage,
            ],
            'retention' => $retention,
            'demographics' => [
                'country' => $countryBreakdown,
                'devices' => $deviceBreakdown,
            ],
            'marketing' => [
                'referrals' => $referrals,
            ]
        ]);
    }

    // -----------------------
    // Helpers
    // -----------------------

    private function getMetric($metric, $startDate, $endDate)
    {
        $request = new RunReportRequest([
            'property' => 'properties/' . $this->propertyId,
            'date_ranges' => [
                new DateRange([
                    'start_date' => $startDate,
                    'end_date'   => $endDate,
                ]),
            ],
            'metrics' => [
                new Metric(['name' => $metric]),
            ],
        ]);

        $response = $this->client->runReport($request);

        if ($response->getRows()->count() > 0) { // RepeatedField has ->count()
            return $response->getRows()[0]->getMetricValues()[0]->getValue();
        }

        return 0;
    }


    private function getDimensionMetric($dimension, $metric, $startDate, $endDate)
    {
        $request = new RunReportRequest([
            'property' => 'properties/' . $this->propertyId,
            'date_ranges' => [
                new DateRange([
                    'start_date' => $startDate,
                    'end_date'   => $endDate,
                ]),
            ],
            'dimensions' => [new Dimension(['name' => $dimension])],
            'metrics'    => [new Metric(['name' => $metric])],
        ]);

        $response = $this->client->runReport($request);

        $data = [];
        foreach ($response->getRows() as $row) {
            $data[$row->getDimensionValues()[0]->getValue()] = $row->getMetricValues()[0]->getValue();
        }

        return $data;
    }

    private function getCustomEventCounts($eventNames, $startDate, $endDate)
    {
        $results = [];
        foreach ($eventNames as $event) {
            $filter = new Filter([
                'field_name'   => 'eventName',
                'string_filter' => new StringFilter([
                    'match_type' => StringFilter\MatchType::EXACT,
                    'value'      => $event,
                ]),
            ]);

            $request = new RunReportRequest([
                'property' => 'properties/' . $this->propertyId,
                'date_ranges' => [
                    new DateRange([
                        'start_date' => $startDate,
                        'end_date'   => $endDate,
                    ]),
                ],
                'dimensions' => [new Dimension(['name' => 'eventName'])],
                'metrics'    => [new Metric(['name' => 'eventCount'])],
                'dimension_filter' => new FilterExpression([
                    'filter' => $filter
                ]),
            ]);

            $response = $this->client->runReport($request);

            $results[$event] = $response->getRowCount() > 0
                ? $response->getRows()[0]->getMetricValues()[0]->getValue()
                : 0;
        }
        return $results;
    }

    private function getRetention($startDate, $endDate)
    {
        $request = new RunReportRequest([
            'property' => 'properties/' . $this->propertyId,
            'date_ranges' => [
                new DateRange([
                    'start_date' => $startDate,
                    'end_date'   => $endDate,
                ]),
            ],
            'dimensions' => [new Dimension(['name' => 'date'])],
            'metrics'    => [new Metric(['name' => 'activeUsers'])],
        ]);

        $response = $this->client->runReport($request);

        $data = [];
        foreach ($response->getRows() as $row) {
            $date = $row->getDimensionValues()[0]->getValue();
            $count = $row->getMetricValues()[0]->getValue();
            $data[$date] = $count;
        }
        return $data;
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
