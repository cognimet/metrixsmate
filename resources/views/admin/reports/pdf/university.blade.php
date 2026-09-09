<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>University Career Readiness Report — {{ $institution_name }}</title>
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
    <h1>University Career Readiness Report</h1>
    <div class="institution">{{ $institution_name }}</div>
    <div class="badge badge-university">UNIVERSITY REPORT</div>
    <div class="meta">Generated on {{ $generatedDate }} &bull; {{ $completed_users }} of {{ $total_users }} students assessed</div>
</div>

{{-- ──────────────────── EXECUTIVE SUMMARY ──────────────────── --}}
<div class="section">
    <div class="section-head">Executive Summary</div>

    <div class="exec-grid">
        <div class="exec-cell">
            <div class="exec-label">Employability Index</div>
            <div class="exec-value v-blue">{{ $employability_index }}%</div>
        </div>
        <div class="exec-cell">
            <div class="exec-label">Career Ready</div>
            <div class="exec-value v-green">{{ $career_ready_pct }}%</div>
            <div style="font-size:7px;color:#6b7280;">{{ $career_ready_count }} of {{ $total }} students</div>
        </div>
        <div class="exec-cell">
            <div class="exec-label">Avg Cognitive</div>
            <div class="exec-value v-purple">{{ $avg_scores['cognitive'] }}%</div>
        </div>
        <div class="exec-cell">
            <div class="exec-label">Overall Average</div>
            <div class="exec-value v-amber">{{ $avg_scores['overall'] }}%</div>
        </div>
    </div>

    <div class="info-box ib-blue">
        <div class="ib-title">What is the Employability Index?</div>
        <div class="ib-text">A weighted composite of Career Aptitude (60%) and Cognitive Ability (40%) that indicates
        how prepared your students are for professional roles. Scores above 65% indicate strong career readiness.</div>
    </div>

    <div class="info-box ib-purple" style="margin-top:6px;">
        <div class="ib-title">How to Read This Report</div>
        <div class="ib-text">This report evaluates placement readiness by combining Personality (OCEAN), Career Interests (RIASEC/Holland), and Cognitive assessments. Performance levels: Exceptional (80%+), High (65-79%), Average (45-64%), Below Average (30-44%), Low (&lt;30%). Sections are designed to inform placement strategy, curriculum planning, and student development programs.</div>
    </div>
</div>

{{-- ──────────────────── PERFORMANCE DISTRIBUTION ──────────────────── --}}
<div class="section">
    <div class="section-head">Performance Distribution</div>
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

{{-- ──────────────────── CAREER INTEREST PROFILE ──────────────────── --}}
<div class="section">
    <div class="section-head">Career Interest Profile — Group Average (RIASEC)</div>
    <div class="section-sub">Holland's model categorizes career interests into six types — these patterns indicate which career environments and job functions will produce the highest engagement and satisfaction. Critical for placement strategy.</div>

    @foreach($riasec_agg as $d)
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

    @if(count($holland_dist) > 0)
        <div style="margin-top: 6px;">
            <table class="dtable">
                <tr><th>Holland Code</th><th>Students</th><th>% of Cohort</th></tr>
                @foreach($holland_dist as $code => $cnt)
                    <tr><td><strong>{{ $code }}</strong></td><td>{{ $cnt }}</td><td>{{ round($cnt / $total * 100) }}%</td></tr>
                @endforeach
            </table>
        </div>
    @endif
</div>

{{-- ──────────────────── PERSONALITY PROFILE ──────────────────── --}}
<div class="section">
    <div class="section-head">Personality Profile — Group Average (OCEAN)</div>

    @foreach($ocean_agg as $d)
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
    <div class="section-head">Cognitive Abilities — Group Average</div>
    <div class="section-sub">Core reasoning and problem-solving capabilities — strong predictors of academic and professional performance</div>

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

{{-- ──────────────────── SPECIALIZATION ALIGNMENT ──────────────────── --}}
<div class="section">
    <div class="section-head">Specialization &amp; Stream Alignment</div>
    <div class="section-sub">Shows which academic specializations align best with student aptitude — informs curriculum decisions and elective planning</div>

    <table class="dtable">
        <tr><th>Specialization</th><th>Avg Aptitude</th><th>Students Suited</th><th>% of Cohort</th><th>Key Careers</th></tr>
        @foreach($spec_alignment as $sa)
            <tr>
                <td><strong>{{ $sa['stream'] }}</strong></td>
                <td>{{ $sa['avg'] }}%</td>
                <td>{{ $sa['suited'] }}</td>
                <td>{{ $sa['pct'] }}%</td>
                <td style="font-size:8px;">{{ implode(', ', array_slice($sa['careers'], 0, 3)) }}</td>
            </tr>
        @endforeach
    </table>
</div>

