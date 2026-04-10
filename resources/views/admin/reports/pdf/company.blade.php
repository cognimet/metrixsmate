<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Corporate Workforce Report — {{ $institution_name }}</title>
    @include('admin.reports.pdf._styles')
</head>
<body>
@php
    $c = fn($level) => match($level ?? 'average') {
        'exceptional'   => 'e',
        'high'          => 'h',
        'below-average' => 'b',
        'low'           => 'l',
        default         => 'a',
    };
    $total = max($completed_users, 1);
    $dist  = $perf_distribution;
    $distTotal = max(array_sum($dist), 1);
@endphp

{{-- ──────────────────── HEADER ──────────────────── --}}
<div class="report-header">
    <div class="brand">MetrixsMate</div>
    <h1>Corporate Workforce Intelligence Report</h1>
    <div class="institution">{{ $institution_name }}</div>
    <div class="badge badge-company">CORPORATE REPORT</div>
    <div class="meta">Generated on {{ $generatedDate }} &bull; {{ $completed_users }} of {{ $total_users }} employees assessed</div>
</div>

{{-- ──────────────────── EXECUTIVE SUMMARY ──────────────────── --}}
<div class="section">
    <div class="section-head">Executive Summary</div>
    <div class="section-sub">A strategic overview of your workforce's talent profile, capabilities, and growth potential</div>

    <div class="exec-grid">
        <div class="exec-cell">
            <div class="exec-label">Personality Index</div>
            <div class="exec-value v-blue">{{ $avg_scores['personality'] }}%</div>
        </div>
        <div class="exec-cell">
            <div class="exec-label">Career Alignment</div>
            <div class="exec-value v-purple">{{ $avg_scores['career'] }}%</div>
        </div>
        <div class="exec-cell">
            <div class="exec-label">Cognitive Ability</div>
            <div class="exec-value v-green">{{ $avg_scores['cognitive'] }}%</div>
        </div>
        <div class="exec-cell">
            <div class="exec-label">Overall Score</div>
            <div class="exec-value v-amber">{{ $avg_scores['overall'] }}%</div>
        </div>
    </div>

    <div class="two-col">
        <div class="col-l">
            <div class="info-box ib-green">
                <div class="ib-title">&#9733; Leadership Pipeline: {{ $leadership_pipeline }} Employees</div>
                <div class="ib-text">Employees with high leadership potential (≥70%) — ready for management roles, succession planning, and leadership development programs.</div>
            </div>
        </div>
        <div class="col-r">
            <div class="info-box ib-purple">
                <div class="ib-title">&#9881; Innovation Champions: {{ $innovation_potential }} Employees</div>
                <div class="ib-text">Employees with high innovation index (≥70%) — ideal for R&amp;D teams, product development, and strategic innovation initiatives.</div>
            </div>
        </div>
    </div>

    <div class="info-box ib-blue">
        <div class="ib-title">How to Read This Report</div>
        <div class="ib-text">This report aggregates workforce assessments across Personality (OCEAN model), Career Interests (RIASEC/Holland model), and Cognitive Abilities. Performance levels: Exceptional (80%+), High (65-79%), Average (45-64%), Below Average (30-44%), Low (&lt;30%). Use this data for role assignments, L&amp;D planning, succession management, and team composition optimization.</div>
    </div>
</div>

{{-- ──────────────────── PERFORMANCE DISTRIBUTION ──────────────────── --}}
<div class="section">
    <div class="section-head">Workforce Performance Distribution</div>
    <div class="dist-bar">
        @foreach(['exceptional' => 'ds-e', 'high' => 'ds-h', 'average' => 'ds-a', 'below-average' => 'ds-b', 'low' => 'ds-l'] as $level => $cls)
            @if(($dist[$level] ?? 0) > 0)
                <div class="dist-seg {{ $cls }}" style="width: {{ round(($dist[$level] / $distTotal) * 100) }}%"></div>
            @endif
        @endforeach
    </div>
    <div class="legend">
        <div class="legend-item"><span class="legend-dot ld-e"></span>Exceptional ({{ $dist['exceptional'] ?? 0 }})</div>
        <div class="legend-item"><span class="legend-dot ld-h"></span>High ({{ $dist['high'] ?? 0 }})</div>
        <div class="legend-item"><span class="legend-dot ld-a"></span>Average ({{ $dist['average'] ?? 0 }})</div>
        <div class="legend-item"><span class="legend-dot ld-b"></span>Below Avg ({{ $dist['below-average'] ?? 0 }})</div>
        <div class="legend-item"><span class="legend-dot ld-l"></span>Low ({{ $dist['low'] ?? 0 }})</div>
    </div>
