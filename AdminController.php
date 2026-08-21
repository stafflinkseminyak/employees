<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\ContactInquiry;
use App\Models\Job;
use App\Models\PageSection;
use App\Models\Vendor;
use App\Models\Business;
use App\Models\Client;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    protected array $sectionLabels = [
        'hero' => 'Hero Banner',
        'overview' => 'Overview',
        'solutions' => 'Solutions',
        'staffing' => 'Careers to Services',
        'industries' => 'Industries',
        'cta' => 'Call to Action',
    ];

    public function dashboard()
    {
        $sections = PageSection::all();
        $metrics = [
            'page_sections' => $sections->count(),
            'total_jobs' => Job::query()->count(),
            'published_jobs' => Job::query()->where('status', 'published')->count(),
            'upcoming_appointments' => Appointment::query()
                ->where('starts_at', '>=', now())
                ->whereIn('status', ['pending', 'confirmed'])
                ->count(),
            'contact_inquiries' => ContactInquiry::query()->count(),
        ];

        $startOfWeek = Carbon::now()->startOfWeek(Carbon::MONDAY);
        $endOfWeek = Carbon::now()->endOfWeek(Carbon::SUNDAY);
        $weeklyAppointments = Appointment::query()
            ->whereBetween('starts_at', [$startOfWeek, $endOfWeek])
            ->orderBy('starts_at')
            ->get();

        $timezone = 'Asia/Singapore';
        $today = Carbon::today($timezone);
        $startOfWeekLocal = Carbon::now($timezone)->startOfWeek(Carbon::MONDAY);
        $lastWeekStart = Carbon::now($timezone)->subWeek()->startOfWeek(Carbon::MONDAY);
        $lastWeekEnd = Carbon::now($timezone)->subWeek()->endOfWeek(Carbon::SUNDAY);
        $startOfMonth = Carbon::now($timezone)->startOfMonth();
        $startOfYear = Carbon::now($timezone)->startOfYear();

        $getStats = function ($modelClass) use ($today, $startOfWeekLocal, $lastWeekStart, $lastWeekEnd, $startOfMonth, $startOfYear, $timezone) {
            $thisWeekCount = $modelClass::where('created_at', '>=', $startOfWeekLocal)->count();
            $lastWeekCount = $modelClass::whereBetween('created_at', [$lastWeekStart, $lastWeekEnd])->count();
            
            $growthWeek = 0;
            if ($lastWeekCount > 0) {
                $growthWeek = round((($thisWeekCount - $lastWeekCount) / $lastWeekCount) * 100);
            } elseif ($thisWeekCount > 0) {
                $growthWeek = 100;
            }

            $monthsData = [];
            $monthsLabels = [];
            for ($i = 6; $i >= 0; $i--) {
                $monthStart = Carbon::now($timezone)->subMonths($i)->startOfMonth();
                $monthEnd = Carbon::now($timezone)->subMonths($i)->endOfMonth();
                $count = $modelClass::whereBetween('created_at', [$monthStart, $monthEnd])->count();
                $monthsData[] = $count;
                $monthsLabels[] = $monthStart->format('M');
            }

            $totalCount = $modelClass::count();
            $hasStatus = \Illuminate\Support\Facades\Schema::hasColumn((new $modelClass)->getTable(), 'status');
            
            $activeCount = $hasStatus ? $modelClass::where('status', 'active')->count() : $totalCount;
            $pendingCount = $hasStatus ? $modelClass::whereIn('status', ['pending', 'inactive'])->count() : 0;
            $approvalRate = $totalCount > 0 ? round(($activeCount / $totalCount) * 100) : ($totalCount == 0 ? 0 : 100);

            $annualTarget = 100;
            $yearCount = $modelClass::where('created_at', '>=', $startOfYear)->count();
            $annualPercent = min(100, round(($yearCount / $annualTarget) * 100));

            return [
                'today' => $modelClass::where('created_at', '>=', $today)->count(),
                'week' => $thisWeekCount,
                'month' => $modelClass::where('created_at', '>=', $startOfMonth)->count(),
                'year' => $yearCount,
                'total' => $totalCount,
                'growth_week' => $growthWeek,
                'chart_data' => $monthsData,
                'chart_labels' => $monthsLabels,
                'approval_rate' => $approvalRate,
                'pending_count' => $pendingCount,
                'annual_target_progress' => $yearCount,
                'annual_target_percent' => $annualPercent,
            ];
        };

        $operationsStats = [
            'Vendors' => $getStats(Vendor::class),
            'Businesses' => $getStats(Business::class),
            'Clients' => array_merge($getStats(Client::class), (function() use ($startOfMonth, $timezone) {
                // Client Monthly KPI: active clients (placements) this month vs target of 10
                $kpiTarget = 10;
                $placementsThisMonth = \App\Models\Client::where('status', 'active')
                    ->where('created_at', '>=', $startOfMonth)
                    ->count();
                $kpiPercent = $kpiTarget > 0 ? min(100, round(($placementsThisMonth / $kpiTarget) * 100)) : 0;
                $kpiYearlyTarget = $kpiTarget * 12; // 10 * 12 = 120
                $yearlyPlacements = \App\Models\Client::where('status', 'active')
                    ->where('created_at', '>=', $startOfYear ?? Carbon::now($timezone)->startOfYear())
                    ->count();
                $kpiYearlyPercent = $kpiYearlyTarget > 0 ? min(100, round(($yearlyPlacements / $kpiYearlyTarget) * 100)) : 0;

                return [
                    'kpi_target' => $kpiTarget,
                    'kpi_placements' => $placementsThisMonth,
                    'kpi_percent' => $kpiPercent,
                    'kpi_yearly_target' => $kpiYearlyTarget,
                    'kpi_yearly_placements' => $yearlyPlacements,
                    'kpi_yearly_percent' => $kpiYearlyPercent,
                ];
            })()),
        ];

        return view('admin.dashboard', [
            'sections' => $sections,
            'labels' => $this->sectionLabels,
            'metrics' => $metrics,
            'weeklyAppointments' => $weeklyAppointments,
            'operationsStats' => $operationsStats,
        ]);
    }

    public function index()
    {
        $sections = PageSection::query()->latest()->paginate(10);

        return view('admin.sections.index', compact('sections'));
    }

    public function edit(string $section)
    {
        $pageSection = PageSection::where('section', $section)->firstOrFail();

        return view('admin.edit', [
            'pageSection' => $pageSection,
            'label' => $this->sectionLabels[$section] ?? ucfirst($section),
        ]);
    }

    public function update(Request $request, string $section)
    {
        $pageSection = PageSection::where('section', $section)->firstOrFail();

        $content = $request->input('content', []);

        // Clean up empty values in nested arrays
        $content = $this->cleanContent($content);

        $pageSection->update(['content' => $content]);

        return redirect()
            ->route('admin.sections.edit', $section)
            ->with('success', 'Section updated successfully.');
    }

    private function cleanContent(array $data): array
    {
        $cleaned = [];

        foreach ($data as $key => $value) {
            if (is_array($value)) {
                // Re-index numeric arrays (in case items were removed)
                $inner = $this->cleanContent($value);
                if ($this->isNumericArray($value)) {
                    $inner = array_values($inner);
                }
                $cleaned[$key] = $inner;
            } else {
                $cleaned[$key] = $value;
            }
        }

        return $cleaned;
    }

    private function isNumericArray(array $arr): bool
    {
        return array_keys($arr) === range(0, count($arr) - 1)
            || array_keys($arr) === array_map('strval', range(0, count($arr) - 1));
    }

    /**
     * Compute the effective display status for an employee today.
     * - 'on-leave' overrides active/probation if a qualifying absence is active today.
     * - 'terminated', 'joining-soon' are never overridden.
     */
    private function effectiveStatus(\App\Models\Employee $employee): string
    {
        $base = $employee->status ?? 'active';

        // These statuses are never overridden by absences
        if (in_array($base, ['terminated', 'joining-soon'])) {
            return $base;
        }

        // Check for an active non-lateness absence covering today
        $today = now()->startOfDay();
        $onLeave = \App\Models\EmployeeAbsence::where('employee_id', $employee->id)
            ->whereIn('type', ['annual', 'personal', 'other'])
            ->where('start_date', '<=', $today)
            ->where(function ($q) use ($today) {
                $q->whereNull('end_date')
                  ->orWhere('end_date', '>=', $today)
                  ->orWhere('ongoing', true);
            })
            ->exists();

        return $onLeave ? 'on-leave' : $base;
    }

    public function performance(Request $request)
    {
        // Admins/HR can jump straight to a specific employee's detailed
        // Performance breakdown (e.g. the "click here for more details" link
        // from that employee's Profile → KPI & Performance tab) via
        // ?employee={id}. Anyone else — or a missing/invalid override — just
        // sees their own, exactly as before; same admin/super_admin check
        // used everywhere else in this app (see AdminKpiJobController).
        $isAdmin = in_array(auth()->user()?->role, ['super_admin', 'admin'], true);
        $viewedEmployee = ($isAdmin && $request->filled('employee'))
            ? \App\Models\Employee::with('division')->find($request->query('employee'))
            : null;
        $viewingOther = $viewedEmployee !== null;

        // clients.assigned_to stores an Employee ID, NOT the logged-in User's ID.
        // Resolve User → Employee first; $userId is the Employee PK used in every query.
        $employee = $viewedEmployee ?? \App\Models\Employee::with('division')->where('user_id', auth()->id())->first();
        $userId   = $employee?->id;
        $now      = now();

        // ==================== YTD DASHBOARD (super_admin only) ====================
        // A separate, mostly-static company-wide operational scorecard — originally
        // built into the KPI builder page, now moved here per Ayu's boss (who built
        // it and wants it kept, just relocated). It has nothing to do with the
        // per-employee weighted "Goals & KPI" section below (that's $kpiGoalGroups,
        // computed further down) — it's whatever cards are stored on the KpiTemplate
        // resolved for the viewed employee. Resolved once, up here, so it's available
        // to every return path below regardless of division. Visibility is gated in
        // the view; still computed for every viewer so nothing is wasted or leaked
        // conditionally on data resolution (the gate is purely presentational).
        // Deliberately NOT resolved via KpiTemplate::forEmployee($employee) — that
        // would tie it to whichever employee's Performance page happens to be open,
        // so it would appear/disappear/change depending on who a super_admin is
        // currently viewing. This is meant as ONE shared, company-wide dashboard
        // (per Ayu and her boss), so it's resolved independently of $employee:
        // whichever KpiTemplate row anywhere still carries a stored ytd_dashboard
        // block is "the" dashboard, full stop.
        $isSuperAdmin = auth()->user()?->role === 'super_admin';
        $ytdKpiTemplate = \App\Models\KpiTemplate::all()->first(fn ($t) => !empty($t->kpi_data['ytd_dashboard'] ?? null));
        $ytdDashboardCards = $ytdKpiTemplate ? ($ytdKpiTemplate->kpi_data['ytd_dashboard'] ?? []) : [];

        if (!empty($ytdDashboardCards)) {
            // Live company-wide headcount figures for the two cards that have always
            // been computed rather than typed in — same source/logic as the KPI
            // builder page previously used (AdminKpiJobController::getKpiTemplate).
            $thisYear = $now->year;

            $ftActive = DB::table('contracts')
                ->join('users', 'contracts.employee_user_id', '=', 'users.id')
                ->where('contracts.status', 'approved')
                ->where('contracts.employment_basis', 'LIKE', '%full%')
                ->distinct('contracts.employee_user_id')->count('contracts.employee_user_id');
            $ptActive = DB::table('contracts')
                ->join('users', 'contracts.employee_user_id', '=', 'users.id')
                ->where('contracts.status', 'approved')
                ->where('contracts.employment_basis', 'LIKE', '%part%')
                ->distinct('contracts.employee_user_id')->count('contracts.employee_user_id');

            $ftThisYear = DB::table('contracts')->where('status', 'approved')
                ->whereYear('created_at', $thisYear)->where('employment_basis', 'LIKE', '%full%')->count();
            $ptThisYear = DB::table('contracts')->where('status', 'approved')
                ->whereYear('created_at', $thisYear)->where('employment_basis', 'LIKE', '%part%')->count();

            $ftLastYear = DB::table('contracts')->where('status', 'approved')
                ->whereYear('created_at', $thisYear - 1)->where('employment_basis', 'LIKE', '%full%')->count();
            $ptLastYear = DB::table('contracts')->where('status', 'approved')
                ->whereYear('created_at', $thisYear - 1)->where('employment_basis', 'LIKE', '%part%')->count();

            $ftVacancies = DB::table('careers')->where('status', 'published')
                ->where(function ($q) {
                    $q->where('type', 'LIKE', '%full%')->orWhere('type', '')->orWhereNull('type');
                })->count();
            $ptVacancies = DB::table('careers')->where('status', 'published')
                ->where('type', 'LIKE', '%part%')->count();

            $ftTotal = $ftActive + $ftVacancies;
            $ptTotal = $ptActive + $ptVacancies;
            $ftPct = $ftTotal > 0 ? round(($ftActive / $ftTotal) * 100) : 0;
            $ptPct = $ptTotal > 0 ? round(($ptActive / $ptTotal) * 100) : 0;

            foreach ($ytdDashboardCards as &$card) {
                if (($card['title'] ?? null) === 'Full Time Employees') {
                    $card['value'] = (string) $ftActive;
                    $card['target'] = (string) $ftThisYear;
                    $card['last_year'] = (string) $ftLastYear;
                    $card['percent'] = $ftPct;
                    $card['color'] = $ftActive > 0 ? 'teal' : 'gray';
                }
                if (($card['title'] ?? null) === 'Part Time Employees') {
                    $card['value'] = (string) $ptActive;
                    $card['target'] = (string) $ptThisYear;
                    $card['last_year'] = (string) $ptLastYear;
                    $card['percent'] = $ptPct;
                    $card['color'] = $ptActive > 0 ? 'teal' : 'gray';
                }
                // Company-wide (not the currently-viewed employee's own number) —
                // total paid revenue this year across every client, divided by every
                // active employee. Computed here, alongside the dashboard's other two
                // live cards, so it updates the same way regardless of whose
                // Performance page is currently open.
                if (($card['title'] ?? null) === 'Revenue Per Employee') {
                    $companyRevenueYTD = (float) \App\Models\ClientInvoice::where('status', 'paid')
                        ->whereYear(DB::raw('COALESCE(payment_date, updated_at)'), $thisYear)
                        ->sum('total_invoice');
                    $activeEmployeeCount = \App\Models\Employee::where(function ($q) {
                        $q->whereNull('status')->orWhere('status', '!=', 'terminated');
                    })->count();
                    $revenuePerEmployee = $activeEmployeeCount > 0 ? $companyRevenueYTD / $activeEmployeeCount : 0;
                    $card['value'] = 'IDR ' . number_format($revenuePerEmployee, 0, ',', '.');
                    $targetNum = (float) preg_replace('/[^\d.\-]/', '', (string) ($card['target'] ?? '0'));
                    $card['percent'] = $targetNum > 0 ? min(100, (int) round(($revenuePerEmployee / $targetNum) * 100)) : ($card['percent'] ?? 0);
                    $card['color'] = $card['percent'] >= 100 ? 'teal' : ($card['percent'] >= 50 ? 'gold' : 'red');
                }
            }
            unset($card);
        }

        // ==================== DIVISION GATE ====================
        // Add new division names here as their dashboards are built.
        $divisionName       = $employee?->division?->name;
        $supportedDivisions = ['Human Resources & Recruitment', 'Finance & Accounts'];
        $dashboardAvailable = in_array($divisionName, $supportedDivisions);

        if (! $dashboardAvailable) {
            return view('admin.performance.index', [
                'dashboardAvailable' => false,
                'divisionName'       => $divisionName,
                'viewingOther'       => $viewingOther,
                'employee'           => $employee,
                'isSuperAdmin'       => $isSuperAdmin,
                'ytdDashboardCards'  => $ytdDashboardCards,
            ]);
        }

        // ==================== MONTH NAVIGATION ====================
        // Shared by all divisions — drives the month picker in the header.
        try {
            $selectedMonth = \Carbon\Carbon::createFromFormat('Y-m', (string) $request->query('month'))->startOfMonth();
        } catch (\Throwable $e) {
            $selectedMonth = $now->copy()->startOfMonth();
        }
        // Clamp: never allow browsing into the future.
        if ($selectedMonth->greaterThan($now->copy()->startOfMonth())) {
            $selectedMonth = $now->copy()->startOfMonth();
        }

        $isCurrentMonth = $selectedMonth->isSameMonth($now);
        $asOf           = $isCurrentMonth ? $now : $selectedMonth->copy()->endOfMonth();
        $quarterStart   = $selectedMonth->copy()->firstOfQuarter();
        $quarterEnd     = $selectedMonth->copy()->lastOfQuarter();
        $prevMonth      = $selectedMonth->copy()->subMonth()->format('Y-m');
        $nextMonth      = $isCurrentMonth ? null : $selectedMonth->copy()->addMonth()->format('Y-m');

        // ==================== HUMAN RESOURCES & RECRUITMENT ====================
        if ($divisionName === 'Human Resources & Recruitment') {

            $monthlyTarget   = 10;
            $quarterlyTarget = 30;
            $yearlyTarget    = 120;

            // 1. PLACEMENTS
            $placementsThisMonth = \App\Models\Client::where('assigned_to', $userId)
                ->where('status', 'active')
                ->whereMonth('placed_at', $selectedMonth->month)
                ->whereYear('placed_at', $selectedMonth->year)
                ->count();

            $placementsThisQuarter = \App\Models\Client::where('assigned_to', $userId)
                ->where('status', 'active')
                ->whereBetween('placed_at', [$quarterStart->toDateString(), $quarterEnd->toDateString()])
                ->count();

            $placementsThisYear = \App\Models\Client::where('assigned_to', $userId)
                ->where('status', 'active')
                ->whereYear('placed_at', $selectedMonth->year)
                ->count();

            // 2. OPEN VACANCIES
            // "Open as of [selected month]" means: the recruitment Service Need had
            // already been created by then, AND it hadn't been closed yet (closed_at
            // is null, or closed_at falls after the reference point). closed_at is
            // set automatically by AdminClientServiceController the moment a service
            // is marked completed/cancelled, so this reconstructs history accurately
            // instead of guessing from updated_at (which can shift for unrelated edits).
            $openVacancies = \App\Models\ClientService::where('service_type', 'recruitment')
                ->where('created_at', '<=', $asOf)
                ->where(function ($q) use ($asOf) {
                    $q->whereNull('closed_at')->orWhere('closed_at', '>', $asOf);
                })
                ->whereHas('client', function ($q) use ($userId) {
                    $q->where('assigned_to', $userId);
                })
                ->with('client.business')
                ->orderBy('created_at')
                ->get()
                ->map(function ($service) use ($asOf) {
                    $client  = $service->client;
                    $opened  = \Carbon\Carbon::parse($service->created_at);
                    $details = $service->service_details ?? [];
                    return (object) [
                        'id'          => $client->id,
                        'role_title'  => $details['position_needed'] ?? 'Role not specified',
                        'company'     => $client->business->name ?? $client->full_name,
                        'opened_at'   => $opened,
                        'months_open' => (int) $opened->diffInMonths($asOf),
                        'days_open'   => (int) $opened->diffInDays($asOf),
                        'is_overdue'  => (int) $opened->diffInMonths($asOf) >= 3,
                    ];
                });
            $overdueVacancyCount = $openVacancies->where('is_overdue', true)->count();

            // 3. REVENUE (from paid invoices only)
            // Revenue is recorded when a ClientInvoice status is set to 'paid'.
            // We use payment_date for grouping when available, falling back to updated_at
            // so invoices manually marked paid without a payment_date still count.
            $dbPaidDate     = \Illuminate\Support\Facades\DB::raw('COALESCE(payment_date, DATE(updated_at))');
            $dbPaidDateTime = \Illuminate\Support\Facades\DB::raw('COALESCE(payment_date, updated_at)');

            $hrInvoiceBase = fn () => \App\Models\ClientInvoice::whereHas('client', function ($q) use ($userId) {
                $q->where('assigned_to', $userId);
            })->where('status', 'paid');

            $revenueThisMonth = (float) $hrInvoiceBase()
                ->whereMonth($dbPaidDate, $selectedMonth->month)
                ->whereYear($dbPaidDate, $selectedMonth->year)
                ->sum('total_invoice');

            $revenueYTD = (float) $hrInvoiceBase()
                ->whereYear($dbPaidDate, $selectedMonth->year)
                ->whereDate($dbPaidDateTime, '<=', $selectedMonth->copy()->endOfMonth())
                ->sum('total_invoice');

            // (The YTD Dashboard's "Revenue Per Employee" card is now computed as a
            // company-wide figure up in the YTD Dashboard block above, independent of
            // which employee's Performance page this is — see the comment there.)

            $lastMonthRevenue = (float) $hrInvoiceBase()
                ->whereMonth($dbPaidDate, $selectedMonth->copy()->subMonth()->month)
                ->whereYear($dbPaidDate, $selectedMonth->copy()->subMonth()->year)
                ->sum('total_invoice');

            $revenueChangePercent = $lastMonthRevenue > 0
                ? round((($revenueThisMonth - $lastMonthRevenue) / $lastMonthRevenue) * 100)
                : null;

            $monthlyRevenueTrend = [];
            for ($i = 5; $i >= 0; $i--) {
                $m   = $selectedMonth->copy()->subMonths($i);
                $sum = (float) $hrInvoiceBase()
                    ->whereMonth($dbPaidDate, $m->month)
                    ->whereYear($dbPaidDate, $m->year)
                    ->sum('total_invoice');
                $monthlyRevenueTrend[] = ['label' => $m->format('M'), 'amount' => $sum];
            }
            $maxMonthlyRevenue = collect($monthlyRevenueTrend)->max('amount') ?: 1;

            // 4. NEW CLIENTS
            $newClientsThisMonth = \App\Models\Client::where('assigned_to', $userId)
                ->whereMonth('created_at', $selectedMonth->month)
                ->whereYear('created_at', $selectedMonth->year)
                ->with(['business', 'services'])
                ->orderByDesc('created_at')
                ->get();

            // 5. GOALS & KPI — the weighted per-position KPI template set on the KPI
            // builder page, resolved to this employee via their contract's
            // division/sub-division. Null/empty when nobody has set one up yet for
            // their position — the partial shows a plain "not set up yet" message
            // rather than fabricating numbers.
            $kpiTemplate = $employee ? \App\Models\KpiTemplate::forEmployee($employee) : null;
            $kpiGoalGroups = $kpiTemplate ? $kpiTemplate->goalGroups() : [];

            return view('admin.performance.index', compact(
                'dashboardAvailable', 'divisionName',
                'viewingOther', 'employee',
                'selectedMonth', 'prevMonth', 'nextMonth', 'isCurrentMonth',
                'monthlyTarget', 'quarterlyTarget', 'yearlyTarget',
                'placementsThisMonth', 'placementsThisQuarter', 'placementsThisYear',
                'openVacancies', 'overdueVacancyCount',
                'revenueThisMonth', 'revenueYTD', 'revenueChangePercent',
                'monthlyRevenueTrend', 'maxMonthlyRevenue',
                'newClientsThisMonth', 'kpiGoalGroups',
                'isSuperAdmin', 'ytdDashboardCards'
            ));
        }

        // ==================== FINANCE & ACCOUNTS ====================
        // Unlike HR, Finance manages company-wide invoicing — there's no personal
        // "assigned_to" concept here. All invoice data is shown at a team level,
        // reflecting the Finance team's collective performance as the department
        // responsible for collections, receivables, and billing.

        $dbFinDate     = \Illuminate\Support\Facades\DB::raw('COALESCE(payment_date, DATE(updated_at))');
        $dbFinDateTime = \Illuminate\Support\Facades\DB::raw('COALESCE(payment_date, updated_at)');

        // KPI targets (invoice count — how many invoices collected per period)
        $finMonthlyTarget   = 15;
        $finQuarterlyTarget = 45;
        $finYearlyTarget    = 180;

        // 1. COLLECTIONS — count of paid invoices per period
        $finCollectedThisMonth = \App\Models\ClientInvoice::where('status', 'paid')
            ->whereMonth($dbFinDate, $selectedMonth->month)
            ->whereYear($dbFinDate, $selectedMonth->year)
            ->count();

        $finCollectedThisQuarter = \App\Models\ClientInvoice::where('status', 'paid')
            ->whereBetween(
                \Illuminate\Support\Facades\DB::raw('DATE(COALESCE(payment_date, updated_at))'),
                [$quarterStart->toDateString(), $quarterEnd->toDateString()]
            )
            ->count();

        $finCollectedThisYear = \App\Models\ClientInvoice::where('status', 'paid')
            ->whereYear($dbFinDate, $selectedMonth->year)
            ->count();

        // 2. REVENUE COLLECTED — IDR amounts
        $finRevenueThisMonth = (float) \App\Models\ClientInvoice::where('status', 'paid')
            ->whereMonth($dbFinDate, $selectedMonth->month)
            ->whereYear($dbFinDate, $selectedMonth->year)
            ->sum('total_invoice');

        $finRevenueYTD = (float) \App\Models\ClientInvoice::where('status', 'paid')
            ->whereYear($dbFinDate, $selectedMonth->year)
            ->whereDate($dbFinDateTime, '<=', $selectedMonth->copy()->endOfMonth())
            ->sum('total_invoice');

        $finLastMonthRevenue = (float) \App\Models\ClientInvoice::where('status', 'paid')
            ->whereMonth($dbFinDate, $selectedMonth->copy()->subMonth()->month)
            ->whereYear($dbFinDate, $selectedMonth->copy()->subMonth()->year)
            ->sum('total_invoice');

        $finRevenueChangePercent = $finLastMonthRevenue > 0
            ? round((($finRevenueThisMonth - $finLastMonthRevenue) / $finLastMonthRevenue) * 100)
            : null;

        $finMonthlyRevenueTrend = [];
        for ($i = 5; $i >= 0; $i--) {
            $m   = $selectedMonth->copy()->subMonths($i);
            $sum = (float) \App\Models\ClientInvoice::where('status', 'paid')
                ->whereMonth($dbFinDate, $m->month)
                ->whereYear($dbFinDate, $m->year)
                ->sum('total_invoice');
            $finMonthlyRevenueTrend[] = ['label' => $m->format('M'), 'amount' => $sum];
        }
        $finMaxMonthlyRevenue = collect($finMonthlyRevenueTrend)->max('amount') ?: 1;

        // 3. OUTSTANDING RECEIVABLES — all unpaid invoices (current snapshot, no month filter)
        $finOutstandingInvoices = \App\Models\ClientInvoice::where('status', '!=', 'paid')
            ->with('client.business')
            ->orderByRaw('CASE WHEN due_date IS NULL THEN 1 ELSE 0 END')
            ->orderBy('due_date')
            ->get()
            ->map(function ($inv) use ($now) {
                $dueDate   = $inv->due_date ? \Carbon\Carbon::parse($inv->due_date) : null;
                $isOverdue = $dueDate && $dueDate->lt($now);
                return (object) [
                    'id'          => $inv->id,
                    'client_name' => $inv->client?->business?->name
                                  ?? $inv->client?->full_name
                                  ?? 'Unknown Client',
                    'amount'      => (float) $inv->total_invoice,
                    'status'      => $inv->status,
                    'due_date'    => $dueDate,
                    'is_overdue'  => $isOverdue,
                    'days_overdue'=> $isOverdue ? (int) $dueDate->diffInDays($now) : 0,
                ];
            });

        $finOverdueInvoiceCount    = $finOutstandingInvoices->where('is_overdue', true)->count();
        $finTotalOutstandingAmount = $finOutstandingInvoices->sum('amount');

        // 4. NEW INVOICES RAISED — invoices created in the selected month
        $finNewInvoicesThisMonth = \App\Models\ClientInvoice::whereMonth('created_at', $selectedMonth->month)
            ->whereYear('created_at', $selectedMonth->year)
            ->with('client.business')
            ->orderByDesc('created_at')
            ->get();

        return view('admin.performance.index', compact(
            'dashboardAvailable', 'divisionName',
            'viewingOther', 'employee',
            'selectedMonth', 'prevMonth', 'nextMonth', 'isCurrentMonth',
            'finMonthlyTarget', 'finQuarterlyTarget', 'finYearlyTarget',
            'finCollectedThisMonth', 'finCollectedThisQuarter', 'finCollectedThisYear',
            'finRevenueThisMonth', 'finRevenueYTD', 'finRevenueChangePercent',
            'finMonthlyRevenueTrend', 'finMaxMonthlyRevenue',
            'finOutstandingInvoices', 'finOverdueInvoiceCount', 'finTotalOutstandingAmount',
            'finNewInvoicesThisMonth',
            'isSuperAdmin', 'ytdDashboardCards'
        ));
    }

    public function linkersHub()
    {
        $nonTerminatedStatuses = ['active', 'probation', 'on-leave'];

        // Active section: active + probation + on-leave (grouped by division)
        $activeEmployees = \App\Models\Employee::with('division')
            ->whereIn('status', $nonTerminatedStatuses)
            ->orderBy('first_name')
            ->get();

        // Joining Soon: added but not yet registered (no user account)
        $joiningEmployees = \App\Models\Employee::with('division')
            ->where('status', 'joining-soon')
            ->orderBy('first_name')
            ->get();

        $terminatedEmployees = \App\Models\Employee::with('division')
            ->where('status', 'terminated')
            ->orderBy('first_name')
            ->get();

        // Apply on-leave override based on today's absences
        foreach ($activeEmployees as $emp) {
            $emp->status = $this->effectiveStatus($emp);
        }

        $grouped = $activeEmployees->groupBy(fn($e) => $e->division?->name ?? 'No team');

        // All divisions (teams) so that Manage Teams shows every team,
        // including teams that currently have no active employees assigned.
        $allDivisions = \App\Models\Division::query()->orderBy('name')->get();

        return view('admin.linkers-hub.index', [
            'groupedEmployees'    => $grouped,
            'joiningEmployees'    => $joiningEmployees,
            'terminatedEmployees' => $terminatedEmployees,
            'totalActive'         => $activeEmployees->count(),
            'totalTerminated'     => $terminatedEmployees->count(),
            'allDivisions'        => $allDivisions,
        ]);
    }

    public function addEmployee()
    {
        return view('admin.linkers-hub.add-employee');
    }

    /**
     * Show a single employee's profile (admin view).
     * Displays the selected employee's details, not the logged-in admin's profile.
     */
    public function showEmployeeProfile($id)
    {
        $employee = \App\Models\Employee::with(['division', 'subDivision', 'position', 'manager', 'managerExternal', 'payrollDetail', 'documents', 'emergencyContacts', 'employmentDetail', 'folders', 'equipmentOnLoan'])->findOrFail($id);
        $absences = \App\Models\EmployeeAbsence::where('employee_id', $employee->id)
            ->orderBy('start_date', 'desc')
            ->get();
        // Calculate days for annual/other (end_date - start_date + 1)
        $absences->each(function ($a) {
            if ($a->end_date && $a->start_date) {
                $a->days = $a->start_date->diffInDays($a->end_date) + 1;
            } else {
                $a->days = 1;
            }
            $a->is_ongoing = $a->ongoing;
        });

        // Override display status with on-leave if absence is active today
        $employee->status = $this->effectiveStatus($employee);

        $profileProgress = $this->computeProfileProgress($employee);

        // Required documents: one consolidated folder + a checklist of
        // which required document types have been uploaded into it.
        $requiredFolder    = $this->ensureRequiredDocumentsFolder($employee);
        $requiredDocTypes  = $this->requiredDocTypesFor($employee);
        $uploadedDocTypes  = $this->uploadedRequiredDocTypes($requiredFolder, $requiredDocTypes);

        // Map each uploaded type to its actual file ID, so the "View" link in
        // the blade (which reads $reqFolder->file_id) has something to point
        // to. If a type somehow has more than one file uploaded, use the most
        // recently uploaded one.
        $uploadedFileIdsByType = \App\Models\EmployeeFile::where('folder_id', $requiredFolder->id)
            ->whereIn('document_type', array_keys($requiredDocTypes))
            ->orderByDesc('created_at')
            ->get()
            ->groupBy('document_type')
            ->map(fn ($files) => $files->first()->id);

        $requiredChecklist = collect($requiredDocTypes)->map(function ($label, $type) use ($uploadedDocTypes, $uploadedFileIdsByType) {
            return (object) [
                'type'     => $type,
                'name'     => $label,
                'has_file' => in_array($type, $uploadedDocTypes, true),
                'file_id'  => $uploadedFileIdsByType->get($type),
                'kind'     => 'file',
            ];
        })->values();
        $requiredDocsLocked = collect($requiredDocTypes)->keys()->diff($uploadedDocTypes)->isEmpty();

        // Contract and KPI template aren't uploaded files — they're existence
        // checks against other parts of the system — so they're appended
        // here as 'link' items rather than folded into requiredDocTypesFor()/
        // uploadedRequiredDocTypes() above, which are file-upload-specific.
        // Missing ones render as a harder red warning (not the softer amber
        // used for a missing file) since these two are considered non-
        // negotiable, not just "not gotten to yet".
        $requiredChecklist->push((object) [
            'type'         => 'contract',
            'name'         => 'Contract',
            'has_file'     => (bool) $employee->contract_id,
            'file_id'      => null,
            'kind'         => 'link',
            'view_url'     => route('admin.contracts.index'),
            'missing_text' => 'No contract on file',
        ]);
        $requiredChecklist->push((object) [
            'type'         => 'kpi',
            'name'         => 'KPI Template',
            'has_file'     => (bool) \App\Models\KpiTemplate::forEmployee($employee),
            'file_id'      => null,
            'kind'         => 'link',
            'view_url'     => route('admin.kpi-jd.kpi-list'),
            'missing_text' => 'No KPI template set up for this position yet',
        ]);

        // Shifts tab: everything from Roster Plans this employee is tied to,
        // split into assigned (pending/accepted/declined) vs open shifts
        // (eligible/claimed) they can self-serve claim, and each of those
        // into upcoming vs past by shift_date.
        // NOTE: RosterPlanShift::shift_date is intentionally NOT cast to a
        // Carbon date on the model (other code — RosterController@index,
        // several groupBy('shift_date') calls in RosterPlanController —
        // relies on it being a plain string, e.g. as an array key, and a
        // Carbon object breaks those). Instead we attach a separate
        // `shift_date_carbon` property to each shift here, scoped to this
        // method only, for date comparisons/formatting in the Shifts tab.
        $today = \Carbon\Carbon::today();
        $withCarbonDate = function ($shift) {
            $shift->shift_date_carbon = \Carbon\Carbon::parse($shift->shift_date);
            return $shift;
        };

        $employeeShifts = \App\Models\RosterPlanShift::with(['plan', 'employees' => function ($q) use ($employee) {
                $q->where('employees.id', $employee->id);
            }])
            ->whereHas('employees', fn ($q) => $q->where('employees.id', $employee->id))
            ->get()
            ->each(function ($shift) use ($withCarbonDate) {
                $shift->my_status = optional($shift->employees->first())->pivot->status;
                $withCarbonDate($shift);
            })
            ->sortBy('shift_date')
            ->values();

        $assignedShifts = $employeeShifts->whereNotIn('my_status', ['eligible', 'claimed'])->values();
        $upcomingShifts = $assignedShifts->filter(fn ($s) => $s->shift_date_carbon->gte($today))->values();
        $pastShifts     = $assignedShifts->filter(fn ($s) => $s->shift_date_carbon->lt($today))->sortByDesc('shift_date')->values();
        $openShifts     = $employeeShifts->whereIn('my_status', ['eligible', 'claimed'])
            ->filter(fn ($s) => $s->shift_date_carbon->gte($today))->values();

        $rosterSettings = \App\Models\RosterSetting::current();
        $canAcceptDecline = $rosterSettings->accept_decline_enabled
            && (empty($rosterSettings->acceptDeclineEmployeeIds()) || in_array((string) $employee->id, $rosterSettings->acceptDeclineEmployeeIds(), true));

        // Open shifts this employee could still request — published, open,
        // upcoming, and not already in their pivot (eligible/claimed/etc).
        $availableOpenShifts = $rosterSettings->open_shifts_enabled
            ? \App\Models\RosterPlanShift::with(['plan', 'employees'])
                ->where('is_open', true)
                ->whereDate('shift_date', '>=', $today)
                ->whereDoesntHave('employees', fn ($q) => $q->where('employees.id', $employee->id))
                ->get()
                ->each($withCarbonDate)
                ->sortBy('shift_date')
                ->values()
            : collect();

        // KPI tab: same KPI template + weighted-progress calculation used on the
        // Performance page (KpiTemplate::forEmployee / goalGroups), so this tab and
        // the employee's own Performance view always show the same numbers.
        $kpiTemplate = \App\Models\KpiTemplate::forEmployee($employee);
        $kpiGoalGroups = $kpiTemplate ? $kpiTemplate->goalGroups() : [];
        // Compact single-number rollup for this tab's summary + motivational
        // message; the full per-area/per-indicator breakdown now lives on the
        // Performance page instead (see AdminController::performance()).
        $kpiSummary = $kpiTemplate ? $kpiTemplate->overallSummary() : null;

        $allDivisions = \App\Models\Division::where('is_active', true)->orderBy('name')->get(['id', 'name']);
        $allSubDivisions = \App\Models\SubDivision::where('is_active', true)->orderBy('name')->get(['id', 'name', 'division_id']);
        $allPositions = \App\Models\Position::where('is_active', true)->orderBy('name')->get(['id', 'name', 'division_id', 'sub_division_id']);
        $possibleManagers = \App\Models\Employee::where('id', '!=', $employee->id)
            ->orderBy('first_name')->orderBy('last_name')
            ->get(['id', 'first_name', 'middle_name', 'last_name', 'position_title']);
        $externalManagers = \App\Models\ManagerExternal::orderBy('name')->get(['id', 'name', 'title']);

        return view('admin.linkers-hub.employee-profile', [
            'employee'           => $employee,
            'allDivisions'       => $allDivisions,
            'allSubDivisions'    => $allSubDivisions,
            'allPositions'       => $allPositions,
            'possibleManagers'   => $possibleManagers,
            'externalManagers'   => $externalManagers,
            'absences'           => $absences,
            'profileProgress'    => $profileProgress,
            'requiredFolders'    => $requiredChecklist,
            'requiredFolderId'   => $requiredFolder->id,
            'requiredDocsLocked' => $requiredDocsLocked,
            'upcomingShifts'     => $upcomingShifts,
            'pastShifts'         => $pastShifts,
            'openShifts'         => $openShifts,
            'rosterSettings'     => $rosterSettings,
            'canAcceptDecline'   => $canAcceptDecline,
            'availableOpenShifts' => $availableOpenShifts,
            'kpiGoalGroups'      => $kpiGoalGroups,
            'kpiSummary'         => $kpiSummary,
        ]);
    }

    /**
     * Compute profile completion progress (0–100, in steps of 25).
     * Each of the 4 tabs contributes 25% when its required fields are complete.
     */
    private function computeProfileProgress(\App\Models\Employee $employee): array
    {
        $edf = fn($field) => optional($employee->employmentDetail)->{$field};
        $pgf = fn($field) => optional($employee->payrollDetail)->{$field};

        // Employment (25%): every field marked as required across the
        // Employment information, Role information, Pay details, Payroll
        // information, Bank details, and Sensitive information cards.
        //
        // Note: "Employment type" (Employment info card) and "Contract type"
        // (Role info card) both read from the same underlying pair of
        // fields (employment_basis / employmentDetail.employee_type), as do
        // "Entitlement unit" and "Leave taken in" (both = employmentDetail.
        // leave_unit) — checking each field once here covers both display
        // rows for each pair.
        $employmentTypeSet = $employee->employment_basis || $edf('employee_type');

        $employment = $employmentTypeSet                  // Employment type / Contract type
            && $edf('leave_unit')                          // Entitlement unit / Leave taken in
            && $employee->start_date                       // Contract start date
            && $edf('working_pattern')                     // Hours of work
            && $edf('place_of_work')                       // Working location
            && $edf('jurisdiction')                        // Public holidays for
            && $employee->position_title                  // Job title
            && $pgf('salary')                              // Amount/rate
            && $pgf('pay_rate')                            // Hourly rate (pay rate unit)
            && $pgf('pay_frequency')                       // Payment frequency
            && $edf('effective_from')                      // Effective date
            && $pgf('payroll_no')                          // Payroll number
            && $pgf('bank_acc_name')                       // Name on account
            && $pgf('bank_name')                           // Name of bank
            && $pgf('bank_acc_no')                         // Account number
            && $pgf('tfn')                                 // Tax File Number
            && $pgf('police_check_conducted');             // National police check conducted

        // Personal (25%): both Contact information AND Personal information
        // must have every required field filled in.
        //   Contact information: account email, personal email, mobile phone
        //   Personal information: title, first name, last name, DOB, gender, address
        $contactInfoComplete = $employee->email
            && $employee->personal_email
            && $employee->phone;

        $personalInfoComplete = $employee->title
            && $employee->first_name
            && $employee->last_name
            && $employee->birth_info
            && $employee->gender
            && ($employee->address || $employee->address_1);

        $personal = $contactInfoComplete && $personalInfoComplete;

        // Emergencies (25%): at least 1 contact with first_name + mobile_phone
        // + address_1 + relationship all filled in.
        $emergencies = $employee->emergencyContacts
            ->filter(fn($c) => $c->first_name && $c->mobile_phone && $c->address_1 && $c->relationship)
            ->isNotEmpty();

        // Documents (25%): every required document type (incl. Visa, only if
        // applicable) must have a file uploaded into the Required Documents folder.
        $requiredFolder   = $this->ensureRequiredDocumentsFolder($employee);
        $requiredDocTypes = $this->requiredDocTypesFor($employee);
        $uploadedTypes    = $this->uploadedRequiredDocTypes($requiredFolder, $requiredDocTypes);
        $documents        = collect($requiredDocTypes)->keys()->diff($uploadedTypes)->isEmpty();

        $tabs = [
            'employment'  => (bool) $employment,
            'personal'    => (bool) $personal,
            'emergencies' => (bool) $emergencies,
            'documents'   => (bool) $documents,
        ];

        $completedTabs = count(array_filter($tabs));
        $percent = $completedTabs * 25;

        return [
            'percent'   => $percent,
            'tabs'      => $tabs,
            'complete'  => $percent === 100,
        ];
    }

    /**
     * Show the "Add absence" form page for an employee.
     * $type: annual | personal | lateness | other
     * NOTE: This currently only renders the form. Saving to the database
     * will be added in a later stage.
     */
    public function showAddAbsence($id, $type)
    {
        $allowed = ['annual', 'personal', 'lateness', 'other'];
        abort_unless(in_array($type, $allowed, true), 404);
        $employee = \App\Models\Employee::with('division')->findOrFail($id);
        return view('admin.linkers-hub.add-absence', ['employee' => $employee, 'type' => $type]);
    }

    public function storeEmployee(Request $request)
    {
        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
        ]);

        $firstName = trim($validated['first_name']);
        $lastName = trim($validated['last_name']);

        // Validate both names
        $firstError = $this->validateEmployeeName($firstName, 'First name');
        $lastError = $this->validateEmployeeName($lastName, 'Last name');

        if ($firstError || $lastError) {
            return response()->json([
                'success' => false,
                'errors' => array_filter([
                    'first_name' => $firstError,
                    'last_name' => $lastError,
                ])
            ], 422);
        }

        // Format names properly (Title Case)
        $firstName = ucwords(strtolower($firstName));
        $lastName = ucwords(strtolower($lastName));

        // Create employee in database
        try {
            // Backend duplicate guard — check first_name + last_name + dob
            $dob = trim($request->input('dob', ''));
            if ($dob) {
                $duplicate = \App\Models\Employee::whereRaw('LOWER(first_name) = ?', [strtolower($firstName)])
                    ->whereRaw('LOWER(last_name) = ?', [strtolower($lastName)])
                    ->where('birth_info', $dob)
                    ->exists();

                if ($duplicate) {
                    return response()->json([
                        'success' => false,
                        'message' => "An employee named {$firstName} {$lastName} with this date of birth already exists in the system.",
                        'duplicate' => true,
                    ], 422);
                }
            }

            $employee = \App\Models\Employee::create([
                'first_name' => $firstName,
                'last_name'  => $lastName,
                'status'     => 'joining-soon',
            ]);

            // Auto-create the consolidated required-documents folder
            $this->ensureRequiredDocumentsFolder($employee);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Database error: ' . $e->getMessage()
            ], 500);
        }

        return response()->json([
            'success' => true,
            'message' => 'Employee ' . $firstName . ' ' . $lastName . ' has been added.',
            'employee' => $employee
        ]);
    }

    /**
     * Required document types for every employee, keyed by internal type => display name.
     * Visa is added conditionally by requiredDocTypesFor() below.
     */
    private const REQUIRED_DOC_TYPES = [
        'id_passport'  => 'ID / Passport',
        'blood_type'   => 'Blood Type Certificate',
        'police_check' => 'Police Check / SKCK',
    ];

    /**
     * Returns the required document types for a given employee.
     * Visa is only required if the employee has a visa record with BOTH
     * a document number AND an expiry date filled in (set either during
     * the Add Employee wizard, or later from the Employment tab).
     */
    private function requiredDocTypesFor(\App\Models\Employee $employee): array
    {
        $types = self::REQUIRED_DOC_TYPES;

        $hasCompleteVisa = $employee->visas()
            ->whereNotNull('document_no')->where('document_no', '!=', '')
            ->whereNotNull('expiry_date')
            ->exists();

        if ($hasCompleteVisa) {
            $types['visa'] = 'Visa';
        }

        return $types;
    }

    /**
     * Ensure the single consolidated "Required Documents" folder exists for
     * this employee. Safe to call repeatedly (idempotent).
     */
    private function ensureRequiredDocumentsFolder(\App\Models\Employee $employee): \App\Models\EmployeeFolder
    {
        return \App\Models\EmployeeFolder::firstOrCreate(
            ['employee_id' => $employee->id, 'is_required' => true],
            [
                'parent_id'   => null,
                'name'        => 'Required Documents',
                'color'       => '#2e7d5e',
                'doc_type'    => null,
                'is_required' => true,
            ]
        );
    }

    /**
     * Which required document types (out of the ones that apply to this
     * employee) already have an uploaded file.
     */
    private function uploadedRequiredDocTypes(\App\Models\EmployeeFolder $requiredFolder, array $requiredTypes): array
    {
        return \App\Models\EmployeeFile::where('folder_id', $requiredFolder->id)
            ->whereIn('document_type', array_keys($requiredTypes))
            ->pluck('document_type')
            ->unique()
            ->all();
    }

    /**
     * The consolidated "Required Documents" folder is a system-managed
     * folder that must always exist for uploads to have somewhere to go —
     * it can never be renamed or deleted, regardless of how many of the
     * required document types have been uploaded into it yet.
     */
    private function isRequiredFolderLocked(\App\Models\Employee $employee, \App\Models\EmployeeFolder $folder): bool
    {
        return (bool) $folder->is_required;
    }

    /**
     * Check if an employee with the same first name, last name, and date of birth already exists.
     * Used by the add-employee wizard for real-time duplicate detection.
     */
    public function checkDuplicateEmployee(Request $request)
    {
        $firstName = trim($request->input('first_name', ''));
        $lastName  = trim($request->input('last_name', ''));
        $dob       = trim($request->input('dob', ''));

        if (!$firstName || !$lastName || !$dob) {
            return response()->json(['duplicate' => false]);
        }

        $exists = \App\Models\Employee::whereRaw('LOWER(first_name) = ?', [strtolower($firstName)])
            ->whereRaw('LOWER(last_name) = ?', [strtolower($lastName)])
            ->where('birth_info', $dob)
            ->exists();

        return response()->json([
            'duplicate' => $exists,
            'message'   => $exists
                ? "An employee named {$firstName} {$lastName} with this date of birth already exists in the system."
                : null,
        ]);
    }

    public function serveAvatar($id)
    {
        $employee = \App\Models\Employee::findOrFail($id);

        if (!$employee->avatar_path) {
            abort(404);
        }

        $fullPath = public_path($employee->avatar_path);

        if (!file_exists($fullPath)) {
            abort(404);
        }

        $mimeType = mime_content_type($fullPath);
        return response()->file($fullPath, ['Content-Type' => $mimeType]);
    }

    public function uploadAvatar(Request $request, $id)
    {
        $employee = \App\Models\Employee::findOrFail($id);

        $request->validate(['avatar' => 'required|image|mimes:jpeg,png,jpg,webp|max:2048']);

        // Delete old avatar if exists
        if ($employee->avatar_path) {
            $oldPath = public_path($employee->avatar_path);
            if (file_exists($oldPath)) @unlink($oldPath);
        }

        $file     = $request->file('avatar');
        $filename = uniqid() . '_' . time() . '.' . $file->getClientOriginalExtension();
        $file->move(public_path('uploads/avatars'), $filename);

        $relativePath = 'uploads/avatars/' . $filename;
        $employee->update(['avatar_path' => $relativePath]);

        return response()->json([
            'success' => true,
            'url'     => route('admin.linkers-hub.serve-avatar', $id),
        ]);
    }

    public function deleteAvatar($id)
    {
        $employee = \App\Models\Employee::findOrFail($id);

        if ($employee->avatar_path) {
            $fullPath = public_path($employee->avatar_path);
            if (file_exists($fullPath)) @unlink($fullPath);
        }

        $employee->update(['avatar_path' => null]);

        return response()->json(['success' => true]);
    }

    // ── End Avatar ──────────────────────────────────────────────────────

    // ── Emergency Contacts ──────────────────────────────────────────────

    public function storeEmergencyContact(Request $request, $employeeId)
    {
        $employee = \App\Models\Employee::findOrFail($employeeId);

        $request->validate([
            'first_name'   => 'required|string|max:255',
            'mobile_phone' => 'required|string|max:50',
            'address_1'    => 'required|string|max:255',
            'relationship' => 'required|string|max:100',
        ]);

        $data = $request->only([
            'first_name', 'last_name', 'relationship', 'is_primary',
            'home_phone', 'mobile_phone', 'work_phone',
            'address_1', 'address_2', 'address_3',
            'city', 'territory', 'postcode', 'country',
        ]);

        $data['first_name'] = $data['first_name'] ?? '';

        // If this is set as primary, unset all others first
        if (!empty($data['is_primary'])) {
            $employee->emergencyContacts()->update(['is_primary' => false]);
        }

        $contact = $employee->emergencyContacts()->create($data);

        return response()->json(['success' => true, 'contact' => $contact]);
    }

    public function updateEmergencyContact(Request $request, $employeeId, $contactId)
    {
        $contact = \App\Models\EmployeeEmergencyContact::where('employee_id', $employeeId)->findOrFail($contactId);

        $request->validate([
            'first_name'   => 'required|string|max:255',
            'mobile_phone' => 'required|string|max:50',
            'address_1'    => 'required|string|max:255',
            'relationship' => 'required|string|max:100',
        ]);

        $data = $request->only([
            'first_name', 'last_name', 'relationship', 'is_primary',
            'home_phone', 'mobile_phone', 'work_phone',
            'address_1', 'address_2', 'address_3',
            'city', 'territory', 'postcode', 'country',
        ]);

        // If setting as primary, unset all others first
        if (!empty($data['is_primary'])) {
            \App\Models\EmployeeEmergencyContact::where('employee_id', $employeeId)
                ->where('id', '!=', $contactId)
                ->update(['is_primary' => false]);
        }

        $contact->update($data);

        return response()->json(['success' => true, 'contact' => $contact]);
    }

    public function destroyEmergencyContact($employeeId, $contactId)
    {
        $contact = \App\Models\EmployeeEmergencyContact::where('employee_id', $employeeId)->findOrFail($contactId);
        $contact->delete();

        return response()->json(['success' => true]);
    }

    // ── End Emergency Contacts ──────────────────────────────────────────

    // ── Employee Files & Folders ────────────────────────────────────────

    public function listEmployeeFiles(Request $request, $id)
    {
        $employee = \App\Models\Employee::findOrFail($id);
        $folderId = $request->query('folder_id') ?: null;
        $search   = trim($request->query('search', ''));
        $perPage  = (int) $request->query('per_page', 10);
        $page     = (int) $request->query('page', 1);

        if ($search !== '') {
            $folders = \App\Models\EmployeeFolder::where('employee_id', $employee->id)
                ->where('name', 'like', '%' . $search . '%')
                ->withCount('files')
                ->orderBy('name')
                ->get()
                ->map(fn($folder) => [
                    'id' => $folder->id, 'type' => 'folder', 'name' => $folder->name,
                    'color' => $folder->color, 'file_count' => $folder->files_count,
                    'created_at' => $folder->created_at->format('d M Y, H:i'),
                    'is_required' => (bool) $folder->is_required,
                    'locked' => $this->isRequiredFolderLocked($employee, $folder),
                ]);

            $filesQuery = \App\Models\EmployeeFile::where('employee_id', $employee->id)
                ->where('original_name', 'like', '%' . $search . '%')
                ->with('folder')
                ->orderBy('created_at', 'desc');
            $totalFiles = $filesQuery->count();
            $files = $filesQuery->skip(($page - 1) * $perPage)->take($perPage)->get();
            return response()->json([
                'success' => true, 'folders' => $folders,
                'files' => $files->map(fn($f) => $this->formatFile($f)),
                'total' => $totalFiles + $folders->count(), 'page' => $page, 'per_page' => $perPage,
                'folder_id' => null, 'search' => $search,
            ]);
        }

        $folders = \App\Models\EmployeeFolder::where('employee_id', $employee->id)
            ->where('parent_id', $folderId)
            ->withCount('files')           // single query instead of N+1
            ->orderBy('name')
            ->get()
            ->map(fn($folder) => [
                'id' => $folder->id, 'type' => 'folder', 'name' => $folder->name,
                'color' => $folder->color, 'file_count' => $folder->files_count,
                'created_at' => $folder->created_at->format('d M Y, H:i'),
                'is_required' => (bool) $folder->is_required,
                'locked' => $this->isRequiredFolderLocked($employee, $folder),
            ]);

        $filesQuery = \App\Models\EmployeeFile::where('employee_id', $employee->id)
            ->where('folder_id', $folderId)->orderBy('created_at', 'desc');
        $total = $filesQuery->count();
        $files = $filesQuery->skip(($page - 1) * $perPage)->take($perPage)->get();

        return response()->json([
            'success' => true, 'folders' => $folders,
            'files' => $files->map(fn($f) => $this->formatFile($f)),
            'total' => $total, 'page' => $page, 'per_page' => $perPage,
            'folder_id' => $folderId,
            'breadcrumb' => $this->buildFolderBreadcrumb($folderId),
        ]);
    }

    public function uploadEmployeeFile(Request $request, $id)
    {
        $employee = \App\Models\Employee::findOrFail($id);
        $request->validate([
            'files'   => 'required|array|max:100',
            'files.*' => 'required|file|max:30720',
            'folder_id' => 'nullable|integer',
            'document_type' => 'nullable|string|max:50',
        ]);
        $folderId = $request->input('folder_id') ?: null;
        $folder = null;
        if ($folderId) {
            $folder = \App\Models\EmployeeFolder::where('employee_id', $employee->id)->findOrFail($folderId);
        }

        // If uploading into the Required Documents folder, the person must
        // pick which required document type this file satisfies.
        $documentType = null;
        if ($folder && $folder->is_required) {
            $validTypes = array_keys($this->requiredDocTypesFor($employee));
            $documentType = $request->input('document_type');
            if (!$documentType || !in_array($documentType, $validTypes, true)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Please select which required document this file is before uploading.',
                ], 422);
            }
        }

        $uploaded = []; $errors = [];
        foreach ($request->file('files') as $file) {
            try {
                $originalName = $file->getClientOriginalName();
                $ext          = $file->getClientOriginalExtension();
                $storedName   = \Illuminate\Support\Str::uuid() . ($ext ? '.' . $ext : '');
                $diskPath     = 'employee-files/' . $employee->id . '/' . $storedName;
                $file->storeAs('employee-files/' . $employee->id, $storedName, 'private');
                $record = \App\Models\EmployeeFile::create([
                    'employee_id' => $employee->id, 'folder_id' => $folderId,
                    'original_name' => $originalName, 'stored_name' => $storedName,
                    'disk_path' => $diskPath, 'mime_type' => $file->getMimeType(),
                    'file_size' => $file->getSize(), 'uploaded_by' => auth()->id(),
                    'document_type' => $documentType,
                ]);
                $uploaded[] = $this->formatFile($record);
            } catch (\Exception $e) {
                $errors[] = $file->getClientOriginalName() . ': ' . $e->getMessage();
            }
        }
        return response()->json([
            'success' => count($uploaded) > 0, 'uploaded' => $uploaded, 'errors' => $errors,
            'message' => count($uploaded) . ' file(s) uploaded successfully' . (count($errors) ? ', ' . count($errors) . ' failed' : '') . '.',
        ]);
    }

    public function downloadEmployeeFile($id, $fileId)
    {
        $employee = \App\Models\Employee::findOrFail($id);
        $file     = \App\Models\EmployeeFile::where('employee_id', $employee->id)->findOrFail($fileId);
        if (!\Illuminate\Support\Facades\Storage::disk('private')->exists($file->disk_path)) {
            abort(404, 'File not found on disk.');
        }
        return \Illuminate\Support\Facades\Storage::disk('private')->download($file->disk_path, $file->original_name);
    }

    /**
     * Stream file inline for browser preview (PDF, images, etc.)
     * GET /admin/linkers-hub/employees/{id}/files/{fileId}/view
     */
    public function viewEmployeeFile($id, $fileId)
    {
        $employee = \App\Models\Employee::findOrFail($id);
        $file     = \App\Models\EmployeeFile::where('employee_id', $employee->id)->findOrFail($fileId);

        if (!\Illuminate\Support\Facades\Storage::disk('private')->exists($file->disk_path)) {
            abort(404, 'File not found on disk.');
        }

        $path     = \Illuminate\Support\Facades\Storage::disk('private')->path($file->disk_path);
        $mime     = $file->mime_type ?: mime_content_type($path) ?: 'application/octet-stream';

        return response()->file($path, [
            'Content-Type'        => $mime,
            'Content-Disposition' => 'inline; filename="' . addslashes($file->original_name) . '"',
        ]);
    }

    public function deleteEmployeeFile($id, $fileId)
    {
        $employee = \App\Models\Employee::findOrFail($id);
        $file     = \App\Models\EmployeeFile::where('employee_id', $employee->id)->findOrFail($fileId);
        \Illuminate\Support\Facades\Storage::disk('private')->delete($file->disk_path);
        $file->delete();
        return response()->json(['success' => true, 'message' => 'File deleted.']);
    }

    /**
     * Bulk-download selected files and/or folders (recursively including
     * subfolders) as a single ZIP archive. Mirrors the same ownership-check
     * pattern (always scoped to this employee) and 'private' disk convention
     * used by downloadEmployeeFile() above.
     */
    public function bulkDownloadEmployeeFiles(Request $request, $id)
    {
        $employee = \App\Models\Employee::findOrFail($id);

        $fileIds = array_filter((array) $request->input('file_ids', []));
        $folderIds = array_filter((array) $request->input('folder_ids', []));

        // Expand each selected folder into all of its descendant subfolders,
        // so picking a folder grabs everything nested inside it too.
        $allFolderIds = [];
        $pending = $folderIds;
        while (!empty($pending)) {
            $allFolderIds = array_merge($allFolderIds, $pending);
            $pending = \App\Models\EmployeeFolder::where('employee_id', $employee->id)
                ->whereIn('parent_id', $pending)
                ->pluck('id')
                ->all();
        }

        $filesFromFolders = !empty($allFolderIds)
            ? \App\Models\EmployeeFile::where('employee_id', $employee->id)
                ->whereIn('folder_id', $allFolderIds)
                ->pluck('id')
                ->all()
            : [];

        $allFileIds = array_unique(array_merge($fileIds, $filesFromFolders));

        $files = \App\Models\EmployeeFile::where('employee_id', $employee->id)
            ->whereIn('id', $allFileIds)
            ->get();

        if ($files->isEmpty()) {
            return redirect()->back()->with('error', 'No files found to download.');
        }

        $employeeName = $employee->full_name ?: 'employee';
        $zipFileName = 'documents-' . \Illuminate\Support\Str::slug($employeeName) . '-' . now()->format('Ymd-His') . '.zip';

        $tmpDir = storage_path('app/tmp');
        if (!is_dir($tmpDir)) {
            mkdir($tmpDir, 0755, true);
        }
        $zipPath = $tmpDir . '/' . $zipFileName;

        $zip = new \ZipArchive();
        $zip->open($zipPath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE);

        $usedNames = [];
        foreach ($files as $file) {
            if (!\Illuminate\Support\Facades\Storage::disk('private')->exists($file->disk_path)) {
                continue; // Skip files missing on disk rather than failing the whole zip
            }
            $realPath = \Illuminate\Support\Facades\Storage::disk('private')->path($file->disk_path);

            // Avoid two files with the same name overwriting each other inside the zip
            $entryName = $file->original_name;
            $suffix = 1;
            while (in_array($entryName, $usedNames, true)) {
                $entryName = pathinfo($file->original_name, PATHINFO_FILENAME) . " ({$suffix})." . pathinfo($file->original_name, PATHINFO_EXTENSION);
                $suffix++;
            }
            $usedNames[] = $entryName;

            $zip->addFile($realPath, $entryName);
        }

        $zip->close();

        return response()->download($zipPath, $zipFileName)->deleteFileAfterSend(true);
    }

    public function storeEmployeeFolder(Request $request, $id)
    {
        $employee  = \App\Models\Employee::findOrFail($id);
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'color' => 'nullable|string|max:20',
            'parent_id' => 'nullable|integer',
        ]);
        if (!empty($validated['parent_id'])) {
            \App\Models\EmployeeFolder::where('employee_id', $employee->id)->findOrFail($validated['parent_id']);
        }
        $folder = \App\Models\EmployeeFolder::create([
            'employee_id' => $employee->id,
            'parent_id'   => $validated['parent_id'] ?? null,
            'name'        => trim($validated['name']),
            'color'       => $validated['color'] ?? '#42a5f5',
        ]);
        return response()->json([
            'success' => true, 'message' => 'Folder created.',
            'folder'  => [
                'id' => $folder->id, 'type' => 'folder', 'name' => $folder->name,
                'color' => $folder->color, 'file_count' => 0,
                'created_at' => $folder->created_at->format('d M Y, H:i'),
            ],
        ]);
    }

    public function updateEmployeeFolder(Request $request, $id, $folderId)
    {
        $employee = \App\Models\Employee::findOrFail($id);
        $folder   = \App\Models\EmployeeFolder::where('employee_id', $employee->id)->findOrFail($folderId);
        if ($this->isRequiredFolderLocked($employee, $folder)) {
            return response()->json([
                'success' => false,
                'message' => 'The Required Documents folder cannot be renamed.',
            ], 422);
        }
        $validated = $request->validate([
            'name'  => 'sometimes|required|string|max:255',
            'color' => 'sometimes|nullable|string|max:20',
        ]);
        $folder->update(array_filter($validated, fn($v) => $v !== null));
        return response()->json(['success' => true, 'message' => 'Folder updated.']);
    }

    public function deleteEmployeeFolder($id, $folderId)
    {
        $employee = \App\Models\Employee::findOrFail($id);
        $folder   = \App\Models\EmployeeFolder::where('employee_id', $employee->id)->findOrFail($folderId);
        if ($this->isRequiredFolderLocked($employee, $folder)) {
            return response()->json([
                'success' => false,
                'message' => 'The Required Documents folder cannot be deleted.',
            ], 422);
        }
        $this->deleteFolderRecursive($folder);
        return response()->json(['success' => true, 'message' => 'Folder deleted.']);
    }

    private function deleteFolderRecursive(\App\Models\EmployeeFolder $folder): void
    {
        foreach ($folder->files as $file) {
            \Illuminate\Support\Facades\Storage::disk('private')->delete($file->disk_path);
            $file->delete();
        }
        foreach ($folder->children as $child) {
            $this->deleteFolderRecursive($child);
        }
        $folder->delete();
    }

    private function formatFile(\App\Models\EmployeeFile $f): array
    {
        return [
            'id' => $f->id, 'type' => 'file', 'name' => $f->original_name,
            'mime_type' => $f->mime_type, 'file_type' => $f->getTypeLabel(),
            'file_size' => $f->file_size_human,
            'created_at' => $f->created_at->format('d M Y, H:i'),
            'folder_id' => $f->folder_id,
            'document_type' => $f->document_type,
        ];
    }

    private function buildFolderBreadcrumb(?int $folderId): array
    {
        $trail = []; $current = $folderId ? \App\Models\EmployeeFolder::find($folderId) : null;
        while ($current) {
            array_unshift($trail, ['id' => $current->id, 'name' => $current->name]);
            $current = $current->parent_id ? \App\Models\EmployeeFolder::find($current->parent_id) : null;
        }
        return $trail;
    }

    // ── End Employee Files & Folders ────────────────────────────────────


    public function updateEmployee(Request $request, $id)
    {
        $employee = \App\Models\Employee::findOrFail($id);

        $validated = $request->validate([
            'first_name' => ['sometimes', 'required', 'string', 'max:255'],
            'last_name' => ['sometimes', 'required', 'string', 'max:255'],
            'email' => ['sometimes', 'nullable', 'email', 'max:255'],
            'start_date' => ['sometimes', 'nullable', 'date'],
        ]);

        if ($request->has('first_name')) {
            $firstError = $this->validateEmployeeName($request->first_name, 'First name');
            if ($firstError) {
                return response()->json(['success' => false, 'errors' => ['first_name' => $firstError]], 422);
            }
            $employee->first_name = ucwords(strtolower(trim($request->first_name)));
        }
        
        if ($request->has('last_name')) {
            $lastError = $this->validateEmployeeName($request->last_name, 'Last name');
            if ($lastError) {
                return response()->json(['success' => false, 'errors' => ['last_name' => $lastError]], 422);
            }
            $employee->last_name = ucwords(strtolower(trim($request->last_name)));
        }

        if ($request->has('title')) {
            $employee->title = $request->title;
        }
        if ($request->has('gender')) {
            $employee->gender = $request->gender;
        }
        if ($request->has('dob')) {
            $employee->birth_info = $request->dob;
        }
        if ($request->has('email')) {
            $employee->email = $request->email ? trim($request->email) : null;
        }
        if ($request->has('mobile')) {
            $employee->phone = $request->mobile ? trim($request->mobile) : null;
        }
        if ($request->has('job_title')) {
            $employee->position_title = $request->job_title ? trim($request->job_title) : null;
        }
        if ($request->has('position_title')) {
            $employee->position_title = $request->position_title ? trim($request->position_title) : null;
        }
        if ($request->has('employment_basis')) {
            $employee->employment_basis = $request->employment_basis ?: null;
        }
        if ($request->has('employee_type')) {
            $employee->employment_basis = $request->employee_type ?: null;
        }
        if ($request->has('probation_required')) {
            $employee->probation_required = (bool) $request->probation_required;
        }
        if ($request->has('probation_end_date')) {
            $employee->probation_end_date = $request->probation_end_date ?: null;
        }
        if ($request->has('notice_during_probation')) {
            $employee->notice_during_probation = $request->notice_during_probation ?: null;
        }

        // Auto-update employee status based on probation_required setting:
        // - probation ON  → set status to 'probation' (if currently active or joining-soon)
        // - probation OFF → revert status to 'active'  (if currently probation)
        if ($request->has('probation_required')) {
            $currentStatus = $employee->status ?? 'active';
            if ((bool) $request->probation_required) {
                if (in_array($currentStatus, ['active', 'joining-soon'])) {
                    $employee->status = 'probation';
                }
            } else {
                if ($currentStatus === 'probation') {
                    $employee->status = 'active';
                }
            }
        }
        // Explicit status change (e.g. from the "Change employee status" modal on the
        // profile page). Takes precedence over the probation auto-status logic above,
        // since it reflects a direct admin action rather than a side effect.
        if ($request->has('status')) {
            $request->validate([
                'status' => 'in:active,probation,on-leave,joining-soon,terminated',
            ]);
            $employee->status = $request->status;
        }
        // Division / sub-division (e.g. from the Role information modal on the
        // profile page). A sub-division must belong to the chosen division —
        // if it doesn't (or the division was cleared), drop it rather than
        // leaving a mismatched/orphaned reference.
        if ($request->has('division_id')) {
            $employee->division_id = $request->division_id ?: null;
        }
        if ($request->has('sub_division_id')) {
            $subDivisionId = $request->sub_division_id ?: null;
            if ($subDivisionId && $employee->division_id) {
                $valid = \App\Models\SubDivision::where('id', $subDivisionId)
                    ->where('division_id', $employee->division_id)
                    ->exists();
                $subDivisionId = $valid ? $subDivisionId : null;
            } elseif (!$employee->division_id) {
                $subDivisionId = null;
            }
            $employee->sub_division_id = $subDivisionId;
        }
        if ($request->has('position_id')) {
            $positionId = $request->position_id ?: null;
            if ($positionId && $employee->division_id) {
                $position = \App\Models\Position::where('id', $positionId)
                    ->where('division_id', $employee->division_id)
                    ->first();
                // A position tied to a specific sub-division must also match
                // the employee's chosen sub-division; a division-level
                // position (sub_division_id null) is valid regardless.
                if ($position && $position->sub_division_id && $position->sub_division_id != $employee->sub_division_id) {
                    $position = null;
                }
                $positionId = $position ? $position->id : null;
            } elseif (!$employee->division_id) {
                $positionId = null;
            }
            $employee->position_id = $positionId;
        }
        // "Reports to" — either a real Employee (manager_id) or a Director/
        // exec with no Employee record of their own (manager_external_id),
        // never both. Reject setting yourself as your own manager, and
        // reject any manager_id assignment that would create a cycle (the
        // chosen manager already reports, directly or indirectly, to this
        // employee) by walking manager_id upward from the proposed manager.
        if ($request->has('manager_id') || $request->has('manager_external_id')) {
            $managerId = $request->has('manager_id') ? ($request->manager_id ?: null) : $employee->manager_id;
            $managerExternalId = $request->has('manager_external_id') ? ($request->manager_external_id ?: null) : $employee->manager_external_id;

            if ($managerId && (int) $managerId === $employee->id) {
                $managerId = null; // can't be your own manager
            } elseif ($managerId && !\App\Models\Employee::where('id', $managerId)->exists()) {
                $managerId = null;
            } elseif ($managerId) {
                $seenIds = [$employee->id];
                $walker = \App\Models\Employee::find($managerId);
                $wouldCycle = false;
                while ($walker) {
                    if (in_array($walker->id, $seenIds, true)) {
                        $wouldCycle = true;
                        break;
                    }
                    $seenIds[] = $walker->id;
                    $walker = $walker->manager;
                }
                if ($wouldCycle) {
                    $managerId = null;
                }
            }

            if ($managerExternalId && !\App\Models\ManagerExternal::where('id', $managerExternalId)->exists()) {
                $managerExternalId = null;
            }

            // Mutually exclusive: whichever one this request explicitly set
            // wins and clears the other.
            if ($request->has('manager_id') && $managerId) {
                $managerExternalId = null;
            } elseif ($request->has('manager_external_id') && $managerExternalId) {
                $managerId = null;
            }

            $employee->manager_id = $managerId;
            $employee->manager_external_id = $managerExternalId;
        }
        if ($request->has('notice_period')) {
            $employee->notice_period = $request->notice_period ?: null;
        }
        if ($request->has('start_date')) {
            $employee->start_date = $request->start_date;
        }

        // Fields that now live in employee_payroll_details table
        $payrollFields = [
            'salary', 'pay_rate', 'pay_frequency', 'payroll_no',
            'tfn', 'bank_acc_name', 'bank_acc_no', 'bank_bsb', 'bank_name', 'bank_branch',
            'super_fund_name', 'super_fund_abn', 'super_member_no', 'super_usi',
            'bpjs_ketenagakerjaan_no', 'bpjs_ketenagakerjaan_start', 'bpjs_ketenagakerjaan_active',
            'bpjs_kesehatan_no', 'bpjs_kesehatan_class', 'bpjs_kesehatan_dependants',
            'bpjs_kesehatan_start', 'bpjs_kesehatan_active',
            'work_country', 'police_check_conducted',
        ];

        // Placeholder values from dropdowns that should be treated as null
        $placeholderValues = [
            'Select rate', 'Select frequency', 'Select reason',
            'Country of issue', 'Select jurisdiction',
        ];

        // Numeric fields where 0 is a valid value (should NOT be treated as null)
        // NOTE: pay_rate is intentionally NOT here — it stores a text label
        // (e.g. "Hour"/"Day"/"Per hour") from a dropdown, not a numeric value.
        $numericFields = ['salary', 'bpjs_kesehatan_dependants', 'bpjs_kesehatan_class', 'police_check_conducted'];

        $payrollPayload = [];
        foreach ($payrollFields as $field) {
            if ($request->has($field)) {
                $val = $request->get($field);
                if (in_array($field, $numericFields)) {
                    // For numeric fields: keep 0 as valid, only null out empty strings
                    $payrollPayload[$field] = ($val !== null && $val !== '') ? $val : null;
                } elseif ($val === null || $val === '' || in_array((string)$val, $placeholderValues)) {
                    $payrollPayload[$field] = null;
                } else {
                    $payrollPayload[$field] = $val;
                }
            }
        }

        if (!empty($payrollPayload)) {
            \App\Models\EmployeePayrollDetail::updateOrCreate(
                ['employee_id' => $employee->id],
                $payrollPayload
            );
        }

        // Save personal fields directly to employees table
        $personalFields = [
            'middle_name', 'personal_email',
            'home_phone', 'work_phone', 'work_extension',
            'address_1', 'address_2', 'address_3',
            'city', 'territory', 'postcode', 'country',
            'blood_type', 'allergies', 'medical_conditions', 'medical_notes',
            'visa_type', 'visa_expiry',
        ];

        $personalPayload = [];
        foreach ($personalFields as $field) {
            if ($request->has($field)) {
                $val = $request->get($field);
                $personalPayload[$field] = ($val !== null && $val !== '') ? $val : null;
            }
        }

        if (!empty($personalPayload)) {
            $employee->update($personalPayload);
        }

        // Note: visa's required-document status is now computed dynamically
        // from the employee_documents record (see requiredDocTypesFor()) —
        // no separate folder needs to be created here anymore.

        // Store remaining fields in extra_details JSON (legacy — will be emptied over time)
        $extraDetails = $employee->extra_details ?? [];

        $extraFields = ['award_notes', 'classification_notes'];

        foreach ($extraFields as $field) {
            if ($request->has($field)) {
                $extraDetails[$field] = $request->get($field);
            }
        }

        $employee->extra_details = $extraDetails;
        $employee->save();

        // Save employment details to employee_employment_details table
        $employmentFields = [
            'place_of_work', 'work_country', 'jurisdiction', 'employee_type',
            'working_pattern', 'leave_unit', 'accrual_rate', 'effective_from', 'salary_reason',
            'contracted_hours', 'contracted_minutes', 'contracted_days',
            'average_hours', 'average_minutes', 'annual_leave_hours', 'annual_leave_minutes',
        ];

        $employmentPayload = [];
        foreach ($employmentFields as $field) {
            if ($request->has($field)) {
                $val = $request->get($field);
                $employmentPayload[$field] = ($val !== null && $val !== '') ? $val : null;
            }
        }

        // Save working_schedule JSON (array of per-day schedule objects)
        if ($request->has('working_schedule')) {
            $schedule = $request->get('working_schedule');
            // Accept either a JSON string or already-decoded array
            if (is_string($schedule)) {
                $decoded = json_decode($schedule, true);
                $employmentPayload['working_schedule'] = json_last_error() === JSON_ERROR_NONE ? json_encode($decoded) : null;
            } elseif (is_array($schedule)) {
                $employmentPayload['working_schedule'] = json_encode($schedule);
            }
        }

        if (!empty($employmentPayload)) {
            \App\Models\EmployeeEmploymentDetail::updateOrCreate(
                ['employee_id' => $employee->id],
                $employmentPayload
            );
        }

        // Save passport to employee_documents table
        if ($request->hasAny(['passport_no', 'passport_country', 'passport_expiry'])) {
            $passportNo      = $request->get('passport_no');
            $passportCountry = $request->get('passport_country');
            $passportExpiry  = $request->get('passport_expiry');
            // Only save if at least one field has a real value
            if ($passportNo || $passportCountry) {
                \App\Models\EmployeeDocument::updateOrCreate(
                    ['employee_id' => $employee->id, 'type' => \App\Models\EmployeeDocument::TYPE_PASSPORT],
                    [
                        'document_no' => $passportNo ?: null,
                        'country'     => ($passportCountry && $passportCountry !== 'Country of issue') ? $passportCountry : null,
                        'expiry_date' => ($passportExpiry && trim($passportExpiry)) ? $passportExpiry : null,
                    ]
                );
            }
        }

        // Save driving licence to employee_documents table
        if ($request->hasAny(['licence_no', 'licence_country', 'licence_class', 'licence_expiry'])) {
            $licenceNo      = $request->get('licence_no');
            $licenceCountry = $request->get('licence_country');
            $licenceClass   = $request->get('licence_class');
            $licenceExpiry  = $request->get('licence_expiry');
            if ($licenceNo || $licenceCountry) {
                \App\Models\EmployeeDocument::updateOrCreate(
                    ['employee_id' => $employee->id, 'type' => \App\Models\EmployeeDocument::TYPE_DRIVING_LICENCE],
                    [
                        'document_no' => $licenceNo ?: null,
                        'country'     => ($licenceCountry && $licenceCountry !== 'Country of issue') ? $licenceCountry : null,
                        'class'       => $licenceClass ?: null,
                        'expiry_date' => ($licenceExpiry && trim($licenceExpiry)) ? $licenceExpiry : null,
                    ]
                );
            }
        }

        // Save visa to employee_documents table
        if ($request->hasAny(['visa_no', 'visa_expiry'])) {
            $visaNo     = $request->get('visa_no');
            $visaExpiry = $request->get('visa_expiry');
            if ($visaNo) {
                \App\Models\EmployeeDocument::updateOrCreate(
                    ['employee_id' => $employee->id, 'type' => \App\Models\EmployeeDocument::TYPE_VISA],
                    [
                        'document_no' => $visaNo,
                        'expiry_date' => ($visaExpiry && trim($visaExpiry)) ? $visaExpiry : null,
                    ]
                );
            }
        }

        // Save emergency contacts array sent from add-employee wizard
        if ($request->has('emergency_contacts')) {
            $contacts = $request->get('emergency_contacts');
            if (is_array($contacts) && count($contacts) > 0) {
                // Delete existing contacts then recreate (wizard sends full list)
                \App\Models\EmployeeEmergencyContact::where('employee_id', $employee->id)->delete();
                foreach ($contacts as $i => $c) {
                    if (empty($c['first_name'])) continue;
                    \App\Models\EmployeeEmergencyContact::create([
                        'employee_id'  => $employee->id,
                        'first_name'   => $c['first_name'] ?? null,
                        'last_name'    => $c['last_name'] ?? null,
                        'mobile_phone' => $c['mobile_phone'] ?? $c['mobile'] ?? null,
                        'home_phone'   => $c['home_phone'] ?? null,
                        'work_phone'   => $c['work_phone'] ?? null,
                        'address_1'    => $c['address_1'] ?? $c['addr1'] ?? null,
                        'address_2'    => $c['address_2'] ?? $c['addr2'] ?? null,
                        'address_3'    => $c['address_3'] ?? $c['addr3'] ?? null,
                        'city'         => $c['city'] ?? null,
                        'territory'    => $c['territory'] ?? null,
                        'postcode'     => $c['postcode'] ?? null,
                        'country'      => $c['country'] ?? null,
                        'relationship' => $c['relationship'] ?? null,
                        'is_primary'   => $i === 0 ? 1 : ($c['is_primary'] ?? 0),
                    ]);
                }
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Employee details updated successfully.',
            'employee' => $employee
        ]);
    }

    /**
     * Delete an absence record.
     * DELETE /admin/linkers-hub/absences/{absenceId}
     */
    public function destroyAbsence($absenceId)
    {
        $absence = \App\Models\EmployeeAbsence::findOrFail($absenceId);
        $absence->delete();
        return back()->with('success', 'Absence removed.');
    }

    public function destroyEmployee($id)
    {
        $employee = \App\Models\Employee::findOrFail($id);
        $employee->delete();

        return response()->json([
            'success' => true,
            'message' => 'Employee deleted successfully.'
        ]);
    }

    /**
     * Store a new absence for an employee.
     * POST /admin/linkers-hub/employees/{id}/absences
     */
    public function storeAbsence(Request $request, $id)
    {
        $employee = \App\Models\Employee::findOrFail($id);

        $request->validate([
            'type'       => 'required|in:annual,personal,lateness,other',
            'start_date' => 'required|date',
            'end_date'   => 'nullable|date|after_or_equal:start_date',
        ]);

        $type = $request->type;

        $data = [
            'employee_id'  => $employee->id,
            'type'         => $type,
            'start_date'   => $request->start_date,
            'end_date'     => $request->end_date ?: null,
            'ongoing'      => $request->boolean('ongoing'),
            'company_paid' => $request->boolean('company_paid'),
            'evidenced'    => $request->boolean('evidenced'),
            'reason'       => $request->reason ?: null,
            'late_hours'   => $type === 'lateness' ? (int) $request->late_hours : null,
            'late_minutes' => $type === 'lateness' ? (int) $request->late_minutes : null,
            'notes'        => $request->notes ?: null,
            'created_by'   => auth()->id(),
        ];

        \App\Models\EmployeeAbsence::create($data);

        return response()->json([
            'success' => true,
            'message' => ucfirst($type) . ' absence recorded successfully.',
        ]);
    }

    public function sendRegistrationEmail(Request $request)
    {
        $employeeIds = $request->input('employee_ids', []);
        if (empty($employeeIds)) {
            return response()->json([
                'success' => false,
                'message' => 'No employees selected.'
            ], 422);
        }

        $sentNames = [];
        $failedNames = [];

        foreach ($employeeIds as $id) {
            $employee = \App\Models\Employee::find($id);
            if ($employee && $employee->email) {
                try {
                    $fullName = $employee->full_name;
                    $firstName = ucwords(strtolower(trim($employee->first_name)));
                    
                    // Generate activation token and expiry (48 hours)
                    $token = \Illuminate\Support\Str::random(40);
                    $extraDetails = $employee->extra_details ?? [];
                    $extraDetails['activation_token'] = $token;
                    $extraDetails['activation_expires_at'] = now()->addDays(2)->toIso8601String();
                    $employee->extra_details = $extraDetails;
                    $employee->save();

                    $activationUrl = route('admin.register.activate', ['token' => $token]);

                    $htmlContent = "
                    <!DOCTYPE html>
                    <html>
                    <head>
                        <meta charset='utf-8'>
                        <meta name='viewport' content='width=device-width, initial-scale=1.0'>
                        <title>Welcome to Staff Link</title>
                    </head>
                    <body style=\"margin: 0; padding: 0; background-color: #f4f6f8; font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; -webkit-font-smoothing: antialiased;\">
                        <table border='0' cellpadding='0' cellspacing='0' width='100%' style='background-color: #f4f6f8; padding: 40px 10px;'>
                            <tr>
                                <td align='center'>
                                    <table border='0' cellpadding='0' cellspacing='0' width='100%' style='max-width: 600px; background-color: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 10px rgba(0,0,0,0.05); border: 1px solid #e2e8f0;'>
                                        <!-- Header -->
                                        <tr>
                                            <td align='center' style='background-color: #1b4332; padding: 30px 20px;'>
                                                <h1 style='color: #ffffff; margin: 0; font-size: 28px; font-weight: 700; letter-spacing: -0.5px;'>Staff Link</h1>
                                            </td>
                                        </tr>
                                        <!-- Body -->
                                        <tr>
                                            <td style='padding: 40px 30px; color: #333333; line-height: 1.6;'>
                                                <h2 style='margin-top: 0; color: #1b4332; font-size: 22px; font-weight: 600;'>Hi {$firstName},</h2>
                                                <p style='font-size: 16px; color: #4a5568; margin-bottom: 20px;'>Great news! The HR team has added you to our <strong>Staff Link</strong> system — an internal HR platform for managing your personal information, contracts, and employment administration.</p>
                                                <p style='font-size: 16px; color: #4a5568; margin-bottom: 30px;'>To get started, please set up your password and activate your account by clicking the button below:</p>
                                                
                                                <!-- CTA Button -->
                                                <table border='0' cellpadding='0' cellspacing='0' width='100%'>
                                                    <tr>
                                                        <td align='center' style='padding-bottom: 30px;'>
                                                            <a href='{$activationUrl}' target='_blank' style='display: inline-block; background-color: #2e7d5e; color: #ffffff; text-decoration: none; padding: 14px 30px; font-size: 16px; font-weight: 600; border-radius: 8px; box-shadow: 0 4px 6px rgba(46, 125, 94, 0.2);'>Get Started</a>
                                                        </td>
                                                    </tr>
                                                </table>
                                                
                                                <p style='font-size: 14px; color: #718096; margin-top: 20px; border-top: 1px solid #edf2f7; padding-top: 20px;'>Or copy and paste this URL into your browser:<br>
                                                <a href='{$activationUrl}' style='color: #2e7d5e; word-break: break-all;'>{$activationUrl}</a></p>
                                            </td>
                                        </tr>
                                        <!-- Footer -->
                                        <tr>
                                            <td style='background-color: #fafbfc; padding: 30px; border-top: 1px solid #edf2f7; text-align: center;'>
                                                <p style='margin: 0; font-size: 14px; color: #718096;'>If you have any questions, please contact our HR team.</p>
                                                <p style='margin: 10px 0 0 0; font-size: 12px; color: #a0aec0;'>— Staff Link HR Team</p>
                                                <p style='margin: 10px 0 0 0; font-size: 12px; color: #cbd5e0;'>&copy; " . date('Y') . " Staff Link. All rights reserved.</p>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        </table>
                    </body>
                    </html>";

                    \Illuminate\Support\Facades\Mail::send([], [], function ($message) use ($employee, $fullName, $htmlContent) {
                        $message->to($employee->email, $fullName)
                                ->from(config('mail.from.address', 'bookings@stafflink.pro'), 'StaffLink Team')
                                ->subject('Your StaffLink Account is Ready')
                                ->html($htmlContent);
                    });

                    $sentNames[] = $fullName;
                } catch (\Exception $e) {
                    \Illuminate\Support\Facades\Log::error("Failed to send registration email to employee {$id}: " . $e->getMessage());
                    $failedNames[] = $employee->full_name;
                }
            } else if ($employee) {
                $failedNames[] = $employee->full_name . ' (No email address)';
            }
        }

        if (count($sentNames) > 0 && count($failedNames) === 0) {
            return response()->json([
                'success' => true,
                'message' => 'Registration email sent to ' . implode(', ', $sentNames) . ' successfully!'
            ]);
        } else if (count($sentNames) > 0 && count($failedNames) > 0) {
            return response()->json([
                'success' => true,
                'message' => 'Registration email sent to ' . implode(', ', $sentNames) . ', but failed for ' . implode(', ', $failedNames) . '. Please check your mail/SMTP settings in .env.'
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Failed to send registration email for ' . implode(', ', $failedNames) . '. Please check your mail/SMTP settings in .env.'
            ], 500);
        }
    }


    // ================================================================
    // TEAM (DIVISION) CRUD
    // ================================================================

    /**
     * Create a new team (Division) and assign selected employees.
     */
    public function storeTeam(Request $request)
    {
        $name = trim($request->input('name', ''));
        if (!$name) {
            return response()->json(['success' => false, 'message' => 'Team name is required.'], 422);
        }

        // Prevent duplicate team names (the divisions table has a unique name).
        // Return a friendly message instead of a raw SQL error.
        $existing = \App\Models\Division::where('name', $name)->first();
        if ($existing) {
            return response()->json([
                'success' => false,
                'message' => 'A team named "' . $name . '" already exists. Please edit the existing team instead of creating a new one.',
            ], 422);
        }

        // Create the division
        $division = \App\Models\Division::create([
            'name' => $name,
            'is_active' => true,
        ]);

        // Assign selected employees to this division
        $employeeIds = $request->input('employee_ids', []);
        if (!empty($employeeIds)) {
            \App\Models\Employee::whereIn('id', $employeeIds)->update(['division_id' => $division->id]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Team "' . $name . '" created successfully.',
            'team' => [
                'id' => $division->id,
                'name' => $division->name,
                'memberCount' => count($employeeIds),
            ]
        ]);
    }

    /**
     * Update an existing team (Division) name and employee assignments.
     */
    public function updateTeam(Request $request, $id)
    {
        $division = \App\Models\Division::findOrFail($id);

        $name = trim($request->input('name', ''));
        if ($name) {
            $division->name = $name;
            $division->save();
        }

        // Reassign employees: first, remove all employees from this division
        \App\Models\Employee::where('division_id', $division->id)->update(['division_id' => null]);

        // Then assign the selected employees
        $employeeIds = $request->input('employee_ids', []);
        if (!empty($employeeIds)) {
            \App\Models\Employee::whereIn('id', $employeeIds)->update(['division_id' => $division->id]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Team "' . $division->name . '" updated successfully.',
            'team' => [
                'id' => $division->id,
                'name' => $division->name,
                'memberCount' => count($employeeIds),
            ]
        ]);
    }

    /**
     * Delete a team (Division) and unassign its employees.
     */
    public function deleteTeam($id)
    {
        $division = \App\Models\Division::findOrFail($id);
        $teamName = $division->name;

        // Unassign employees from this division
        \App\Models\Employee::where('division_id', $division->id)->update(['division_id' => null]);

        $division->delete();

        return response()->json([
            'success' => true,
            'message' => 'Team "' . $teamName . '" deleted successfully.'
        ]);
    }

    /**
     * Add a "Reports to" option for someone with no Employee record of their
     * own (e.g. a Director) — see ManagerExternal for why this is a separate,
     * deliberately minimal table rather than a stripped-down Employee row.
     */
    public function storeManagerExternal(Request $request)
    {
        $data = $request->validate([
            'name'  => ['required', 'string', 'max:150'],
            'title' => ['nullable', 'string', 'max:150'],
        ]);

        $manager = \App\Models\ManagerExternal::create($data);

        return response()->json([
            'success' => true,
            'manager' => ['id' => $manager->id, 'name' => $manager->name, 'title' => $manager->title],
        ]);
    }

    /**
     * Validate an employee name against spam, profanity, and garbage patterns.
     */
    private function validateEmployeeName(string $name, string $field): ?string
    {
        // Must be at least 2 real alphabetic characters
        $lettersOnly = preg_replace('/[^a-zA-Z]/', '', $name);
        if (strlen($lettersOnly) < 2) {
            return "{$field} must contain at least 2 letters.";
        }

        // Max 50 characters
        if (strlen($name) > 50) {
            return "{$field} is too long (max 50 characters).";
        }

        // Only allow letters, spaces, hyphens, apostrophes, dots
        if (!preg_match('/^[a-zA-ZÀ-ÿĀ-žА-яÑñ\s\'\-\.]+$/u', $name)) {
            return "{$field} contains invalid characters. Only letters, spaces, hyphens, and apostrophes are allowed.";
        }

        // Block repeated characters (3+ same char in a row)
        if (preg_match('/(.)\1{2,}/', strtolower($name))) {
            return "{$field} contains repeated characters and does not appear to be a valid name.";
        }

        $lower = strtolower($name);
        $cleanedLower = preg_replace('/[\s\.\-\']+/', '', $lower);

        // Block keyboard spam patterns
        $keyboardSpam = [
            'qwert', 'asdf', 'zxcv', 'qazwsx', 'poiuy', 'lkjhg', 'mnbvc',
            'abcdef', 'zyxwvu',
        ];
        foreach ($keyboardSpam as $pattern) {
            if (strpos($lower, $pattern) !== false) {
                return "{$field} does not appear to be a valid name.";
            }
        }

        // ── Comprehensive nonsense / placeholder / gibberish blocklist ──
        $nonsenseWords = [
            // Test / placeholder
            'test', 'testing', 'tester', 'testtest', 'testy',
            'blah', 'blabla', 'bla', 'blahblah', 'bleh', 'bluh',
            'foo', 'foobar', 'fubar',
            'lorem', 'ipsum', 'dolor', 'amet',
            'null', 'undefined', 'none', 'nil', 'void', 'empty', 'blank',
            'admin', 'user', 'root', 'guest', 'system', 'login', 'password',
            'sample', 'example', 'demo', 'dummy', 'fake', 'temp', 'tmp',
            'unknown', 'nobody', 'noname', 'nope', 'anon', 'anonymous',
            // Alphabet / keyboard
            'aaa', 'bbb', 'ccc', 'ddd', 'eee', 'fff', 'ggg', 'hhh', 'iii',
            'jjj', 'kkk', 'lll', 'mmm', 'nnn', 'ooo', 'ppp', 'qqq', 'rrr',
            'sss', 'ttt', 'uuu', 'vvv', 'www', 'xxx', 'yyy', 'zzz',
            'abc', 'xyz', 'qwerty', 'abcd', 'abcde',
            // Greetings / exclamations (not names)
            'hello', 'hey', 'bye', 'sup', 'yo', 'yep', 'yup', 'yeah', 'yay',
            'nah', 'naw', 'nuh', 'ugh', 'meh', 'hmm', 'huh', 'duh', 'pfft',
            'ooh', 'aah', 'eww', 'ew', 'wow', 'ohh', 'ahh', 'umm', 'err',
            'shh', 'tsk', 'psst', 'oof', 'yikes', 'oops', 'ouch',
            // Childish / toilet / silly words
            'poo', 'poop', 'poopy', 'poopoo', 'poopie',
            'pee', 'peepee', 'peep', 'pipi',
            'boo', 'booboo', 'boob', 'boobs', 'boobie',
            'butt', 'butts', 'bum', 'bumm', 'bummy',
            'fart', 'farty', 'toot', 'toots', 'tootie',
            'wee', 'weewee', 'weeee',
            'doo', 'doody', 'doodoo', 'doofus',
            'goo', 'gooey', 'goop', 'goober',
            'loo', 'loopy', 'loser',
            'barf', 'barfy', 'yuck', 'yucky', 'icky', 'gross',
            'snot', 'snotty', 'booger', 'boogers',
            'dork', 'dorky', 'nerd', 'nerdy', 'geek', 'geeky',
            'dumb', 'dumbo', 'dumdum', 'stupid', 'idiot', 'moron', 'fool',
            'ugly', 'fatty', 'skinny', 'stinky', 'smelly', 'stink',
            'lame', 'lameo', 'loser', 'sucker', 'noob', 'newbie',
            'wimp', 'wimpy', 'sissy', 'pansy', 'wuss', 'wussy',
            'nutty', 'nuts', 'bonkers', 'crazy', 'wacko', 'weirdo',
            'turd', 'turds',
            // Animal sounds
            'moo', 'baa', 'meow', 'woof', 'bark', 'quack', 'oink',
            'neigh', 'cluck', 'ribbit', 'hiss', 'roar', 'buzz',
            'mew', 'purr', 'caw', 'chirp', 'tweet', 'squawk',
            // Random sounds / syllables
            'la', 'da', 'na', 'ba', 'ga', 'ta', 'ka', 'pa', 'fa', 'za',
            'lala', 'dada', 'nana', 'baba', 'gaga', 'tata', 'kaka', 'papa',
            'mama', 'bibi', 'bobo', 'bubu', 'didi', 'dudu', 'fifi',
            'gigi', 'jojo', 'kiki', 'koko', 'lili', 'lolo', 'lulu',
            'mimi', 'nini', 'nono', 'pipi', 'pupu', 'riri', 'sisi',
            'titi', 'toto', 'tutu', 'zuzu', 'wawa', 'wewe',
            'haha', 'hehe', 'hihi', 'hoho', 'huhu',
            'blip', 'blob', 'blop', 'blub', 'bonk', 'boop', 'beep',
            'ding', 'dong', 'ping', 'pong', 'bing', 'bong', 'bam', 'boom',
            'zap', 'zip', 'zop', 'zoop', 'zoom', 'zing',
            'yada', 'yadda',
            // Internet slang
            'lol', 'lmao', 'rofl', 'omg', 'wtf', 'brb', 'idk', 'smh',
            'fml', 'yolo', 'swag', 'derp', 'herp', 'noob', 'kek',
            'bruh', 'skibidi', 'rizz', 'gyatt', 'sigma', 'chad', 'karen',
            // Generic / obviously not names
            'thing', 'stuff', 'nothing', 'something', 'whatever', 'whoever',
            'person', 'people', 'human', 'man', 'woman', 'boy', 'girl',
            'name', 'first', 'last', 'employee', 'worker', 'staff',
            'yes', 'no', 'ok', 'okay', 'maybe', 'sure', 'right', 'wrong',
            'good', 'bad', 'nice', 'cool', 'hot', 'cold',
            'big', 'small', 'tall', 'short', 'fat', 'thin',
            'red', 'blue', 'green', 'black', 'white', 'pink', 'purple',
            'one', 'two', 'three', 'four', 'five', 'six', 'seven',
            'dog', 'cat', 'pig', 'cow', 'rat', 'bat', 'bug', 'ant', 'fly',
            'eat', 'run', 'sit', 'hit', 'die', 'cry', 'lie',
            // Common English words that are NOT names
            'joke', 'joker', 'friend', 'enemy', 'boss', 'chief', 'king', 'queen',
            'prince', 'lord', 'lady', 'duke', 'master', 'slave', 'hero', 'villain',
            'angel', 'devil', 'ghost', 'zombie', 'alien', 'robot', 'ninja', 'pirate',
            'happy', 'sad', 'angry', 'mad', 'glad', 'scared', 'brave', 'lazy',
            'funny', 'silly', 'smart', 'clever', 'strong', 'weak', 'fast', 'slow',
            'loud', 'quiet', 'soft', 'hard', 'easy', 'tough', 'rough', 'smooth',
            'dark', 'light', 'bright', 'dim', 'rich', 'poor', 'cheap', 'free',
            'real', 'true', 'false', 'alive', 'dead', 'lost', 'found', 'broken',
            'love', 'hate', 'kiss', 'hug', 'kill', 'fight', 'help', 'save',
            'work', 'play', 'stop', 'go', 'come', 'stay', 'leave', 'wait',
            'walk', 'jump', 'swim', 'sing', 'dance', 'sleep', 'wake', 'dream',
            'look', 'see', 'hear', 'feel', 'think', 'know', 'want', 'need',
            'give', 'take', 'make', 'break', 'open', 'close', 'push', 'pull',
            'food', 'water', 'milk', 'beer', 'wine', 'cake', 'bread', 'rice',
            'fish', 'meat', 'egg', 'salt', 'sugar', 'candy', 'fruit',
            'house', 'home', 'room', 'door', 'wall', 'floor', 'roof', 'window',
            'table', 'chair', 'bed', 'desk', 'lamp', 'phone', 'book', 'card',
            'money', 'cash', 'gold', 'silver', 'iron', 'steel', 'wood', 'stone',
            'fire', 'rain', 'snow', 'wind', 'storm', 'cloud', 'star', 'moon',
            'tree', 'leaf', 'seed', 'dirt', 'sand', 'rock', 'mud', 'dust',
            'hand', 'foot', 'head', 'face', 'nose', 'eye', 'ear', 'mouth',
            'hair', 'skin', 'bone', 'blood', 'heart', 'brain', 'back', 'neck',
            'baby', 'child', 'kid', 'teen', 'adult', 'old', 'young', 'new',
            'game', 'ball', 'team', 'club', 'group', 'band', 'gang', 'crew',
            'car', 'bus', 'boat', 'ship', 'bike', 'road', 'path', 'bridge',
            'city', 'town', 'land', 'farm', 'park', 'lake', 'hill', 'river',
            'world', 'earth', 'sky', 'space', 'time', 'life', 'death', 'soul',
            'god', 'king', 'war', 'peace', 'pain', 'joy', 'fear', 'hope',
            'luck', 'fate', 'power', 'magic', 'spell', 'trick', 'trap', 'plan',
            'rule', 'law', 'crime', 'jail', 'gun', 'bomb', 'drug', 'poison',
            'cup', 'hat', 'bag', 'box', 'key', 'ring', 'bell', 'flag',
            'song', 'note', 'word', 'code', 'sign', 'mark', 'link', 'page',
            'smile', 'laugh', 'grin', 'wink', 'clap', 'cheer', 'shout', 'scream',
            'tiger', 'lion', 'bear', 'wolf', 'fox', 'deer', 'bird', 'fish',
            'snake', 'frog', 'duck', 'goat', 'horse', 'mouse', 'rabbit', 'monkey',
            'doctor', 'nurse', 'teacher', 'driver', 'farmer', 'soldier', 'guard',
            'police', 'judge', 'lawyer', 'actor', 'singer', 'dancer', 'player',
            'captain', 'leader', 'winner', 'loser', 'killer', 'hunter', 'fighter',
            'brother', 'sister', 'mother', 'father', 'uncle', 'aunt', 'cousin',
            'husband', 'wife', 'child', 'daughter', 'son', 'grandma', 'grandpa',
            'super', 'mega', 'ultra', 'hyper', 'turbo', 'maxi', 'mini',
            'banana', 'apple', 'orange', 'mango', 'lemon', 'cherry', 'grape',
            'pizza', 'burger', 'taco', 'pasta', 'salad', 'sandwich', 'cookie',
            'chicken', 'turkey', 'bacon', 'steak', 'sushi', 'noodle', 'soup',
            'coffee', 'juice', 'soda', 'vodka', 'whiskey', 'cocktail',
            'morning', 'night', 'today', 'tomorrow', 'yesterday', 'always', 'never',
            'here', 'there', 'where', 'when', 'what', 'which', 'that', 'this',
            // Emotion / feeling words
            'upset', 'worried', 'nervous', 'anxious', 'stressed', 'depressed', 'lonely',
            'confused', 'bored', 'tired', 'sick', 'hurt', 'shy', 'proud', 'jealous',
            'excited', 'annoyed', 'frustrated', 'grumpy', 'moody', 'cranky', 'miserable',
            'cheerful', 'joyful', 'grateful', 'thankful', 'hopeful', 'peaceful', 'calm',
            'gentle', 'kind', 'mean', 'rude', 'polite', 'humble', 'greedy', 'selfish',
            'honest', 'loyal', 'guilty', 'innocent', 'sorry', 'ashamed', 'embarrassed',
            // More adjectives / descriptors
            'painful', 'beautiful', 'wonderful', 'horrible', 'terrible', 'amazing',
            'awesome', 'awful', 'lovely', 'pretty', 'handsome', 'gorgeous', 'cute',
            'perfect', 'special', 'normal', 'strange', 'weird', 'odd', 'bizarre',
            'boring', 'interesting', 'important', 'dangerous', 'careful', 'careless',
            'useless', 'useful', 'helpless', 'hopeless', 'worthless', 'pointless',
            'harmless', 'fearless', 'endless', 'restless', 'homeless', 'clueless',
            'powerful', 'colorful', 'graceful', 'hateful', 'spiteful', 'wasteful',
            'pleasant', 'unpleasant', 'foolish', 'childish', 'selfish', 'clownish',
            'friendly', 'unfriendly', 'cowardly', 'deadly', 'likely', 'unlikely',
            'famous', 'nervous', 'jealous', 'curious', 'furious', 'serious',
            'obvious', 'precious', 'gorgeous', 'dangerous', 'enormous', 'ridiculous',
            'fantastic', 'dramatic', 'pathetic', 'romantic', 'sarcastic', 'toxic',
            // Body conditions / medical / appearance
            'pimple', 'pimples', 'scar', 'scars', 'wound', 'wounds', 'bruise', 'rash',
            'blister', 'wart', 'mole', 'freckle', 'wrinkle', 'acne', 'eczema',
            'tumor', 'cancer', 'virus', 'germ', 'disease', 'illness', 'fever',
            'cough', 'sneeze', 'vomit', 'diarrhea', 'infection', 'swelling',
            'itch', 'itchy', 'scratch', 'scab', 'sore', 'bleed', 'bleeding',
            'blind', 'deaf', 'mute', 'lame', 'cripple', 'disabled',
            'sweat', 'sweaty', 'smear', 'stain', 'spill', 'drool', 'spit',
            // More random nouns/verbs people might try
            'carpet', 'curtain', 'pillow', 'blanket', 'towel', 'mirror', 'toilet',
            'shower', 'kitchen', 'garden', 'garage', 'office', 'school', 'church',
            'bottle', 'basket', 'bucket', 'pencil', 'eraser', 'paper', 'folder',
            'computer', 'laptop', 'tablet', 'screen', 'mouse', 'keyboard',
            'number', 'letter', 'email', 'message', 'picture', 'photo', 'video',
            'music', 'movie', 'series', 'channel', 'stream', 'upload', 'download',
            'weather', 'summer', 'winter', 'spring', 'autumn', 'season', 'holiday',
            'dinner', 'lunch', 'breakfast', 'snack', 'dessert', 'recipe', 'menu',
            'soccer', 'football', 'tennis', 'boxing', 'cricket', 'hockey', 'golf',
            'dollar', 'pound', 'euro', 'bitcoin', 'profit', 'salary', 'income',
        ];
        foreach ($nonsenseWords as $word) {
            if ($cleanedLower === $word) {
                return "{$field} does not appear to be a valid name.";
            }
        }

        // ── Profanity / swear words (multilingual, substring match) ──
        $profanity = [
            // English
            'fuck', 'shit', 'asshole', 'bitch', 'bastard', 'damn', 'dick', 'cock',
            'cunt', 'piss', 'bollocks', 'wanker', 'twat', 'slut', 'whore', 'nigger', 'nigga',
            'faggot', 'retard', 'crap', 'pussy', 'vagina', 'penis', 'anus', 'booty',
            'dildo', 'orgasm', 'horny', 'sexy', 'nude', 'naked', 'porn', 'hentai',
            'tits', 'titty', 'titties', 'scrotum', 'testicle', 'erection', 'ejaculate',
            // Indonesian / Bahasa
            'kontol', 'memek', 'jancok', 'jancuk', 'anjing', 'bangsat', 'babi', 'goblok',
            'tolol', 'bodoh', 'kampret', 'bajingan', 'keparat', 'setan', 'iblis', 'tai',
            'pepek', 'ngentot',
            // Spanish
            'puta', 'mierda', 'cabron', 'pendejo', 'chingada', 'coño', 'verga', 'joder',
            'maricon', 'polla', 'culo',
            // French
            'merde', 'putain', 'connard', 'salaud', 'enculer', 'bordel', 'foutre',
            // German
            'scheiße', 'scheisse', 'arschloch', 'wichser', 'fotze', 'hurensohn',
            // Portuguese
            'porra', 'caralho', 'foda', 'merda', 'buceta',
            // Italian
            'cazzo', 'stronzo', 'vaffanculo', 'minchia', 'puttana',
            // Chinese (pinyin)
            'tmd', 'nimabi',
            // Arabic (transliterated)
            'kuss', 'sharmouta',
        ];

        foreach ($profanity as $word) {
            if (stripos($cleanedLower, $word) !== false) {
                return "{$field} contains inappropriate language.";
            }
        }

        // Block names that are just dots, dashes, or spaces
        $stripped = str_replace(['.', '-', "'", ' '], '', $name);
        if (strlen($stripped) < 2) {
            return "{$field} must contain at least 2 actual letters.";
        }

        // Block single repeated word patterns (e.g. "la la la", "na na na")
        $words = preg_split('/\s+/', trim($lower));
        if (count($words) >= 2) {
            $unique = array_unique($words);
            if (count($unique) === 1 && strlen($words[0]) <= 4) {
                return "{$field} does not appear to be a valid name.";
            }
        }

        // ── Consonant-only or vowel-only check (gibberish like "brtxnm" or "aouei") ──
        if (strlen($cleanedLower) >= 4) {
            $vowels = preg_replace('/[^aeiou]/i', '', $cleanedLower);
            $consonants = preg_replace('/[aeiou]/i', '', $cleanedLower);
            // All consonants, no vowels (4+ chars)
            if (strlen($vowels) === 0) {
                return "{$field} does not appear to be a valid name.";
            }
            // All vowels, no consonants (4+ chars)
            if (strlen($consonants) === 0) {
                return "{$field} does not appear to be a valid name.";
            }
        }

        // ── Short name validation: if 2-3 letters, must be a known legitimate name ──
        if (strlen($cleanedLower) <= 3) {
            $legitimateShortNames = [
                // 2-letter names
                'al', 'an', 'bo', 'ed', 'em', 'ev', 'io', 'jo', 'ki', 'li', 'lu',
                'mo', 'mu', 'nu', 'oz', 'po', 'qi', 'ri', 'ru', 'ty', 'vi', 'wu',
                'xi', 'xu', 'yu', 'ze',
                // 3-letter names (common worldwide)
                'ada', 'adi', 'afi', 'aja', 'aki', 'alf', 'ali', 'ami', 'ana', 'ane',
                'ann', 'ari', 'asa', 'ava', 'ayu', 'bea', 'ben', 'bob', 'bud', 'cal',
                'cam', 'che', 'cho', 'col', 'dan', 'deb', 'dee', 'del', 'den', 'dev',
                'dex', 'dom', 'don', 'dot', 'eda', 'eka', 'eli', 'ema', 'emi', 'eri',
                'eva', 'eve', 'fay', 'fia', 'fin', 'flo', 'gab', 'gal', 'gay', 'gem',
                'gia', 'gil', 'gus', 'guy', 'hal', 'han', 'ida', 'ike', 'ina', 'ira',
                'isa', 'iva', 'ivy', 'jae', 'jan', 'jay', 'jen', 'jet', 'jia', 'jim',
                'joe', 'jon', 'joy', 'jun', 'kai', 'kam', 'kat', 'kay', 'ken', 'kim',
                'kit', 'koa', 'kye', 'lam', 'lan', 'lea', 'lee', 'len', 'leo', 'les',
                'lex', 'lia', 'lin', 'liu', 'liv', 'liz', 'lou', 'luc', 'luz', 'lyn',
                'mae', 'mai', 'mak', 'max', 'may', 'mel', 'mia', 'moe', 'mor', 'mya',
                'nan', 'nat', 'ned', 'nia', 'nik', 'noa', 'noe', 'ora', 'ori', 'oto',
                'ova', 'own', 'pam', 'pat', 'pax', 'peg', 'pen', 'pia', 'pip', 'pop',
                'rae', 'raj', 'ram', 'ran', 'ray', 'ren', 'rex', 'ria', 'rio', 'rob',
                'rod', 'ron', 'ros', 'roy', 'rui', 'sam', 'san', 'sel', 'sid', 'sim',
                'sol', 'sri', 'sue', 'sun', 'tam', 'ted', 'tim', 'tom', 'val', 'van',
                'vin', 'viv', 'wai', 'wan', 'wei', 'wen', 'wil', 'win', 'yam', 'yan',
                'yui', 'yun', 'zen', 'zoe', 'zul',
                // Indonesian short names
                'adi', 'aji', 'ayu', 'bui', 'cha', 'dea', 'eka', 'eko', 'eri', 'evi',
                'ika', 'ima', 'ina', 'ira', 'ita', 'lia', 'lim', 'nur', 'oka', 'oni',
                'pur', 'ria', 'rio', 'ris', 'riy', 'saf', 'sri', 'sui', 'tin', 'tri',
                'udi', 'umi', 'uni', 'uta', 'uun', 'wah', 'wan', 'yul', 'yun',
            ];
            if (!in_array($cleanedLower, $legitimateShortNames)) {
                return "{$field} does not appear to be a valid name.";
            }
        }

        return null; // Valid
    }
}
