<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>MetrixsMate Assessment Report</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 10px; color: #1f2937; background: #fff; padding: 14px; line-height: 1.45; }
        .header { text-align: center; margin-bottom: 14px; padding-bottom: 10px; border-bottom: 2px solid #e5e7eb; }
        .header h1 { font-size: 20px; font-weight: 700; color: #111827; }
        .header .sub { font-size: 9px; color: #6b7280; margin-top: 2px; }
        .overview { display: table; width: 100%; margin-bottom: 12px; border-collapse: separate; border-spacing: 8px 0; }
        .ov-cell { display: table-cell; width: 33.33%; text-align: center; padding: 10px 8px; border-radius: 8px; border: 1px solid #e5e7eb; background: #f9fafb; }
        .ov-label { font-size: 9px; color: #6b7280; font-weight: 600; margin-bottom: 3px; }
        .ov-value { font-size: 24px; font-weight: 700; }
        .ov-blue   { color: #4f46e5; }
        .ov-green  { color: #059669; }
        .ov-purple { color: #7c3aed; }
        .two-col { display: table; width: 100%; border-collapse: separate; border-spacing: 8px 0; margin-bottom: 12px; }
        .col-l { display: table-cell; width: 50%; vertical-align: top; }
        .col-r { display: table-cell; width: 50%; vertical-align: top; }
        .section { margin-bottom: 14px; }
        .section-head { font-size: 13px; font-weight: 700; color: #111827; border-bottom: 2px solid #e5e7eb; padding-bottom: 5px; margin-bottom: 4px; }
        .section-sub { font-size: 9px; color: #6b7280; margin-bottom: 8px; }
        .career-box { background: #4f46e5; color: #fff; border-radius: 8px; padding: 12px; }
        .career-box h3 { font-size: 11px; font-weight: 700; margin-bottom: 2px; }
        .cb-sub { font-size: 8px; color: rgba(255,255,255,.8); margin-bottom: 8px; }
        .career-item { background: rgba(255,255,255,.15); border-left: 3px solid rgba(255,255,255,.5); padding: 5px 7px; border-radius: 3px; font-size: 9px; color: #fff; font-weight: 500; margin-bottom: 4px; }
        .learning-box { background: #fff; border: 1px solid #e5e7eb; border-radius: 8px; padding: 12px; }
        .learning-box h3 { font-size: 11px; font-weight: 700; color: #111827; margin-bottom: 2px; }
        .lb-sub { font-size: 8px; color: #6b7280; margin-bottom: 8px; }
        .ls-grid { display: table; width: 100%; border-collapse: separate; border-spacing: 5px; }
        .ls-cell { display: table-cell; width: 50%; text-align: center; border: 1px solid #d1d5db; border-radius: 6px; padding: 6px 4px; background: #f9fafb; }
        .ls-cell.pref { border: 2px solid #10b981; background: #ecfdf5; }
        .ls-name { font-size: 9px; font-weight: 600; color: #111827; }
        .ls-score { font-size: 15px; font-weight: 700; color: #059669; }
        .pbar { height: 4px; background: #e5e7eb; border-radius: 3px; margin-top: 4px; overflow: hidden; }
        .pfill { height: 100%; border-radius: 3px; }
        .dc-e { border-left: 4px solid #059669; background: #f0fdf4; }
        .dc-h { border-left: 4px solid #3b82f6; background: #eff6ff; }
        .dc-a { border-left: 4px solid #f59e0b; background: #fffbeb; }
        .dc-b { border-left: 4px solid #f97316; background: #fff7ed; }
        .dc-l { border-left: 4px solid #ef4444; background: #fef2f2; }
        .dcard { border: 1px solid #e5e7eb; border-radius: 6px; padding: 9px; margin-bottom: 7px; page-break-inside: avoid; }
        .dh { display: table; width: 100%; margin-bottom: 5px; }
        .dname { display: table-cell; font-size: 10px; font-weight: 600; color: #111827; vertical-align: middle; }
        .dright { display: table-cell; text-align: right; vertical-align: middle; }
        .dscore { font-size: 16px; font-weight: 700; }
        .dbadge { font-size: 8px; font-weight: 600; padding: 1px 6px; border-radius: 10px; margin-left: 4px; }
        .sc-e { color: #059669; } .bd-e { background: #d1fae5; color: #047857; } .pf-e { background: #059669; }
        .sc-h { color: #3b82f6; } .bd-h { background: #dbeafe; color: #1d4ed8; } .pf-h { background: #3b82f6; }
        .sc-a { color: #f59e0b; } .bd-a { background: #fef3c7; color: #b45309; } .pf-a { background: #f59e0b; }
        .sc-b { color: #f97316; } .bd-b { background: #ffedd5; color: #c2410c; } .pf-b { background: #f97316; }
        .sc-l { color: #ef4444; } .bd-l { background: #fee2e2; color: #b91c1c; } .pf-l { background: #ef4444; }
        .ddesc { font-size: 8.5px; color: #4b5563; line-height: 1.3; margin-top: 5px; margin-bottom: 5px; }
        .dins { border-left: 3px solid; padding: 5px 7px; border-radius: 3px; font-size: 8px; line-height: 1.3; margin-top: 5px; }
        .di-e { border-color: #059669; background: #d1fae5; color: #065f46; }
        .di-h { border-color: #3b82f6; background: #dbeafe; color: #1e40af; }
        .di-a { border-color: #f59e0b; background: #fef3c7; color: #78350f; }
        .di-b { border-color: #f97316; background: #ffedd5; color: #9a3412; }
        .di-l { border-color: #ef4444; background: #fee2e2; color: #991b1b; }
        .pi-grid { display: table; width: 100%; border-collapse: separate; border-spacing: 6px 0; margin-top: 10px; }
        .pi-cell { display: table-cell; width: 25%; text-align: center; padding: 8px 5px; border-radius: 6px; border: 1px solid #e5e7eb; background: #f9fafb; vertical-align: top; }
        .pi-e { border-color: #a7f3d0; background: #ecfdf5; }
        .pi-h { border-color: #bfdbfe; background: #eff6ff; }
        .pi-a { border-color: #fde68a; background: #fffbeb; }
        .pi-b { border-color: #fed7aa; background: #fff7ed; }
        .pi-l { border-color: #fecaca; background: #fef2f2; }
        .pi-value { font-size: 18px; font-weight: 700; }
        .pi-label { font-size: 8px; font-weight: 600; color: #6b7280; margin-top: 3px; }
        .hc-grid { display: table; width: 100%; border-collapse: separate; border-spacing: 5px 0; margin-bottom: 10px; }
        .hc-cell { display: table-cell; width: 16.66%; text-align: center; padding: 8px 4px; border: 1px solid #e5e7eb; border-radius: 6px; background: #f9fafb; vertical-align: top; }
        .hc-e { border-color: #a7f3d0; background: #f0fdf4; }
        .hc-h { border-color: #bfdbfe; background: #eff6ff; }
        .hc-a { border-color: #fde68a; background: #fffbeb; }
        .hc-b { border-color: #fed7aa; background: #fff7ed; }
        .hc-l { border-color: #fecaca; background: #fef2f2; }
        .hc-letter { display: inline-block; width: 22px; height: 22px; line-height: 22px; border-radius: 50%; font-size: 13px; font-weight: 700; background: #eef2ff; color: #4f46e5; margin-bottom: 3px; }
        .hc-name { font-size: 8px; font-weight: 600; color: #111827; margin-bottom: 2px; }
        .hc-score { font-size: 13px; font-weight: 700; }
        .box-g { background: #ecfdf5; border: 1px solid #a7f3d0; border-radius: 6px; padding: 10px; margin-bottom: 8px; }
        .box-g h4 { font-size: 10px; font-weight: 700; color: #065f46; margin-bottom: 6px; }
        .bgi { font-size: 9px; color: #047857; margin-bottom: 3px; padding-left: 14px; position: relative; }
        .bgi:before { content: "\2713"; position: absolute; left: 0; font-weight: 700; color: #059669; }
        .box-t { background: #f0fdfa; border: 1px solid #99f6e4; border-radius: 6px; padding: 10px; margin-bottom: 8px; }
        .box-t h4 { font-size: 10px; font-weight: 700; color: #0f766e; margin-bottom: 6px; }
        .bti { font-size: 9px; color: #0f766e; margin-bottom: 3px; padding-left: 14px; position: relative; }
        .bti:before { content: "\2713"; position: absolute; left: 0; font-weight: 700; color: #059669; }
        .cog-grid { display: table; width: 100%; border-collapse: separate; border-spacing: 6px 0; margin-bottom: 6px; }
        .cog-c { display: table-cell; width: 50%; vertical-align: top; }
        .str-row { display: table; width: 100%; border-collapse: separate; border-spacing: 6px 0; margin-bottom: 6px; }
        .str-c { display: table-cell; width: 33.33%; border: 1px solid #d1d5db; border-radius: 6px; padding: 8px; background: #f9fafb; vertical-align: top; }
        .str-b { border-color: #bfdbfe; background: #eff6ff; }
        .str-g { border-color: #a7f3d0; background: #ecfdf5; }
        .str-p { border-color: #ddd6fe; background: #f5f3ff; }
        .slabel { font-size: 7px; font-weight: 700; text-transform: uppercase; margin-bottom: 3px; }
        .str-b .slabel { color: #1d4ed8; } .str-g .slabel { color: #047857; } .str-p .slabel { color: #5b21b6; }
        .spct { font-size: 16px; font-weight: 700; }
        .str-b .spct { color: #3b82f6; } .str-g .spct { color: #059669; } .str-p .spct { color: #7c3aed; }
        .sname { font-size: 9px; font-weight: 600; color: #111827; margin-top: 2px; }
        .sbar { width: 100%; height: 4px; background: #e5e7eb; border-radius: 3px; margin-top: 5px; overflow: hidden; }
        .sbar-b { background: #3b82f6; height: 100%; border-radius: 3px; }
        .sbar-g { background: #059669; height: 100%; border-radius: 3px; }
        .sbar-p { background: #7c3aed; height: 100%; border-radius: 3px; }
        .da-row { background: #f9fafb; border: 1px solid #e5e7eb; border-radius: 6px; padding: 7px 9px; margin-bottom: 5px; }
        .da-top { display: table; width: 100%; margin-bottom: 4px; }
        .da-name { display: table-cell; font-size: 9px; font-weight: 600; color: #111827; }
        .da-pct { display: table-cell; text-align: right; font-size: 9px; font-weight: 700; }
        .sbar-a { background: #f59e0b; height: 100%; border-radius: 3px; }
        .sbar-o { background: #f97316; height: 100%; border-radius: 3px; }
        .sbar-r { background: #ef4444; height: 100%; border-radius: 3px; }
        .box-am { background: #fffbeb; border: 1px solid #fde68a; border-radius: 6px; padding: 10px; margin-top: 8px; }
        .box-am h4 { font-size: 10px; font-weight: 700; color: #78350f; margin-bottom: 6px; }
        .bai { font-size: 9px; color: #92400e; padding-left: 14px; position: relative; margin-bottom: 3px; }
        .bai:before { content: "\2192"; position: absolute; left: 0; font-weight: 700; color: #f59e0b; }
        .cog-box { background: #f5f3ff; border: 1px solid #ddd6fe; border-radius: 6px; padding: 10px; margin-top: 4px; }
        .cog-box-title { font-size: 10px; font-weight: 700; color: #5b21b6; margin-bottom: 4px; }
        .cog-box-text { font-size: 9px; color: #6d28d9; }
        .footer { margin-top: 14px; padding-top: 8px; border-top: 1px solid #e5e7eb; text-align: center; font-size: 8px; color: #9ca3af; }
    </style>
</head>
<body>

@php
    /* Helper: map performance_level to single letter prefix */
    $c = function($pl) {
        return match($pl ?? 'average') {
            'exceptional'  => 'e',
            'high'         => 'h',
            'below-average'=> 'b',
            'low'          => 'l',
            default        => 'a',
        };
    };

    /* Learning styles (array of arrays from controller) */
    $lsStyles    = $learningStyles ?? [];
    $lsPreferred = count($lsStyles) > 0 ? $lsStyles[0]['name'] : null;

    /* Predictive insights: Collection keyed by result_type, each value is UserResult */
    $piKeys   = ['growth_potential','organizational_fit_forecast','leadership_potential','innovation_index'];
    $piLabels = [
        'growth_potential'            => 'Growth Potential',
        'organizational_fit_forecast' => 'Organizational Fit',
        'leadership_potential'        => 'Leadership Potential',
        'innovation_index'            => 'Innovation Index',
    ];
    $piData = $oceanResults['predictive_insights'] ?? [];

    /* Career analysis and work environment: single UserResult objects */
    $careerAnalysis = $riasecResults['career_analysis'] ?? null;
    $careersList    = $careerAnalysis
        ? array_slice(array_filter(array_map('trim', explode(';', $careerAnalysis->level_description ?? ''))), 0, 4)
        : [];

    $workEnv     = $riasecResults['work_environment'] ?? null;
    $workEnvList = $workEnv
        ? array_filter(array_map('trim', explode(';', $workEnv->level_description ?? '')))
        : [];

    /* Development Plan */
    $topOcean   = $oceanResults['domains']->sortByDesc('percentage')->first();
    $weakOcean  = $oceanResults['domains']->sortBy('percentage')->first();
    $topRiasec  = $riasecResults['domains']->sortByDesc('percentage')->first();
    $weakRiasec = $riasecResults['domains']->sortBy('percentage')->first();
    $topCog     = $cognitiveResults['domains']->sortByDesc('percentage')->first();
    $weakCog    = $cognitiveResults['domains']->sortBy('percentage')->first();

    $devAreas = collect([
        ['name' => $weakOcean->name,  'pct' => round($weakOcean->percentage),  'cls' => 'a'],
        ['name' => $weakRiasec->name, 'pct' => round($weakRiasec->percentage), 'cls' => 'o'],
        ['name' => $weakCog->name,    'pct' => round($weakCog->percentage),     'cls' => 'r'],
    ])->filter(fn($d) => $d['pct'] < 70)->values();

    $devColors = ['a' => '#f59e0b', 'o' => '#f97316', 'r' => '#ef4444'];
@endphp

<!-- HEADER -->
<div class="header">
    <h1>MetrixsMate Assessment Report</h1>
    <div class="sub">{{ $user->name }} &bull; {{ $generatedDate }}</div>
</div>

<!-- OVERVIEW -->
<div class="overview">
    <div class="ov-cell"><div class="ov-label">Personality Score</div><div class="ov-value ov-blue">{{ round($overallScore['personality'] ?? 0) }}%</div></div>
    <div class="ov-cell"><div class="ov-label">Holland Code</div><div class="ov-value ov-purple">{{ $riasecResults['holland_code'] ?? 'N/A' }}</div></div>
    <div class="ov-cell"><div class="ov-label">Cognitive Score</div><div class="ov-value ov-green">{{ round($overallScore['cognitive'] ?? 0) }}%</div></div>
</div>

<!-- CAREER + LEARNING STYLES -->
<div class="two-col">
    <div class="col-l">
        <div class="career-box">
            <h3>Career Opportunities</h3>
            <div class="cb-sub">Based on your {{ $riasecResults['holland_code'] ?? '' }} profile</div>
            @forelse($careersList as $career)
                <div class="career-item">{{ $career }}</div>
            @empty
                <div class="career-item">Career recommendations based on your profile</div>
            @endforelse
        </div>
    </div>
    <div class="col-r">
        <div class="learning-box">
            <h3>Learning Styles</h3>
            <div class="lb-sub">Your preferred way of acquiring information</div>
            <div class="ls-grid">
                @forelse($lsStyles as $style)
                    @php $isPref = ($style['name'] === $lsPreferred); @endphp
                    <div class="ls-cell {{ $isPref ? 'pref' : '' }}">
                        <div class="ls-name">{{ $style['name'] }}</div>
                        <div class="ls-score">{{ $style['score'] }}%</div>
                        <div class="pbar"><div class="pfill" style="width:{{ $style['score'] }}%;background:{{ $isPref ? '#059669' : '#9ca3af' }};"></div></div>
                    </div>
                @empty
                    <div class="ls-cell"><div class="ls-name">Visual</div><div class="ls-score">-</div></div>
                    <div class="ls-cell"><div class="ls-name">Verbal</div><div class="ls-score">-</div></div>
                    <div class="ls-cell"><div class="ls-name">Reading</div><div class="ls-score">-</div></div>
                    <div class="ls-cell"><div class="ls-name">Kinesthetic</div><div class="ls-score">-</div></div>
                @endforelse
            </div>
        </div>
    </div>
</div>

<!-- OCEAN PERSONALITY -->
<div class="section">
    <div class="section-head">Personality Profile (OCEAN)</div>
    <div class="section-sub">Your personality dimensions across five major factors</div>

    @foreach($oceanResults['domains'] as $domain)
        @php $k = $c($domain->performance_level); @endphp
        <div class="dcard dc-{{ $k }}">
            <div class="dh">
                <div class="dname">{{ $domain->name }}</div>
                <div class="dright">
                    <span class="dscore sc-{{ $k }}">{{ round($domain->percentage) }}%</span>
                    <span class="dbadge bd-{{ $k }}">{{ $domain->performance_text ?? ucfirst($domain->performance_level ?? 'average') }}</span>
                </div>
            </div>
            <div class="pbar"><div class="pfill pf-{{ $k }}" style="width:{{ $domain->percentage }}%"></div></div>
            @if($domain->description)
                <div class="ddesc">{{ $domain->description }}</div>
            @endif
            @if($domain->actionable_insights)
                <div class="dins di-{{ $k }}"><strong>Insight:</strong> {{ $domain->actionable_insights }}</div>
            @endif
        </div>
    @endforeach

    @if(count($piData) > 0)
    <div style="font-size:9px;font-weight:700;color:#374151;margin-top:8px;margin-bottom:6px;">Predictive Insights</div>
    <div class="pi-grid">
        @foreach($piKeys as $piKey)
            @if(isset($piData[$piKey]))
                @php $pi = $piData[$piKey]; $pk = $c($pi->performance_level); @endphp
                <div class="pi-cell pi-{{ $pk }}">
                    <div class="pi-value sc-{{ $pk }}">{{ round($pi->percentage) }}%</div>
                    <div class="pi-label">{{ $piLabels[$piKey] }}</div>
                </div>
            @endif
        @endforeach
    </div>
    @endif

    @php
        $pdfFacets   = $oceanResults['facets'] ?? collect();
        $pdfCcsSkills = $oceanResults['ccs_skills'] ?? collect();
    @endphp

    @if($pdfFacets->count() > 0)
    <div style="font-size:9px;font-weight:700;color:#374151;margin-top:10px;margin-bottom:6px;text-transform:uppercase;border-top:1px solid #e5e7eb;padding-top:8px;">Personality Facets</div>
    @for($fi = 0; $fi < ceil($pdfFacets->count() / 4); $fi++)
        <div style="display:table;width:100%;border-collapse:separate;border-spacing:5px 0;margin-bottom:5px;">
            @for($fj = 0; $fj <= 3; $fj++)
                @php $fidx = $fi * 4 + $fj; $facet = $pdfFacets->get($fidx); @endphp
                @if($facet)
                    @php $fk = $c($facet->performance_level); @endphp
                    <div style="display:table-cell;width:25%;text-align:center;padding:6px 4px;border-radius:5px;vertical-align:top;" class="dc-{{ $fk }}">
                        <div style="font-size:7.5px;color:#374151;font-weight:600;margin-bottom:2px;">{{ $facet->name }}</div>
                        <div class="sc-{{ $fk }}" style="font-size:13px;font-weight:700;">{{ round($facet->percentage) }}%</div>
                        <div class="pbar" style="margin-top:3px;"><div class="pfill pf-{{ $fk }}" style="width:{{ $facet->percentage }}%"></div></div>
                        <div class="sc-{{ $fk }}" style="font-size:7px;margin-top:2px;">{{ $facet->performance_text }}</div>
                    </div>
                @else
                    <div style="display:table-cell;width:25%;"></div>
                @endif
            @endfor
        </div>
    @endfor
    @endif

    @if($pdfCcsSkills->count() > 0)
    <div style="font-size:9px;font-weight:700;color:#374151;margin-top:10px;margin-bottom:6px;text-transform:uppercase;border-top:1px solid #e5e7eb;padding-top:8px;">Core Character Strengths</div>
    @for($ci = 0; $ci < ceil($pdfCcsSkills->count() / 4); $ci++)
        <div style="display:table;width:100%;border-collapse:separate;border-spacing:5px 0;margin-bottom:5px;">
            @for($cj = 0; $cj <= 3; $cj++)
                @php $cidx = $ci * 4 + $cj; $skill = $pdfCcsSkills->get($cidx); @endphp
                @if($skill)
                    @php $ck = $c($skill->performance_level); @endphp
                    <div style="display:table-cell;width:25%;text-align:center;padding:6px 4px;border-radius:5px;vertical-align:top;" class="dc-{{ $ck }}">
                        <div style="font-size:7.5px;color:#374151;font-weight:600;margin-bottom:2px;">{{ $skill->name }}</div>
                        <div class="sc-{{ $ck }}" style="font-size:13px;font-weight:700;">{{ round($skill->percentage) }}%</div>
                        <div class="pbar" style="margin-top:3px;"><div class="pfill pf-{{ $ck }}" style="width:{{ $skill->percentage }}%"></div></div>
                        <div class="sc-{{ $ck }}" style="font-size:7px;margin-top:2px;">{{ $skill->performance_text }}</div>
                    </div>
                @else
                    <div style="display:table-cell;width:25%;"></div>
                @endif
            @endfor
        </div>
    @endfor
    @endif
</div>

<!-- RIASEC CAREER PROFILE -->
<div class="section">
    <div class="section-head">RIASEC Career Profile</div>
    <div class="section-sub">Your career interests across six professional dimensions</div>

    <div class="hc-grid">
        @foreach($riasecResults['domains'] as $domain)
            @php
                $k      = $c($domain->performance_level);
                $nameEn = $domain->name_en ?? $domain->name;
                $letter = strtoupper(substr($nameEn, 0, 1));
            @endphp
            <div class="hc-cell hc-{{ $k }}">
                <div class="hc-letter">{{ $letter }}</div>
                <div class="hc-name">{{ $domain->name }}</div>
                <div class="hc-score sc-{{ $k }}">{{ round($domain->percentage) }}%</div>
                <div class="pbar" style="margin-top:4px"><div class="pfill pf-{{ $k }}" style="width:{{ $domain->percentage }}%"></div></div>
            </div>
        @endforeach
    </div>

    @if(count($careersList) > 0)
    <div class="box-g">
        <h4>Career Paths</h4>
        @foreach($careersList as $career)
            <div class="bgi">{{ $career }}</div>
        @endforeach
    </div>
    @endif

    @if(count($workEnvList) > 0)
    <div class="box-t">
        <h4>Work Environment Preferences</h4>
        @foreach($workEnvList as $env)
            <div class="bti">{{ $env }}</div>
        @endforeach
    </div>
    @endif
</div>

<!-- STREAM RECOMMENDATIONS -->
@if(!empty($streamRecommendations))
<div class="section">
    <div class="section-head">Recommended Academic Streams</div>
    <div class="section-sub">Based on your personality, career interests, and cognitive profile</div>

    <div style="display:table;width:100%;border-collapse:separate;border-spacing:0 5px;">
        @foreach($streamRecommendations as $sr)
        @php
            $srColors = [
                'blue'   => ['bg' => '#eff6ff', 'border' => '#bfdbfe', 'text' => '#1d4ed8', 'bar' => '#3b82f6'],
                'green'  => ['bg' => '#f0fdf4', 'border' => '#bbf7d0', 'text' => '#15803d', 'bar' => '#22c55e'],
                'amber'  => ['bg' => '#fffbeb', 'border' => '#fde68a', 'text' => '#92400e', 'bar' => '#f59e0b'],
                'purple' => ['bg' => '#faf5ff', 'border' => '#e9d5ff', 'text' => '#6b21a8', 'bar' => '#a855f7'],
                'orange' => ['bg' => '#fff7ed', 'border' => '#fed7aa', 'text' => '#9a3412', 'bar' => '#f97316'],
            ];
            $sc = $srColors[$sr['color']] ?? $srColors['blue'];
            $badgeColors = [
                'strongly_recommended' => ['bg' => '#d1fae5', 'text' => '#065f46'],
                'recommended'          => ['bg' => '#dbeafe', 'text' => '#1e40af'],
                'suitable'             => ['bg' => '#fef3c7', 'text' => '#78350f'],
                'less_suitable'        => ['bg' => '#f3f4f6', 'text' => '#374151'],
            ];
            $bc = $badgeColors[$sr['recommendation']] ?? $badgeColors['suitable'];
        @endphp
        <div style="display:table-row;">
            <div style="display:table-cell;width:100%;background:{{ $sc['bg'] }};border:1px solid {{ $sc['border'] }};border-radius:6px;padding:8px 10px;margin-bottom:4px;vertical-align:top;">
                <div style="display:table;width:100%;margin-bottom:3px;">
                    <div style="display:table-cell;font-size:9px;font-weight:700;color:#111827;">{{ $sr['name'] }}</div>
                    <div style="display:table-cell;text-align:right;font-size:9px;font-weight:700;color:{{ $sc['text'] }};">{{ $sr['score'] }}%</div>
                    <div style="display:table-cell;text-align:right;width:80px;padding-left:6px;">
                        <span style="font-size:7.5px;font-weight:700;background:{{ $bc['bg'] }};color:{{ $bc['text'] }};padding:1px 6px;border-radius:8px;">{{ $sr['recommendation_label'] }}</span>
                    </div>
                </div>
                <div style="width:100%;height:3px;background:#e5e7eb;border-radius:3px;margin-bottom:4px;overflow:hidden;">
                    <div style="width:{{ $sr['score'] }}%;height:100%;background:{{ $sc['bar'] }};border-radius:3px;"></div>
                </div>
                <div style="font-size:7.5px;color:#6b7280;margin-bottom:4px;">{{ $sr['description'] }}</div>
                <div style="font-size:7px;color:{{ $sc['text'] }};">{{ implode(' &bull; ', $sr['careers']) }}</div>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endif

<!-- COGNITIVE ABILITIES -->
<div class="section">
    <div class="section-head">Cognitive Abilities</div>
    <div class="section-sub">Your cognitive strengths across different dimensions</div>

    @php $cogDomains = $cognitiveResults['domains']->values(); @endphp
    @for($i = 0; $i < ceil($cogDomains->count() / 2); $i++)
        <div class="cog-grid">
            @for($j = 0; $j <= 1; $j++)
                @php $idx = $i * 2 + $j; $cog = $cogDomains->get($idx); @endphp
                @if($cog)
                    @php $k = $c($cog->performance_level); @endphp
                    <div class="cog-c">
                        <div class="dcard dc-{{ $k }}">
                            <div class="dh">
                                <div class="dname">{{ $cog->name }}</div>
                                <div class="dright">
                                    <span class="dscore sc-{{ $k }}">{{ round($cog->percentage) }}%</span>
                                    <span class="dbadge bd-{{ $k }}">{{ $cog->performance_text ?? ucfirst($cog->performance_level ?? 'average') }}</span>
                                </div>
                            </div>
                            <div class="pbar"><div class="pfill pf-{{ $k }}" style="width:{{ $cog->percentage }}%"></div></div>
                            @if($cog->description)
                                <div class="ddesc">{{ $cog->description }}</div>
                            @endif
                        </div>
                    </div>
                @else
                    <div class="cog-c"></div>
                @endif
            @endfor
        </div>
    @endfor

    @php
        $cogLevel  = $cognitiveResults['domains']->where('percentage', '>=', 85)->count() > 0 ? 'exceptional'
            : ($cognitiveResults['domains']->where('percentage', '>=', 70)->count() > 0 ? 'strong' : 'good');
        $topTwoCog = $cognitiveResults['domains']->sortByDesc('percentage')->take(2)->pluck('name')->implode(', ');
    @endphp
    <div class="cog-box">
        <div class="cog-box-title">Cognitive Profile</div>
        <div class="cog-box-text">
            Your cognitive profile shows <strong>{{ $cogLevel }}</strong> performance. <strong>Top strengths:</strong> {{ $topTwoCog }}
        </div>
    </div>
</div>

<!-- DEVELOPMENT PLAN -->
<div class="section">
    <div class="section-head">Learning &amp; Development Plan</div>
    <div class="section-sub">Personalised recommendations based on your complete assessment results</div>

    <div style="font-size:9px;font-weight:700;text-transform:uppercase;color:#374151;margin-bottom:6px;">Key Strengths</div>
    <div class="str-row">
        <div class="str-c str-b">
            <div class="slabel">Personality</div>
            <div class="spct">{{ round($topOcean->percentage) }}%</div>
            <div class="sname">{{ $topOcean->name }}</div>
            <div class="sbar"><div class="sbar-b" style="width:{{ $topOcean->percentage }}%"></div></div>
        </div>
        <div class="str-c str-g">
            <div class="slabel">Career</div>
            <div class="spct">{{ round($topRiasec->percentage) }}%</div>
            <div class="sname">{{ $topRiasec->name }}</div>
            <div class="sbar"><div class="sbar-g" style="width:{{ $topRiasec->percentage }}%"></div></div>
        </div>
        <div class="str-c str-p">
            <div class="slabel">Cognitive</div>
            <div class="spct">{{ round($topCog->percentage) }}%</div>
            <div class="sname">{{ $topCog->name }}</div>
            <div class="sbar"><div class="sbar-p" style="width:{{ $topCog->percentage }}%"></div></div>
        </div>
    </div>

    @if($devAreas->count() > 0)
    <div style="font-size:9px;font-weight:700;text-transform:uppercase;color:#374151;margin-bottom:6px;margin-top:10px;">Development Areas</div>
    @foreach($devAreas as $da)
    <div class="da-row">
        <div class="da-top">
            <div class="da-name">{{ $da['name'] }}</div>
            <div class="da-pct" style="color:{{ $devColors[$da['cls']] }}">{{ $da['pct'] }}%</div>
        </div>
        <div class="sbar"><div class="sbar-{{ $da['cls'] }}" style="width:{{ $da['pct'] }}%"></div></div>
    </div>
    @endforeach
    @endif

    <div class="box-am">
        <h4>Recommended Development Path</h4>
        <div class="bai">Build skills aligned with your <strong>{{ $riasecResults['holland_code'] ?? '' }}</strong> career profile</div>
        <div class="bai">Leverage your <strong>{{ $topOcean->name }}</strong> personality strength in team environments</div>
        <div class="bai">Focus on developing <strong>{{ $weakOcean->name }}</strong> as a growth opportunity</div>
        <div class="bai">Seek learning experiences that match your <strong>{{ $lsPreferred ?? 'preferred' }}</strong> learning style</div>
        @if(count($careersList) > 0)
        <div class="bai">Explore careers: {{ implode(', ', array_slice($careersList, 0, 2)) }}</div>
        @endif
    </div>
</div>

<div class="footer">
    MetrixsMate Assessment Report &bull; {{ now()->format('F j, Y') }} &bull; Confidential
</div>

</body>
</html>