</div>

{{-- ──────────────────── ROLE FITMENT ANALYSIS ──────────────────── --}}
<div class="section">
    <div class="section-head">Role Fitment Analysis</div>
    <div class="section-sub">How well your workforce matches key organizational roles — guides role assignments, internal mobility, and hiring gaps</div>

    @foreach($role_fitment as $rf)
        @php
            $lvl = $rf['avg'] >= 70 ? 'high' : ($rf['avg'] >= 50 ? 'average' : ($rf['avg'] >= 30 ? 'below-average' : 'low'));
            $k = $c($lvl);
        @endphp
        <div class="dc dc-{{ $k }}">
            <div class="dh">
                <div class="dname">{{ $rf['role'] }}</div>
                <div class="dright">
                    <span class="dscore sc-{{ $k }}">{{ $rf['avg'] }}%</span>
                </div>
            </div>
            <div class="pbar"><div class="pfill pf-{{ $k }}" style="width:{{ $rf['avg'] }}%"></div></div>
            <div class="dstats">Fit: {{ $rf['fit_count'] }} employees ({{ $rf['fit_pct'] }}%)</div>
        </div>
    @endforeach
</div>

{{-- ──────────────────── TEAM COMPOSITION ──────────────────── --}}
<div class="section">
    <div class="section-head">Team Composition — Personality Types</div>
    <div class="section-sub">Distribution of dominant personality/work styles across the organization — reveals team balance and blind spots</div>

    <table class="dtable">
        <tr><th>Work Style</th><th>Employees</th><th>% of Workforce</th></tr>
        @foreach($team_composition as $tc)
            <tr>
                <td><strong>{{ $tc['type'] }}</strong></td>
                <td>{{ $tc['count'] }}</td>
                <td>{{ $tc['pct'] }}%</td>
            </tr>
        @endforeach
    </table>

    @php
        $dominantType = $team_composition[0]['type'] ?? null;
        $dominantPct  = $team_composition[0]['pct'] ?? 0;
    @endphp
    @if($dominantPct > 40)
        <div class="info-box ib-amber">
            <div class="ib-title">&#9888; Team Imbalance Detected</div>
            <div class="ib-text"><strong>{{ $dominantType }}</strong> represents {{ $dominantPct }}% of your workforce. Consider diversifying team composition for better innovation and resilience. Over-concentration in one work style can lead to groupthink.</div>
        </div>
    @endif
</div>

{{-- ──────────────────── PERSONALITY PROFILE ──────────────────── --}}
<div class="section">
    <div class="section-head">Personality Profile — Workforce Average (OCEAN)</div>

    @foreach($ocean_agg as $d)
        @php $k = $c($d['level']); @endphp
        <div class="dc dc-{{ $k }}">
            <div class="dh">
                <div class="dname">{{ $d['name'] }}</div>
                <div class="dright">
                    <span class="dscore sc-{{ $k }}">{{ $d['avg'] }}%</span>
                    <span class="dbadge bd-{{ $k }}">{{ ucfirst($d['level']) }}</span>
                </div>
            </div>
            <div class="pbar"><div class="pfill pf-{{ $k }}" style="width:{{ $d['avg'] }}%"></div></div>
            <div class="dstats">Range: {{ $d['min'] }}% — {{ $d['max'] }}% &bull; Std Dev: {{ $d['std_dev'] }}</div>
        </div>
    @endforeach
