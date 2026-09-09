<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Student Assessment Report — {{ $user->name }}</title>
    @include('admin.reports.pdf._styles')
    <style>
        .profile-grid { display: table; width: 100%; margin-bottom: 12px; }
        .profile-left { display: table-cell; width: 65%; vertical-align: top; }
        .profile-right { display: table-cell; width: 35%; vertical-align: top; text-align: center; padding-left: 10px; }
        .score-ring { width: 80px; height: 80px; border-radius: 50%; border: 6px solid #e5e7eb; display: inline-block; text-align: center; line-height: 68px; font-size: 22px; font-weight: 700; margin-bottom: 4px; }
        .profile-detail { font-size: 8.5px; color: #6b7280; margin-bottom: 3px; }
        .profile-detail strong { color: #111827; }
        .stream-card { border: 1px solid #e5e7eb; border-radius: 6px; padding: 8px 10px; margin-bottom: 6px; page-break-inside: avoid; }
        .stream-rank { display: inline-block; width: 18px; height: 18px; border-radius: 50%; text-align: center; line-height: 18px; font-size: 8px; font-weight: 700; color: #fff; margin-right: 6px; }
        .rank-1 { background: #059669; } .rank-2 { background: #3b82f6; } .rank-3 { background: #f59e0b; }
        .careers-list { font-size: 8px; color: #6b7280; margin-top: 3px; }
        .ls-bar { display: table; width: 100%; margin-bottom: 6px; }
        .ls-label { display: table-cell; width: 30%; font-size: 9px; font-weight: 600; color: #374151; vertical-align: middle; }
        .ls-track { display: table-cell; width: 55%; vertical-align: middle; padding: 0 8px; }
        .ls-value { display: table-cell; width: 15%; text-align: right; font-size: 9px; font-weight: 700; color: #4f46e5; vertical-align: middle; }
    </style>
</head>
<body>
@php
    $c = fn($level) => match($level ?? 'average') {
        'exceptional' => 'e', 'high' => 'h', 'below-average' => 'b', 'low' => 'l', default => 'a',
    };
@endphp

{{-- ──────────────────── HEADER ──────────────────── --}}
<div class="report-header">
    <div class="brand">MetrixsMate</div>
    <h1>Student Talent &amp; Development Report</h1>
    <div class="institution">{{ $user->name }}</div>
    <div class="badge badge-school">SCHOOL REPORT</div>
    <div class="meta">Generated on {{ $generatedDate }}</div>
</div>

{{-- ──────────────────── STUDENT PROFILE ──────────────────── --}}
<div class="section">
    <div class="section-head">Student Profile</div>
    <div class="profile-grid">
        <div class="profile-left">
            <div class="profile-detail"><strong>Name:</strong> {{ $user->name }}</div>
            <div class="profile-detail"><strong>Email:</strong> {{ $user->email }}</div>
            <div class="profile-detail"><strong>Location:</strong> {{ $user->city->name ?? 'N/A' }}, {{ $user->state->name ?? 'N/A' }}</div>
            <div class="profile-detail"><strong>Holland Code:</strong> {{ $holland_code ?: 'N/A' }}</div>
            <div class="profile-detail"><strong>Performance Level:</strong> <span style="color:{{ $overall_colors['hex_text'] }};font-weight:700;">{{ ucfirst($overall_level) }}</span></div>
        </div>
        <div class="profile-right">
            <div class="score-ring" style="border-color:{{ $overall_colors['hex_progress'] }};color:{{ $overall_colors['hex_text'] }}">{{ $overall_score }}%</div>
            <div style="font-size:8px;color:#6b7280;font-weight:600;">OVERALL SCORE</div>
        </div>
    </div>
</div>

{{-- ──────────────────── SCORE OVERVIEW ──────────────────── --}}
<div class="section">
    <div class="section-head">Assessment Score Overview</div>
    <div class="section-sub">A snapshot of performance across the three core assessment dimensions</div>

    <div class="exec-grid">
        <div class="exec-cell">
            <div class="exec-label">Personality</div>
            <div class="exec-value v-blue">{{ $scores['personality'] }}%</div>
        </div>
        <div class="exec-cell">
            <div class="exec-label">Career Aptitude</div>
            <div class="exec-value v-purple">{{ $scores['career'] }}%</div>
        </div>
        <div class="exec-cell">
            <div class="exec-label">Cognitive Ability</div>
            <div class="exec-value v-green">{{ $scores['cognitive'] }}%</div>
        </div>
        <div class="exec-cell">
            <div class="exec-label">Overall</div>
            <div class="exec-value v-amber">{{ $overall_score }}%</div>
        </div>
    </div>
</div>

{{-- ──────────────────── PERSONALITY PROFILE (OCEAN) ──────────────────── --}}
@if(count($ocean_domains) > 0)
<div class="section">
    <div class="section-head">Personality Profile (OCEAN)</div>
    <div class="section-sub">Measures five core personality dimensions that influence behavior, learning style, and interpersonal dynamics</div>

    @foreach($ocean_domains as $d)
        @php $k = $c($d['level']); @endphp
        <div class="dc dc-{{ $k }}">
            <div class="dh">
                <div class="dname">{{ $d['name'] }}</div>
                <div class="dright">
                    <span class="dscore sc-{{ $k }}">{{ $d['percentage'] }}%</span>
                    <span class="dbadge bd-{{ $k }}">{{ ucfirst($d['level']) }}</span>
                </div>
            </div>
            <div class="pbar"><div class="pfill pf-{{ $k }}" style="width:{{ $d['percentage'] }}%"></div></div>
            @if(!empty($d['description']))
                <div class="dstats">{{ $d['description'] }}</div>
            @endif
        </div>
    @endforeach
</div>
@endif

{{-- ──────────────────── FACETS & CCS SKILLS ──────────────────── --}}
@if(count($facets) > 0 || count($ccs_skills) > 0)
<div class="section">
    @if(count($facets) > 0)
    <div class="section-head">Personality Facets</div>
    <div class="section-sub">Detailed sub-traits that provide a granular view of personality characteristics</div>
    <div class="two-col">
        @foreach(array_chunk($facets, (int)ceil(count($facets)/2)) as $chunk)
        <div class="{{ $loop->first ? 'col-l' : 'col-r' }}">
            @foreach($chunk as $f)
                @php $k = $c($f['level']); @endphp
                <div style="margin-bottom:4px;">
                    <div class="dh" style="margin-bottom:1px;">
                        <div class="dname" style="font-size:8.5px;">{{ $f['name'] }}</div>
                        <div class="dright"><span class="dscore sc-{{ $k }}" style="font-size:11px;">{{ $f['percentage'] }}%</span></div>
                    </div>
                    <div class="pbar"><div class="pfill pf-{{ $k }}" style="width:{{ $f['percentage'] }}%"></div></div>
                </div>
            @endforeach
        </div>
        @endforeach
    </div>
    @endif

    @if(count($ccs_skills) > 0)
    <div style="margin-top:8px;">
        <div class="section-head">Core Competency Skills (CCS)</div>
        <div class="section-sub">Key competencies derived from personality assessment — important for academic and career success</div>
        @foreach($ccs_skills as $s)
            @php $k = $c($s['level']); @endphp
            <div class="dc dc-{{ $k }}" style="padding:6px 8px;">
                <div class="dh">
                    <div class="dname" style="font-size:9px;">{{ $s['name'] }}</div>
                    <div class="dright"><span class="dscore sc-{{ $k }}" style="font-size:12px;">{{ $s['percentage'] }}%</span></div>
                </div>
                <div class="pbar"><div class="pfill pf-{{ $k }}" style="width:{{ $s['percentage'] }}%"></div></div>
            </div>
        @endforeach
    </div>
    @endif
</div>
@endif

{{-- ──────────────────── PREDICTIVE INSIGHTS ──────────────────── --}}
@if(count($predictive_insights) > 0)
<div class="section">
    <div class="section-head">Predictive Insights</div>
    <div class="section-sub">Data-driven projections of potential in key areas based on personality and aptitude patterns</div>

    <div class="exec-grid">
        @foreach($predictive_insights as $pi)
            @php $k = $c($pi['level']); @endphp
            <div class="exec-cell" style="border-left:3px solid {{ $pi['colors']['hex_progress'] }};">
                <div class="exec-label">{{ $pi['name'] }}</div>
                <div class="exec-value" style="color:{{ $pi['colors']['hex_text'] }}">{{ $pi['percentage'] }}%</div>
                <div style="font-size:7px;color:#6b7280;">{{ ucfirst($pi['level']) }}</div>
            </div>
        @endforeach
    </div>
</div>
@endif

{{-- ──────────────────── CAREER INTERESTS (RIASEC) ──────────────────── --}}
@if(count($riasec_domains) > 0)
<div class="section">
    <div class="section-head">Career Interest Profile (RIASEC)</div>
    <div class="section-sub">Identifies career interest patterns using Holland's model — helps guide academic stream and career exploration</div>

    @if($holland_code)
        <div class="info-box ib-blue" style="margin-bottom:8px;">
            <div class="ib-title">Holland Code: {{ $holland_code }}</div>
            <div class="ib-text">This three-letter code represents the student's dominant career interest areas, guiding academic and extracurricular choices.</div>
        </div>
    @endif

    @foreach($riasec_domains as $d)
        @php $k = $c($d['level']); @endphp
        <div class="dc dc-{{ $k }}">
            <div class="dh">
                <div class="dname">{{ $d['name'] }}</div>
                <div class="dright">
                    <span class="dscore sc-{{ $k }}">{{ $d['percentage'] }}%</span>
                    <span class="dbadge bd-{{ $k }}">{{ ucfirst($d['level']) }}</span>
                </div>
            </div>
            <div class="pbar"><div class="pfill pf-{{ $k }}" style="width:{{ $d['percentage'] }}%"></div></div>
            @if(!empty($d['description']))
                <div class="dstats">{{ $d['description'] }}</div>
            @endif
        </div>
    @endforeach
</div>
@endif

{{-- ──────────────────── COGNITIVE ABILITIES ──────────────────── --}}
@if(count($cognitive_domains) > 0)
<div class="section">
    <div class="section-head">Cognitive Abilities</div>
    <div class="section-sub">Measures core intellectual abilities including reasoning, memory, processing speed, and problem-solving</div>

    @foreach($cognitive_domains as $d)
        @php $k = $c($d['level']); @endphp
        <div class="dc dc-{{ $k }}">
            <div class="dh">
                <div class="dname">{{ $d['name'] }}</div>
                <div class="dright">
                    <span class="dscore sc-{{ $k }}">{{ $d['percentage'] }}%</span>
                    <span class="dbadge bd-{{ $k }}">{{ ucfirst($d['level']) }}</span>
                </div>
            </div>
            <div class="pbar"><div class="pfill pf-{{ $k }}" style="width:{{ $d['percentage'] }}%"></div></div>
        </div>
    @endforeach
</div>
@endif

{{-- ──────────────────── LEARNING STYLES ──────────────────── --}}
@if(count($learning_styles) > 0)
<div class="section">
    <div class="section-head">Learning Style Analysis</div>
    <div class="section-sub">Understanding how this student learns best — use these insights to personalize teaching approaches</div>

    @foreach($learning_styles as $ls)
        <div class="ls-bar">
            <div class="ls-label">{{ $ls['name'] }}</div>
            <div class="ls-track">
                <div class="pbar" style="height:7px;"><div class="pfill" style="width:{{ $ls['score'] }}%;background:#4f46e5;height:100%;border-radius:3px;"></div></div>
            </div>
            <div class="ls-value">{{ $ls['score'] }}%</div>
        </div>
    @endforeach

    @php $topStyle = $learning_styles[0] ?? null; @endphp
    @if($topStyle)
        <div class="info-box ib-purple" style="margin-top:6px;">
            <div class="ib-title">Primary Learning Style: {{ $topStyle['name'] }}</div>
            <div class="ib-text">This student learns most effectively through {{ strtolower($topStyle['name']) }} methods. Incorporate related activities into lessons for maximum engagement and retention.</div>
        </div>
    @endif
</div>
@endif

{{-- ──────────────────── STREAM RECOMMENDATIONS ──────────────────── --}}
@if(count($top_streams) > 0)
<div class="section">
    <div class="section-head">Academic Stream Recommendations</div>
    <div class="section-sub">Best-fit academic streams based on combined personality, career interest, and cognitive assessment data</div>

    @foreach($top_streams as $i => $s)
        @php $rank = $i + 1; @endphp
        <div class="stream-card" style="border-left:4px solid {{ $rank === 1 ? '#059669' : ($rank === 2 ? '#3b82f6' : '#f59e0b') }};">
            <div class="dh">
                <div class="dname"><span class="stream-rank rank-{{ $rank }}">{{ $rank }}</span>{{ $s['name'] }}</div>
                <div class="dright"><span class="dscore" style="color:{{ $rank === 1 ? '#059669' : ($rank === 2 ? '#3b82f6' : '#f59e0b') }};">{{ $s['score'] }}%</span></div>
            </div>
            <div class="pbar" style="margin-top:4px;"><div class="pfill" style="width:{{ $s['score'] }}%;background:{{ $rank === 1 ? '#059669' : ($rank === 2 ? '#3b82f6' : '#f59e0b') }};height:100%;border-radius:3px;"></div></div>
            @if(count($s['careers'] ?? []) > 0)
                <div class="careers-list">Careers: {{ implode(', ', array_slice($s['careers'], 0, 5)) }}</div>
            @endif
        </div>
    @endforeach
</div>
@endif

{{-- ──────────────────── STRENGTHS & GROWTH ──────────────────── --}}
<div class="two-col">
    <div class="col-l">
        <div class="section">
            <div class="section-head" style="color:#059669;border-color:#a7f3d0;">&#9733; Key Strengths</div>
            @forelse($strengths as $s)
                <div class="info-box ib-green" style="margin-bottom:4px;">
                    <div class="ib-text"><strong>{{ $s['name'] }}</strong> ({{ $s['category'] }}) — {{ $s['percentage'] }}%</div>
                </div>
            @empty
                <div class="info-box ib-green"><div class="ib-text">Continue building across all domains — potential is there!</div></div>
            @endforelse
        </div>
    </div>
    <div class="col-r">
        <div class="section">
            <div class="section-head" style="color:#dc2626;border-color:#fecaca;">&#9888; Areas for Growth</div>
            @forelse($weaknesses as $w)
                <div class="info-box ib-red" style="margin-bottom:4px;">
                    <div class="ib-text"><strong>{{ $w['name'] }}</strong> ({{ $w['category'] }}) — {{ $w['percentage'] }}%</div>
                </div>
            @empty
                <div class="info-box ib-green"><div class="ib-text">No critical weaknesses identified — excellent!</div></div>
            @endforelse
        </div>
    </div>
</div>

{{-- ──────────────────── TEACHER ACTION PLAN ──────────────────── --}}
@if(count($teacher_actions) > 0)
<div class="section">
    <div class="section-head">Recommended Actions for Teachers</div>
    <div class="section-sub">Personalized, prioritized steps to support this student's development</div>

    @foreach($teacher_actions as $action)
        <div class="action-item action-{{ $action['priority'] }}">
            <div class="action-label">{{ strtoupper($action['priority']) }} PRIORITY</div>
            {{ $action['action'] }}
        </div>
    @endforeach
</div>
@endif

{{-- ──────────────────── PARENT GUIDANCE ──────────────────── --}}
@if(count($parent_guidance) > 0)
<div class="section">
    <div class="section-head">Guidance for Parents</div>
    <div class="section-sub">Key insights to share with parents — helping them support learning and growth at home</div>

    <div class="info-box ib-blue">
        @foreach($parent_guidance as $pg)
            <div class="ib-text" style="margin-bottom:4px;">&#8226; {!! str_replace(['**'], ['<strong>'], str_replace(['**'], ['</strong>'], $pg)) !!}</div>
        @endforeach
    </div>
</div>
@endif

{{-- ──────────────────── FOOTER ──────────────────── --}}
<div class="footer">
    <div class="footer-brand">MetrixsMate</div>
    <div class="footer-text">Comprehensive Talent Assessment &amp; Career Guidance Platform</div>
    <div class="footer-conf">CONFIDENTIAL — This report is intended for authorized school administrators only.</div>
</div>

</body>
</html>
