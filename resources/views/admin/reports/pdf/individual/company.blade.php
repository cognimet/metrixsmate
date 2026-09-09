<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Workforce Intelligence Report — {{ $user->name }}</title>
    @include('admin.reports.pdf._styles')
    <style>
        .profile-grid { display: table; width: 100%; margin-bottom: 12px; }
        .profile-left { display: table-cell; width: 65%; vertical-align: top; }
        .profile-right { display: table-cell; width: 35%; vertical-align: top; text-align: center; padding-left: 10px; }
        .score-ring { width: 80px; height: 80px; border-radius: 50%; border: 6px solid #e5e7eb; display: inline-block; text-align: center; line-height: 68px; font-size: 22px; font-weight: 700; margin-bottom: 4px; }
        .profile-detail { font-size: 8.5px; color: #6b7280; margin-bottom: 3px; }
        .profile-detail strong { color: #111827; }
        .fit-indicator { display: inline-block; padding: 2px 8px; border-radius: 10px; font-size: 7.5px; font-weight: 700; }
        .fit-yes { background: #d1fae5; color: #047857; }
        .fit-no { background: #fee2e2; color: #b91c1c; }
        .fit-partial { background: #fef3c7; color: #92400e; }
        .dev-card { border: 1px solid #e5e7eb; border-radius: 6px; padding: 8px 10px; margin-bottom: 6px; page-break-inside: avoid; }
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
    <h1>Workforce Intelligence &amp; Potential Report</h1>
    <div class="institution">{{ $user->name }}</div>
    <div class="badge badge-company">CORPORATE REPORT</div>
    <div class="meta">Generated on {{ $generatedDate }}</div>
</div>

{{-- ──────────────────── EMPLOYEE PROFILE ──────────────────── --}}
<div class="section">
    <div class="section-head">Employee Profile</div>
    <div class="profile-grid">
        <div class="profile-left">
            <div class="profile-detail"><strong>Name:</strong> {{ $user->name }}</div>
            <div class="profile-detail"><strong>Email:</strong> {{ $user->email }}</div>
            <div class="profile-detail"><strong>Location:</strong> {{ $user->city->name ?? 'N/A' }}, {{ $user->state->name ?? 'N/A' }}</div>
            <div class="profile-detail"><strong>Work Style:</strong> {{ $work_style }}</div>
            <div class="profile-detail"><strong>Holland Code:</strong> {{ $holland_code ?: 'N/A' }}</div>
            <div class="profile-detail"><strong>Performance:</strong> <span style="color:{{ $overall_colors['hex_text'] }};font-weight:700;">{{ ucfirst($overall_level) }}</span></div>
        </div>
        <div class="profile-right">
            <div class="score-ring" style="border-color:{{ $overall_colors['hex_progress'] }};color:{{ $overall_colors['hex_text'] }}">{{ $overall_score }}%</div>
            <div style="font-size:8px;color:#6b7280;font-weight:600;">OVERALL SCORE</div>
        </div>
    </div>
</div>

{{-- ──────────────────── EXECUTIVE SUMMARY ──────────────────── --}}
<div class="section">
    <div class="section-head">Executive Summary</div>

    <div class="exec-grid">
        <div class="exec-cell">
            <div class="exec-label">Personality</div>
            <div class="exec-value v-blue">{{ $scores['personality'] }}%</div>
        </div>
        <div class="exec-cell">
            <div class="exec-label">Career Alignment</div>
            <div class="exec-value v-purple">{{ $scores['career'] }}%</div>
        </div>
        <div class="exec-cell">
            <div class="exec-label">Cognitive Ability</div>
            <div class="exec-value v-green">{{ $scores['cognitive'] }}%</div>
        </div>
        <div class="exec-cell">
            <div class="exec-label">Overall Score</div>
            <div class="exec-value v-amber">{{ $overall_score }}%</div>
        </div>
    </div>

    <div class="two-col">
        <div class="col-l">
            <div class="info-box ib-green">
                <div class="ib-title">&#9733; Leadership Potential</div>
                <div class="ib-text">
                    @if($leadership)
                        Score: <strong>{{ $leadership['percentage'] }}%</strong> ({{ ucfirst($leadership['level']) }}).
                        {{ $leadership['percentage'] >= 70 ? 'Strong candidate for management track and succession planning.' : ($leadership['percentage'] >= 50 ? 'Shows promise — consider leadership development programs.' : 'Growth needed — focus on responsibility and decision-making skills.') }}
                    @else
                        Not assessed — complete all assessments for this insight.
                    @endif
                </div>
            </div>
        </div>
        <div class="col-r">
            <div class="info-box ib-purple">
                <div class="ib-title">&#9881; Innovation Index</div>
                <div class="ib-text">
                    @if($innovation)
                        Score: <strong>{{ $innovation['percentage'] }}%</strong> ({{ ucfirst($innovation['level']) }}).
                        {{ $innovation['percentage'] >= 70 ? 'Ideal for R&D teams, product innovation, and creative problem-solving roles.' : ($innovation['percentage'] >= 50 ? 'Has creative capacity — involve in brainstorming and innovation projects.' : 'Prefers structured work — best suited for well-defined processes.') }}
                    @else
                        Not assessed — complete all assessments for this insight.
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ──────────────────── ROLE FITMENT ──────────────────── --}}
@if(count($role_fitment) > 0)
<div class="section">
    <div class="section-head">Role Fitment Analysis</div>
    <div class="section-sub">How well this employee matches key organizational roles — guides role assignments and internal mobility</div>

    @foreach($role_fitment as $rf)
        @php $k = $c($rf['level']); @endphp
        <div class="dc dc-{{ $k }}">
            <div class="dh">
                <div class="dname">{{ $rf['role'] }}</div>
                <div class="dright">
                    <span class="dscore sc-{{ $k }}">{{ $rf['score'] }}%</span>
                    <span class="fit-indicator {{ $rf['fit'] ? 'fit-yes' : ($rf['score'] >= 45 ? 'fit-partial' : 'fit-no') }}">{{ $rf['fit'] ? 'STRONG FIT' : ($rf['score'] >= 45 ? 'PARTIAL FIT' : 'LOW FIT') }}</span>
                </div>
            </div>
            <div class="pbar"><div class="pfill pf-{{ $k }}" style="width:{{ $rf['score'] }}%"></div></div>
        </div>
    @endforeach

    @php $bestRole = $role_fitment[0] ?? null; @endphp
    @if($bestRole)
        <div class="info-box ib-blue" style="margin-top:6px;">
            <div class="ib-title">Best Role Fit: {{ $bestRole['role'] }} ({{ $bestRole['score'] }}%)</div>
            <div class="ib-text">This employee's strongest alignment is with {{ $bestRole['role'] }} roles. Consider this when making role assignments, project allocations, or internal transfer decisions.</div>
        </div>
    @endif
</div>
@endif

{{-- ──────────────────── PERSONALITY PROFILE ──────────────────── --}}
@if(count($ocean_domains) > 0)
<div class="section">
    <div class="section-head">Personality Profile (OCEAN)</div>
    <div class="section-sub">Workplace behavior patterns, collaboration style, and professional characteristics</div>

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
        </div>
    @endforeach
</div>
@endif

{{-- ──────────────────── CAREER INTERESTS ──────────────────── --}}
@if(count($riasec_domains) > 0)
<div class="section">
    <div class="section-head">Career Interest Profile (RIASEC)</div>
    <div class="section-sub">Professional interest areas that drive engagement, motivation, and job satisfaction</div>

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
        </div>
    @endforeach
</div>
@endif

{{-- ──────────────────── COGNITIVE ABILITIES ──────────────────── --}}
@if(count($cognitive_domains) > 0)
<div class="section">
    <div class="section-head">Cognitive Abilities</div>
    <div class="section-sub">Core reasoning and problem-solving capabilities — critical for complex tasks and decision-making</div>

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

{{-- ──────────────────── PREDICTIVE INSIGHTS ──────────────────── --}}
@if(count($predictive_insights) > 0)
<div class="section">
    <div class="section-head">Predictive Insights</div>
    <div class="section-sub">Forward-looking indicators of potential in strategic workplace dimensions</div>

    <div class="exec-grid">
        @foreach($predictive_insights as $pi)
            <div class="exec-cell" style="border-left:3px solid {{ $pi['colors']['hex_progress'] }};">
                <div class="exec-label">{{ $pi['name'] }}</div>
                <div class="exec-value" style="color:{{ $pi['colors']['hex_text'] }}">{{ $pi['percentage'] }}%</div>
            </div>
        @endforeach
    </div>
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
                <div class="info-box ib-green"><div class="ib-text">Building across all domains — continue development.</div></div>
            @endforelse
        </div>
    </div>
    <div class="col-r">
        <div class="section">
            <div class="section-head" style="color:#dc2626;border-color:#fecaca;">&#9888; Development Areas</div>
            @forelse($weaknesses as $w)
                <div class="info-box ib-red" style="margin-bottom:4px;">
                    <div class="ib-text"><strong>{{ $w['name'] }}</strong> ({{ $w['category'] }}) — {{ $w['percentage'] }}%</div>
                </div>
            @empty
                <div class="info-box ib-green"><div class="ib-text">No critical gaps identified — strong profile!</div></div>
            @endforelse
        </div>
    </div>
</div>

{{-- ──────────────────── DEVELOPMENT PRIORITIES ──────────────────── --}}
@if(count($dev_priorities) > 0)
<div class="section">
    <div class="section-head">Development Priorities</div>
    <div class="section-sub">Areas where targeted training and coaching will have the highest impact</div>

    <table class="dtable">
        <tr><th>Domain</th><th>Category</th><th>Current Score</th><th>Gap to Target (65%)</th></tr>
        @foreach($dev_priorities as $dp)
            <tr>
                <td><strong>{{ $dp['domain'] }}</strong></td>
                <td>{{ $dp['category'] }}</td>
                <td>{{ $dp['score'] }}%</td>
                <td>
                    @if($dp['gap'] > 20)
                        <span style="color:#dc2626;font-weight:700;">{{ $dp['gap'] }}pts (Critical)</span>
                    @elseif($dp['gap'] > 10)
                        <span style="color:#f97316;font-weight:700;">{{ $dp['gap'] }}pts (High)</span>
                    @else
                        <span style="color:#f59e0b;font-weight:700;">{{ $dp['gap'] }}pts (Moderate)</span>
                    @endif
                </td>
            </tr>
        @endforeach
    </table>
</div>
@endif

{{-- ──────────────────── STRATEGIC RECOMMENDATIONS ──────────────────── --}}
<div class="section">
    <div class="section-head">Strategic Workforce Recommendations</div>
    <div class="section-sub">Actionable insights for HR, team leads, and L&amp;D decision-making</div>

    {{-- Role assignment --}}
    @php $bestRole = $role_fitment[0] ?? null; @endphp
    @if($bestRole)
        <div class="action-item action-{{ $bestRole['fit'] ? 'low' : 'medium' }}">
            <div class="action-label">ROLE ASSIGNMENT</div>
            Best fit: <strong>{{ $bestRole['role'] }}</strong> ({{ $bestRole['score'] }}%). {{ $bestRole['fit'] ? 'Ready for this role type — consider for upcoming openings.' : 'Close to fit — bridge the gap with targeted training before role transition.' }}
        </div>
    @endif

    {{-- Leadership --}}
    @if($leadership)
        <div class="action-item action-{{ $leadership['percentage'] >= 70 ? 'low' : ($leadership['percentage'] >= 50 ? 'medium' : 'high') }}">
            <div class="action-label">LEADERSHIP DEVELOPMENT</div>
            @if($leadership['percentage'] >= 70)
                High leadership potential ({{ $leadership['percentage'] }}%). Fast-track for management programs, mentoring opportunities, and cross-functional project leadership.
            @elseif($leadership['percentage'] >= 50)
                Moderate leadership potential ({{ $leadership['percentage'] }}%). Consider emerging leader programs, decision-making workshops, and team lead assignments.
            @else
                Leadership score is {{ $leadership['percentage'] }}%. Focus on building confidence, communication skills, and accountability before management-track consideration.
            @endif
        </div>
    @endif

    {{-- Development --}}
    @foreach(array_slice($dev_priorities, 0, 2) as $dp)
        <div class="action-item action-medium">
            <div class="action-label">L&amp;D PRIORITY — {{ strtoupper($dp['category']) }}</div>
            {{ $dp['domain'] }} is at {{ $dp['score'] }}% (gap: {{ $dp['gap'] }}pts). Recommend: targeted courses, coaching sessions, or peer learning groups to help build this competency.
        </div>
    @endforeach

    {{-- Retention --}}
    @if($overall_score >= 70)
        <div class="action-item action-medium">
            <div class="action-label">RETENTION STRATEGY</div>
            This is a high-potential employee ({{ $overall_score }}% overall). Ensure engagement through competitive compensation, meaningful projects, growth opportunities, and regular recognition.
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
