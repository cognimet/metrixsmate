<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>School Talent Report — {{ $institution_name }}</title>
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
    <h1>School Talent &amp; Development Report</h1>
    <div class="institution">{{ $institution_name }}</div>
    <div class="badge badge-school">SCHOOL REPORT</div>
    <div class="meta">Generated on {{ $generatedDate }} &bull; {{ $completed_users }} of {{ $total_users }} students assessed</div>
</div>

{{-- ──────────────────── EXECUTIVE SUMMARY ──────────────────── --}}
<div class="section">
    <div class="section-head">Executive Summary</div>
    <div class="section-sub">High-level overview of your student body's performance across all assessment dimensions</div>

    <div class="exec-grid">
        <div class="exec-cell">
            <div class="exec-label">Students Assessed</div>
            <div class="exec-value v-blue">{{ $completed_users }}</div>
            <div style="font-size:7px;color:#6b7280;">of {{ $total_users }} enrolled</div>
        </div>
        <div class="exec-cell">
            <div class="exec-label">Personality Score</div>
            <div class="exec-value v-blue">{{ $avg_scores['personality'] }}%</div>
        </div>
        <div class="exec-cell">
            <div class="exec-label">Career Aptitude</div>
            <div class="exec-value v-purple">{{ $avg_scores['career'] }}%</div>
        </div>
        <div class="exec-cell">
            <div class="exec-label">Cognitive Score</div>
            <div class="exec-value v-green">{{ $avg_scores['cognitive'] }}%</div>
        </div>
        <div class="exec-cell">
            <div class="exec-label">Overall Average</div>
            <div class="exec-value v-amber">{{ $avg_scores['overall'] }}%</div>
        </div>
    </div>

    <div class="two-col">
        <div class="col-l">
            <div class="info-box ib-green">
                <div class="ib-title">&#9733; Talent Pool: {{ $talent_pool_count }} Students</div>
                <div class="ib-text">Students scoring above 70% overall — high potential candidates for advanced programs, competitions, and leadership roles.</div>
            </div>
        </div>
        <div class="col-r">
            <div class="info-box ib-red">
                <div class="ib-title">&#9888; Needs Intervention: {{ $intervention_count }} Students</div>
                <div class="ib-text">Students scoring below 40% overall — require personalized support, mentoring, and focused skill-building activities.</div>
            </div>
        </div>
    </div>

    <div class="info-box ib-blue">
        <div class="ib-title">How to Read This Report</div>
        <div class="ib-text">This report aggregates assessment results across three dimensions: Personality (OCEAN model), Career Interests (RIASEC/Holland model), and Cognitive Abilities. Scores are presented as percentages. Performance levels: Exceptional (80%+), High (65-79%), Average (45-64%), Below Average (30-44%), Low (&lt;30%). Use this data to inform curriculum, counselling, and student support decisions.</div>
    </div>
</div>

{{-- ──────────────────── PERFORMANCE DISTRIBUTION ──────────────────── --}}
<div class="section">
    <div class="section-head">Performance Distribution</div>
    <div class="section-sub">How students are distributed across performance bands</div>

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

{{-- ──────────────────── PERSONALITY PROFILE (OCEAN) ──────────────────── --}}
<div class="section">
    <div class="section-head">Personality Profile — Group Average (OCEAN)</div>
    <div class="section-sub">Average scores across the five major personality dimensions for all assessed students. The OCEAN model measures Openness, Conscientiousness, Extraversion, Agreeableness, and Neuroticism (Emotional Stability).</div>

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
            <div class="dstats">Range: {{ $d['min'] }}% — {{ $d['max'] }}% &bull; Std Dev: {{ $d['std_dev'] }} &bull; n={{ $d['count'] }}</div>
        </div>
    @endforeach
</div>

{{-- ──────────────────── CAREER INTERESTS (RIASEC) ──────────────────── --}}
<div class="section">
    <div class="section-head">Career Interest Profile — Group Average (RIASEC)</div>
    <div class="section-sub">Holland's model categorizes career interests into six types: Realistic, Investigative, Artistic, Social, Enterprising, and Conventional — guiding subject and career counselling</div>

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
        <div style="margin-top: 8px;">
            <div style="font-size:9px;font-weight:700;color:#374151;">Top Holland Codes</div>
            <table class="dtable" style="margin-top:4px;">
                <tr><th>Holland Code</th><th>Students</th></tr>
                @foreach($holland_dist as $code => $cnt)
                    <tr><td><strong>{{ $code }}</strong></td><td>{{ $cnt }}</td></tr>
                @endforeach
            </table>
        </div>
    @endif
</div>

{{-- ──────────────────── COGNITIVE ABILITIES ──────────────────── --}}
<div class="section">
    <div class="section-head">Cognitive Abilities — Group Average</div>
    <div class="section-sub">Measures reasoning, memory, processing speed, and problem-solving skills — these predict academic performance and learning potential</div>

    @foreach($cognitive_agg as $d)
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
            <div class="dstats">Range: {{ $d['min'] }}% — {{ $d['max'] }}%</div>
        </div>
    @endforeach