</div>

{{-- ──────────────────── CAREER INTERESTS ──────────────────── --}}
<div class="section">
    <div class="section-head">Career Interest Profile — Workforce Average (RIASEC)</div>

    @foreach($riasec_agg as $d)
        @php $k = $c($d['level']); @endphp
        <div class="dc dc-{{ $k }}">
            <div class="dh">
                <div class="dname">{{ $d['name'] }}</div>
                <div class="dright">
                    <span class="dscore sc-{{ $k }}">{{ $d['avg'] }}%</span>
                </div>
            </div>
            <div class="pbar"><div class="pfill pf-{{ $k }}" style="width:{{ $d['avg'] }}%"></div></div>
            <div class="dstats">Range: {{ $d['min'] }}% — {{ $d['max'] }}%</div>
        </div>
    @endforeach
</div>

{{-- ──────────────────── COGNITIVE ABILITIES ──────────────────── --}}
<div class="section">
    <div class="section-head">Cognitive Abilities — Workforce Average</div>

    @foreach($cognitive_agg as $d)
        @php $k = $c($d['level']); @endphp
        <div class="dc dc-{{ $k }}">
            <div class="dh">
                <div class="dname">{{ $d['name'] }}</div>
                <div class="dright">
                    <span class="dscore sc-{{ $k }}">{{ $d['avg'] }}%</span>
                </div>
            </div>
            <div class="pbar"><div class="pfill pf-{{ $k }}" style="width:{{ $d['avg'] }}%"></div></div>
            <div class="dstats">Range: {{ $d['min'] }}% — {{ $d['max'] }}%</div>
        </div>
    @endforeach
</div>

{{-- ──────────────────── UPSKILLING PRIORITIES ──────────────────── --}}
@if(count($upskilling_priorities) > 0)
<div class="section">
    <div class="section-head">Upskilling Priorities</div>
    <div class="section-sub">Domains where the workforce scores below the target threshold (65%) — direct L&amp;D budgets here for maximum impact</div>

    <table class="dtable">
        <tr><th>Domain</th><th>Current Avg</th><th>Gap to Target</th><th>Priority Level</th></tr>
        @foreach($upskilling_priorities as $up)
            <tr>
                <td><strong>{{ $up['domain'] }}</strong></td>
                <td>{{ $up['avg'] }}%</td>
                <td>{{ $up['gap'] }}pts</td>
                <td>
                    @if($up['priority'] === 'Critical')
                        <span style="color:#dc2626;font-weight:700;">{{ $up['priority'] }}</span>
                    @elseif($up['priority'] === 'High')
                        <span style="color:#f97316;font-weight:700;">{{ $up['priority'] }}</span>
                    @else
                        <span style="color:#f59e0b;font-weight:700;">{{ $up['priority'] }}</span>
                    @endif
                </td>
            </tr>
        @endforeach
    </table>
</div>
@endif

{{-- ──────────────────── STRENGTHS & GROWTH ──────────────────── --}}
<div class="two-col">
    <div class="col-l">
        <div class="section">
            <div class="section-head" style="color:#059669;border-color:#a7f3d0;">&#9733; Organizational Strengths</div>
            @forelse($strengths as $s)
                <div class="info-box ib-green" style="margin-bottom:4px;">
                    <div class="ib-text"><strong>{{ $s['name'] }}</strong> — {{ $s['avg'] }}%</div>
                </div>
            @empty
                <div class="info-box ib-green"><div class="ib-text">No standout strengths identified yet.</div></div>
            @endforelse
        </div>
    </div>
    <div class="col-r">
        <div class="section">
            <div class="section-head" style="color:#dc2626;border-color:#fecaca;">&#9888; Development Areas</div>
            @forelse($weaknesses as $w)
                <div class="info-box ib-red" style="margin-bottom:4px;">
                    <div class="ib-text"><strong>{{ $w['name'] }}</strong> — {{ $w['avg'] }}%</div>
                </div>
            @empty
                <div class="info-box ib-green"><div class="ib-text">No critical weaknesses identified.</div></div>
            @endforelse
        </div>
    </div>
