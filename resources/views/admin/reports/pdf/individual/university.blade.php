<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Career Readiness Report — {{ $user->name }}</title>
    @include('admin.reports.pdf._styles')
    <style>
        .profile-grid { display: table; width: 100%; margin-bottom: 12px; }
        .profile-left { display: table-cell; width: 65%; vertical-align: top; }
        .profile-right { display: table-cell; width: 35%; vertical-align: top; text-align: center; padding-left: 10px; }
        .score-ring { width: 80px; height: 80px; border-radius: 50%; border: 6px solid #e5e7eb; display: inline-block; text-align: center; line-height: 68px; font-size: 22px; font-weight: 700; margin-bottom: 4px; }
        .profile-detail { font-size: 8.5px; color: #6b7280; margin-bottom: 3px; }
        .profile-detail strong { color: #111827; }
        .career-card { border: 1px solid #e5e7eb; border-radius: 6px; padding: 8px 10px; margin-bottom: 6px; page-break-inside: avoid; }
        .career-rank { display: inline-block; width: 18px; height: 18px; border-radius: 50%; text-align: center; line-height: 18px; font-size: 8px; font-weight: 700; color: #fff; margin-right: 6px; }
        .rank-1 { background: #059669; } .rank-2 { background: #3b82f6; } .rank-3 { background: #f59e0b; }
        .careers-list { font-size: 8px; color: #6b7280; margin-top: 3px; }
        .readiness-indicator { display: inline-block; padding: 2px 8px; border-radius: 10px; font-size: 7.5px; font-weight: 700; }
        .ready-yes { background: #d1fae5; color: #047857; }
        .ready-no { background: #fee2e2; color: #b91c1c; }
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
    <h1>Career Readiness &amp; Potential Report</h1>
    <div class="institution">{{ $user->name }}</div>
    <div class="badge badge-university">UNIVERSITY REPORT</div>
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
            <div class="profile-detail"><strong>Career Ready:</strong>
                <span class="readiness-indicator {{ $career_ready ? 'ready-yes' : 'ready-no' }}">{{ $career_ready ? 'YES' : 'NEEDS DEVELOPMENT' }}</span>
            </div>
        </div>
        <div class="profile-right">
            <div class="score-ring" style="border-color:#4f46e5;color:#4f46e5;">{{ $employability_index }}%</div>
            <div style="font-size:8px;color:#6b7280;font-weight:600;">EMPLOYABILITY INDEX</div>
        </div>
    </div>
</div>

{{-- ──────────────────── SCORE OVERVIEW ──────────────────── --}}
<div class="section">
    <div class="section-head">Assessment Score Overview</div>

    <div class="exec-grid">
        <div class="exec-cell">
            <div class="exec-label">Employability</div>
            <div class="exec-value v-blue">{{ $employability_index }}%</div>
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
            <div class="exec-label">Personality</div>
            <div class="exec-value v-amber">{{ $scores['personality'] }}%</div>
        </div>
    </div>

    <div class="info-box ib-blue">
        <div class="ib-title">What is the Employability Index?</div>
        <div class="ib-text">A weighted composite of Career Aptitude (60%) and Cognitive Ability (40%) that indicates
        how prepared this student is for professional roles. Scores above 65% indicate strong career readiness.</div>
    </div>
</div>

{{-- ──────────────────── INDUSTRY READINESS ──────────────────── --}}
@if(count($industry_readiness) > 0)
<div class="section">
    <div class="section-head">Industry Sector Readiness</div>
    <div class="section-sub">How well-prepared this student is for different industry sectors — guides placement and internship targeting</div>

    @foreach($industry_readiness as $ir)
        @php
            $k = $c($ir['level']);
        @endphp
        <div class="dc dc-{{ $k }}">
            <div class="dh">
                <div class="dname">{{ $ir['sector'] }}</div>
                <div class="dright">
                    <span class="dscore sc-{{ $k }}">{{ $ir['score'] }}%</span>
                    <span class="readiness-indicator {{ $ir['ready'] ? 'ready-yes' : 'ready-no' }}" style="margin-left:4px;">{{ $ir['ready'] ? 'READY' : 'DEVELOPING' }}</span>
                </div>
            </div>
            <div class="pbar"><div class="pfill pf-{{ $k }}" style="width:{{ $ir['score'] }}%"></div></div>
        </div>
    @endforeach
</div>
@endif

{{-- ──────────────────── CAREER INTEREST PROFILE ──────────────────── --}}
@if(count($riasec_domains) > 0)
<div class="section">
    <div class="section-head">Career Interest Profile (RIASEC)</div>
    <div class="section-sub">Holland's model of career interests — identifies which types of work environments and tasks resonate most</div>

    @if($holland_code)
        <div class="info-box ib-purple" style="margin-bottom:8px;">
            <div class="ib-title">Holland Code: {{ $holland_code }}</div>
            <div class="ib-text">This three-letter code summarizes dominant career orientations and guides specialization choices.</div>
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
        </div>
    @endforeach
</div>
@endif

{{-- ──────────────────── CAREER PATHS ──────────────────── --}}
@if(count($career_paths) > 0)
<div class="section">
    <div class="section-head">Recommended Career Paths</div>
    <div class="section-sub">Academic streams and career directions best aligned with this student's aptitude profile</div>

    @foreach($career_paths as $i => $cp)
        @php $rank = min($i + 1, 3); @endphp
        <div class="career-card" style="border-left:4px solid {{ $rank === 1 ? '#059669' : ($rank === 2 ? '#3b82f6' : '#f59e0b') }};">
            <div class="dh">
                <div class="dname"><span class="career-rank rank-{{ $rank }}">{{ $rank }}</span>{{ $cp['stream'] }}</div>
                <div class="dright"><span class="dscore" style="color:{{ $rank === 1 ? '#059669' : ($rank === 2 ? '#3b82f6' : '#f59e0b') }};">{{ $cp['score'] }}%</span></div>
            </div>
            <div class="pbar" style="margin-top:4px;"><div class="pfill" style="width:{{ $cp['score'] }}%;background:{{ $rank === 1 ? '#059669' : ($rank === 2 ? '#3b82f6' : '#f59e0b') }};height:100%;border-radius:3px;"></div></div>
            @if(count($cp['careers'] ?? []) > 0)
                <div class="careers-list">Careers: {{ implode(', ', array_slice($cp['careers'], 0, 5)) }}</div>
            @endif
        </div>
    @endforeach
</div>
@endif

{{-- ──────────────────── PERSONALITY PROFILE (OCEAN) ──────────────────── --}}
@if(count($ocean_domains) > 0)
<div class="section">
    <div class="section-head">Personality Profile (OCEAN)</div>
    <div class="section-sub">Personality traits that affect workplace behavior, teamwork, and professional growth</div>

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

{{-- ──────────────────── COGNITIVE ABILITIES ──────────────────── --}}
@if(count($cognitive_domains) > 0)
<div class="section">
    <div class="section-head">Cognitive Abilities</div>
    <div class="section-sub">Core intellectual capabilities — critical for academic success and professional problem-solving</div>

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
    <div class="section-sub">Forward-looking indicators of potential in key career-relevant dimensions</div>

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
                <div class="info-box ib-green"><div class="ib-text">Building across all domains — keep going!</div></div>
            @endforelse
        </div>
    </div>
    <div class="col-r">
        <div class="section">
            <div class="section-head" style="color:#dc2626;border-color:#fecaca;">&#9888; Growth Areas</div>
            @forelse($weaknesses as $w)
                <div class="info-box ib-red" style="margin-bottom:4px;">
                    <div class="ib-text"><strong>{{ $w['name'] }}</strong> ({{ $w['category'] }}) — {{ $w['percentage'] }}%</div>
                </div>
            @empty
                <div class="info-box ib-green"><div class="ib-text">No critical gaps — strong profile!</div></div>
            @endforelse
        </div>
    </div>
</div>

{{-- ──────────────────── STRATEGIC RECOMMENDATIONS ──────────────────── --}}
<div class="section">
    <div class="section-head">Strategic Recommendations</div>
    <div class="section-sub">Personalized guidance for maximizing career potential and placement outcomes</div>

    @php $topIndustry = $industry_readiness[0] ?? null; @endphp
    @if($topIndustry)
        <div class="action-item action-{{ $topIndustry['ready'] ? 'low' : 'medium' }}">
            <div class="action-label">PLACEMENT FOCUS</div>
            Strongest sector alignment: <strong>{{ $topIndustry['sector'] }}</strong> ({{ $topIndustry['score'] }}%). {{ $topIndustry['ready'] ? 'Prioritize internships and placements in this sector.' : 'Targeted skill-building can improve readiness for this sector.' }}
        </div>
    @endif

    @if(!$career_ready)
        <div class="action-item action-high">
            <div class="action-label">EMPLOYABILITY DEVELOPMENT</div>
            Employability index is {{ $employability_index }}% — below the 60% readiness threshold. Focus on building cognitive skills and career-specific competencies through workshops, projects, and mock interviews.
        </div>
    @endif

    @foreach(array_slice($weaknesses, 0, 2) as $w)
        <div class="action-item action-medium">
            <div class="action-label">SKILL GAP — {{ strtoupper($w['category']) }}</div>
            {{ $w['name'] }} is at {{ $w['percentage'] }}%. Recommended: targeted practice, mentoring, and course modules to bridge this gap before placement season.
        </div>
    @endforeach

    @if($career_ready)
        <div class="action-item action-low">
            <div class="action-label">CAREER READY</div>
            This student meets the career readiness threshold. Prioritize for premium placement opportunities, industry connect programs, and leadership roles.
        </div>
    @endif
</div>

{{-- ──────────────────── FOOTER ──────────────────── --}}
<div class="footer">
    <div class="footer-brand">MetrixsMate</div>
    <div class="footer-text">Comprehensive Talent Assessment &amp; Career Guidance Platform</div>
    <div class="footer-conf">CONFIDENTIAL — This report is intended for authorized university administrators and faculty only.</div>
</div>

</body>
</html>