{{-- ──────────────────── INDUSTRY READINESS ──────────────────── --}}
<div class="section">
    <div class="section-head">Industry Readiness Analysis</div>
    <div class="section-sub">How prepared your students are for different industry sectors — drives placement strategy and training partnerships</div>

    @foreach($industry_readiness as $ir)
        @php
            $k = $c($ir['avg'] >= 70 ? 'high' : ($ir['avg'] >= 50 ? 'average' : ($ir['avg'] >= 30 ? 'below-average' : 'low')));
        @endphp
        <div class="dc dc-{{ $k }}">
            <div class="dh">
                <div class="dname">{{ $ir['sector'] }}</div>
                <div class="dright">
                    <span class="dscore sc-{{ $k }}">{{ $ir['avg'] }}%</span>
                </div>
            </div>
            <div class="pbar"><div class="pfill pf-{{ $k }}" style="width:{{ $ir['avg'] }}%"></div></div>
            <div class="dstats">Ready: {{ $ir['ready'] }} students ({{ $ir['ready_pct'] }}%)</div>
        </div>
    @endforeach
</div>

{{-- ──────────────────── STRENGTHS & GROWTH ──────────────────── --}}
<div class="two-col">
    <div class="col-l">
        <div class="section">
            <div class="section-head" style="color:#059669;border-color:#a7f3d0;">&#9733; Cohort Strengths</div>
            @forelse($strengths as $s)
                <div class="info-box ib-green" style="margin-bottom:4px;">
                    <div class="ib-text"><strong>{{ $s['name'] }}</strong> — {{ $s['avg'] }}%</div>
                </div>
            @empty
                <div class="info-box ib-green"><div class="ib-text">No standout strengths yet.</div></div>
            @endforelse
        </div>
    </div>
    <div class="col-r">
        <div class="section">
            <div class="section-head" style="color:#dc2626;border-color:#fecaca;">&#9888; Growth Areas</div>
            @forelse($weaknesses as $w)
                <div class="info-box ib-red" style="margin-bottom:4px;">
                    <div class="ib-text"><strong>{{ $w['name'] }}</strong> — {{ $w['avg'] }}%</div>
                </div>
            @empty
                <div class="info-box ib-green"><div class="ib-text">No critical weaknesses.</div></div>
            @endforelse
        </div>
    </div>
</div>

{{-- ──────────────────── TOP PERFORMERS ──────────────────── --}}
@if($top_performers->isNotEmpty())
<div class="section">
    <div class="section-head">Top Performers — Placement Priority</div>
    <div class="section-sub">Highest scoring students — prioritize for premium placements and industry partnerships</div>
    <table class="dtable">
        <tr><th>#</th><th>Student</th><th>Personality</th><th>Career</th><th>Cognitive</th><th>Overall</th></tr>
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

{{-- ──────────────────── STUDENTS NEEDING SUPPORT ──────────────────── --}}
@if($needs_attention->isNotEmpty())
<div class="section">
    <div class="section-head" style="color:#dc2626;">Students Needing Career Guidance</div>
    <table class="dtable">
        <tr><th>#</th><th>Student</th><th>Personality</th><th>Career</th><th>Cognitive</th><th>Overall</th></tr>
        @foreach($needs_attention as $i => $na)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ $na['user']->name }}</td>
                <td>{{ round($na['data']['overallScore']['personality']) }}%</td>
                <td>{{ round($na['data']['overallScore']['career']) }}%</td>
                <td>{{ round($na['data']['overallScore']['cognitive']) }}%</td>
                <td><strong>{{ $na['overall'] }}%</strong></td>
            </tr>
        @endforeach
    </table>
</div>
@endif

{{-- ──────────────────── RECOMMENDED ACTIONS ──────────────────── --}}
<div class="section">
    <div class="section-head">Strategic Recommendations</div>

    <div class="action-item action-high">
        <div class="action-label">PLACEMENT STRATEGY</div>
        @if($career_ready_pct >= 60)
            {{ $career_ready_pct }}% of students are career-ready — establish industry partnerships aligned with top RIASEC profiles ({{ implode(', ', array_keys(array_slice($holland_dist, 0, 2))) }}).
        @else
            Only {{ $career_ready_pct }}% of students are career-ready — implement intensive employability workshops and internship programs before placement season.
        @endif
    </div>

    @foreach($industry_readiness as $ir)
        @if($ir['avg'] < 50)
            <div class="action-item action-medium">
                <div class="action-label">UPSKILLING NEEDED</div>
                {{ $ir['sector'] }} readiness is at {{ $ir['avg'] }}% — consider specialized training modules and guest lectures from industry professionals.
            </div>
        @endif
    @endforeach

    @foreach($weaknesses as $w)
        <div class="action-item action-medium">
            <div class="action-label">CURRICULUM GAP</div>
            {{ $w['name'] }} is a group weakness ({{ $w['avg'] }}%) — integrate targeted exercises and workshops into the curriculum.
        </div>
    @endforeach
</div>

{{-- ──────────────────── FOOTER ──────────────────── --}}
<div class="footer">
    <div class="footer-brand">MetrixsMate</div>
    <div class="footer-text">Comprehensive Talent Assessment &amp; Career Guidance Platform</div>
    <div class="footer-conf">CONFIDENTIAL — This report is intended for authorized university administrators and faculty only.</div>
</div>

</body>
</html>