</div>

{{-- ──────────────────── TOP PERFORMERS ──────────────────── --}}
@if($top_performers->isNotEmpty())
<div class="section">
    <div class="section-head">Top Performers — High Potential</div>
    <div class="section-sub">Highest scoring employees — candidates for leadership roles, promotions, and key projects</div>
    <table class="dtable">
        <tr><th>#</th><th>Employee</th><th>Personality</th><th>Career</th><th>Cognitive</th><th>Overall</th></tr>
        @foreach($top_performers as $i => $tp)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ $tp['user']->name }}</td>
                <td>{{ round($tp['data']['overallScore']['personality']) }}%</td>
                <td>{{ round($tp['data']['overallScore']['career']) }}%</td>
                <td>{{ round($tp['data']['overallScore']['cognitive']) }}%</td>
                <td><strong>{{ $tp['overall'] }}%</strong></td>
            </tr>
        @endforeach
    </table>
</div>
@endif

{{-- ──────────────────── STRATEGIC RECOMMENDATIONS ──────────────────── --}}
<div class="section">
    <div class="section-head">Strategic Workforce Recommendations</div>

    {{-- Leadership --}}
    <div class="action-item action-{{ $leadership_pipeline > 0 ? 'low' : 'high' }}">
        <div class="action-label">LEADERSHIP DEVELOPMENT</div>
        @if($leadership_pipeline > 0)
            {{ $leadership_pipeline }} employees identified with high leadership potential. Create a fast-track leadership program with mentoring, cross-functional projects, and executive coaching.
        @else
            No employees currently meet the leadership potential threshold. Invest in leadership training programs and identify emerging managers for accelerated development.
        @endif
    </div>

    {{-- Innovation --}}
    <div class="action-item action-{{ $innovation_potential > 0 ? 'low' : 'medium' }}">
        <div class="action-label">INNOVATION STRATEGY</div>
        @if($innovation_potential > 0)
            {{ $innovation_potential }} employees show high innovation potential. Deploy them in dedicated innovation sprints, hackathons, or skunkworks projects.
        @else
            Innovation potential is below target. Introduce creativity workshops, design thinking training, and allocate time for experimental projects.
        @endif
    </div>

    {{-- Role fit --}}
    @php $weakestRole = collect($role_fitment)->last(); @endphp
    @if($weakestRole && $weakestRole['avg'] < 50)
        <div class="action-item action-high">
            <div class="action-label">HIRING GAP</div>
            {{ $weakestRole['role'] }} fitment is only {{ $weakestRole['avg'] }}%. Consider targeted hiring for this capability or cross-train existing employees.
        </div>
    @endif

    {{-- Upskilling --}}
    @foreach(array_slice($upskilling_priorities, 0, 2) as $up)
        <div class="action-item action-medium">
            <div class="action-label">L&amp;D PRIORITY — {{ strtoupper($up['priority']) }}</div>
            Invest in {{ $up['domain'] }} training (current: {{ $up['avg'] }}%, gap: {{ $up['gap'] }}pts). Consider online courses, workshops, or external certifications.
        </div>
    @endforeach

    {{-- Retention --}}
    @if(($dist['exceptional'] ?? 0) > 0)
        <div class="action-item action-medium">
            <div class="action-label">RETENTION STRATEGY</div>
            {{ $dist['exceptional'] }} exceptional performers identified. Implement retention strategies: competitive compensation, growth opportunities, meaningful recognition, and clear career paths.
        </div>
    @endif
</div>

{{-- ──────────────────── FOOTER ──────────────────── --}}
<div class="footer">
    <div class="footer-brand">MetrixsMate</div>
    <div class="footer-text">Comprehensive Talent Assessment &amp; Workforce Intelligence Platform</div>
    <div class="footer-conf">CONFIDENTIAL — This report contains sensitive HR data and is intended for authorized personnel only.</div>
</div>

</body>
</html>