</div>

{{-- ──────────────────── LEARNING STYLES ──────────────────── --}}
<div class="section">
    <div class="section-head">Learning Style Distribution</div>
    <div class="section-sub">Average preferred learning modalities across the student body — use these to inform teaching methodology</div>

    <div class="exec-grid">
        @foreach($learning_agg as $ls)
            <div class="exec-cell">
                <div class="exec-label">{{ $ls['name'] }}</div>
                <div class="exec-value v-blue">{{ $ls['avg'] }}%</div>
            </div>
        @endforeach
    </div>
</div>

{{-- ──────────────────── STREAM RECOMMENDATIONS ──────────────────── --}}
<div class="section">
    <div class="section-head">Academic Stream Recommendations</div>
    <div class="section-sub">Which academic streams best fit your student body — helps with subject counselling and curriculum planning</div>

    <table class="dtable">
        <tr><th>Stream</th><th>Avg Aptitude</th><th>Students Suited (≥60%)</th><th>Top Careers</th></tr>
        @foreach($stream_agg as $s)
            <tr>
                <td><strong>{{ $s['name'] }}</strong></td>
                <td>{{ $s['avg'] }}%</td>
                <td>{{ $s['count'] }} ({{ $total > 0 ? round($s['count'] / $total * 100) : 0 }}%)</td>
                <td>{{ implode(', ', array_slice($s['careers'], 0, 3)) }}</td>
            </tr>
        @endforeach
    </table>
</div>

{{-- ──────────────────── STRENGTHS & WEAKNESSES ──────────────────── --}}
<div class="two-col">
    <div class="col-l">
        <div class="section">
            <div class="section-head" style="color:#059669;border-color:#a7f3d0;">&#9733; Group Strengths</div>
            @forelse($strengths as $s)
                <div class="info-box ib-green" style="margin-bottom:4px;">
                    <div class="ib-text"><strong>{{ $s['name'] }}</strong> — {{ $s['avg'] }}% avg</div>
                </div>
            @empty
                <div class="info-box ib-green"><div class="ib-text">No standout strengths identified yet.</div></div>
            @endforelse
        </div>
    </div>
    <div class="col-r">
        <div class="section">
            <div class="section-head" style="color:#dc2626;border-color:#fecaca;">&#9888; Areas for Growth</div>
            @forelse($weaknesses as $w)
                <div class="info-box ib-red" style="margin-bottom:4px;">
                    <div class="ib-text"><strong>{{ $w['name'] }}</strong> — {{ $w['avg'] }}% avg</div>
                </div>
            @empty
                <div class="info-box ib-green"><div class="ib-text">No critical weaknesses identified — great work!</div></div>
            @endforelse
        </div>
    </div>
</div>

{{-- ──────────────────── TOP PERFORMERS ──────────────────── --}}
@if($top_performers->isNotEmpty())
<div class="section">
    <div class="section-head">Top Performers</div>
    <div class="section-sub">Students with the highest overall scores — candidates for advanced programs and leadership roles</div>
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

{{-- ──────────────────── STUDENTS NEEDING ATTENTION ──────────────────── --}}
@if($needs_attention->isNotEmpty())
<div class="section">
    <div class="section-head" style="color:#dc2626;">Students Needing Support</div>
    <div class="section-sub">Students with the lowest overall scores — prioritize for mentoring and intervention programs</div>
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

{{-- ──────────────────── TEACHER ACTION PLAN ──────────────────── --}}
<div class="section">
    <div class="section-head">Recommended Actions for Teachers</div>
    <div class="section-sub">Prioritized steps to improve student outcomes based on assessment data</div>

    @foreach($teacher_actions as $action)
        <div class="action-item action-{{ $action['priority'] }}">
            <div class="action-label">{{ strtoupper($action['priority']) }} PRIORITY</div>
            {{ $action['action'] }}
        </div>
    @endforeach
</div>

{{-- ──────────────────── PARENT GUIDANCE ──────────────────── --}}
<div class="section">
    <div class="section-head">Guidance for Parents</div>
    <div class="section-sub">Key insights that can be shared with parents to support student development at home</div>

    <div class="info-box ib-blue">
        @foreach($parent_guidance as $pg)
            <div class="ib-text" style="margin-bottom:4px;">&#8226; {!! str_replace(['**'], ['<strong>'], str_replace(['**'], ['</strong>'], $pg)) !!}</div>
        @endforeach
    </div>
</div>

{{-- ──────────────────── FOOTER ──────────────────── --}}
<div class="footer">
    <div class="footer-brand">MetrixsMate</div>
    <div class="footer-text">Comprehensive Talent Assessment &amp; Career Guidance Platform</div>
    <div class="footer-conf">CONFIDENTIAL — This report is intended for authorized school administrators only.</div>
</div>

</body>
</html>
