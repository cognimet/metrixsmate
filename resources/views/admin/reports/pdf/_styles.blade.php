{{-- Shared CSS styles for admin report PDFs (DomPDF-compatible) --}}
<style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { font-family: DejaVu Sans, sans-serif; font-size: 9.5px; color: #1f2937; background: #fff; padding: 16px; line-height: 1.5; }

    /* Header */
    .report-header { text-align: center; margin-bottom: 16px; padding: 18px 14px; border-radius: 0; border-bottom: 3px solid #4f46e5; }
    .report-header h1 { font-size: 22px; font-weight: 700; color: #111827; margin-bottom: 2px; }
    .report-header .brand { font-size: 10px; color: #6366f1; font-weight: 600; letter-spacing: 2px; text-transform: uppercase; margin-bottom: 6px; }
    .report-header .meta { font-size: 9px; color: #6b7280; }
    .report-header .institution { font-size: 14px; font-weight: 600; color: #4f46e5; margin-top: 4px; }
    .report-header .badge { display: inline-block; font-size: 8px; font-weight: 700; padding: 2px 10px; border-radius: 10px; color: #fff; margin-top: 6px; }
    .badge-school { background: #059669; }
    .badge-university { background: #4f46e5; }
    .badge-company { background: #dc2626; }

    /* Section */
    .section { margin-bottom: 14px; page-break-inside: avoid; }
    .section-head { font-size: 13px; font-weight: 700; color: #111827; border-bottom: 2px solid #e5e7eb; padding-bottom: 4px; margin-bottom: 8px; }
    .section-sub { font-size: 8.5px; color: #6b7280; margin-bottom: 8px; }

    /* Executive summary cards */
    .exec-grid { display: table; width: 100%; border-collapse: separate; border-spacing: 6px 0; margin-bottom: 12px; }
    .exec-cell { display: table-cell; text-align: center; padding: 10px 6px; border-radius: 8px; border: 1px solid #e5e7eb; background: #f9fafb; vertical-align: top; }
    .exec-label { font-size: 8px; font-weight: 600; color: #6b7280; text-transform: uppercase; margin-bottom: 3px; }
    .exec-value { font-size: 22px; font-weight: 700; }
    .v-blue { color: #4f46e5; }
    .v-green { color: #059669; }
    .v-purple { color: #7c3aed; }
    .v-amber { color: #d97706; }
    .v-red { color: #dc2626; }

    /* Progress bar */
    .pbar { height: 5px; background: #e5e7eb; border-radius: 3px; overflow: hidden; margin-top: 3px; }
    .pfill { height: 100%; border-radius: 3px; }
    .pf-e { background: #059669; } .pf-h { background: #3b82f6; } .pf-a { background: #f59e0b; } .pf-b { background: #f97316; } .pf-l { background: #ef4444; }

    /* Domain cards */
    .dc { border: 1px solid #e5e7eb; border-radius: 6px; padding: 8px 10px; margin-bottom: 6px; page-break-inside: avoid; }
    .dc-e { border-left: 4px solid #059669; background: #f0fdf4; }
    .dc-h { border-left: 4px solid #3b82f6; background: #eff6ff; }
    .dc-a { border-left: 4px solid #f59e0b; background: #fffbeb; }
    .dc-b { border-left: 4px solid #f97316; background: #fff7ed; }
    .dc-l { border-left: 4px solid #ef4444; background: #fef2f2; }
    .dh { display: table; width: 100%; margin-bottom: 3px; }
    .dname { display: table-cell; font-size: 10px; font-weight: 600; color: #111827; vertical-align: middle; }
    .dright { display: table-cell; text-align: right; vertical-align: middle; }
    .dscore { font-size: 14px; font-weight: 700; }
    .dbadge { font-size: 7px; font-weight: 600; padding: 1px 6px; border-radius: 10px; margin-left: 3px; }
    .sc-e { color: #059669; } .bd-e { background: #d1fae5; color: #047857; }
    .sc-h { color: #3b82f6; } .bd-h { background: #dbeafe; color: #1d4ed8; }
    .sc-a { color: #f59e0b; } .bd-a { background: #fef3c7; color: #b45309; }
    .sc-b { color: #f97316; } .bd-b { background: #ffedd5; color: #c2410c; }
    .sc-l { color: #ef4444; } .bd-l { background: #fee2e2; color: #b91c1c; }
    .dstats { font-size: 8px; color: #6b7280; margin-top: 2px; }

    /* Two-column layout */
    .two-col { display: table; width: 100%; border-collapse: separate; border-spacing: 8px 0; margin-bottom: 10px; }
    .col-l { display: table-cell; width: 50%; vertical-align: top; }
    .col-r { display: table-cell; width: 50%; vertical-align: top; }

    /* Three-column layout */
    .three-col { display: table; width: 100%; border-collapse: separate; border-spacing: 6px 0; margin-bottom: 10px; }
    .col-3 { display: table-cell; width: 33.33%; vertical-align: top; }

    /* Info box */
    .info-box { border-radius: 6px; padding: 10px; margin-bottom: 8px; }
    .ib-green  { background: #ecfdf5; border: 1px solid #a7f3d0; }
    .ib-blue   { background: #eff6ff; border: 1px solid #bfdbfe; }
    .ib-amber  { background: #fffbeb; border: 1px solid #fde68a; }
    .ib-red    { background: #fef2f2; border: 1px solid #fecaca; }
    .ib-purple { background: #f5f3ff; border: 1px solid #ddd6fe; }
    .ib-title { font-size: 10px; font-weight: 700; margin-bottom: 4px; }
    .ib-text { font-size: 8.5px; line-height: 1.4; }
    .ib-green .ib-title { color: #065f46; } .ib-green .ib-text { color: #047857; }
    .ib-blue .ib-title { color: #1e40af; } .ib-blue .ib-text { color: #1d4ed8; }
    .ib-amber .ib-title { color: #78350f; } .ib-amber .ib-text { color: #92400e; }
    .ib-red .ib-title { color: #991b1b; } .ib-red .ib-text { color: #b91c1c; }
    .ib-purple .ib-title { color: #5b21b6; } .ib-purple .ib-text { color: #6d28d9; }

    /* Table */
    .dtable { width: 100%; border-collapse: collapse; margin-bottom: 10px; font-size: 8.5px; }
    .dtable th { background: #f3f4f6; color: #374151; font-weight: 700; padding: 5px 8px; text-align: left; border-bottom: 2px solid #d1d5db; font-size: 8px; text-transform: uppercase; }
    .dtable td { padding: 5px 8px; border-bottom: 1px solid #e5e7eb; color: #4b5563; }
    .dtable tr:nth-child(even) td { background: #f9fafb; }

    /* Distribution bar (horizontal stacked) */
    .dist-bar { display: table; width: 100%; height: 16px; border-radius: 4px; overflow: hidden; margin: 6px 0; }
    .dist-seg { display: table-cell; height: 100%; }
    .ds-e { background: #059669; } .ds-h { background: #3b82f6; } .ds-a { background: #f59e0b; } .ds-b { background: #f97316; } .ds-l { background: #ef4444; }

    /* Legends */
    .legend { display: table; width: 100%; margin-bottom: 8px; }
    .legend-item { display: table-cell; text-align: center; font-size: 7.5px; color: #6b7280; }
    .legend-dot { display: inline-block; width: 8px; height: 8px; border-radius: 50%; vertical-align: middle; margin-right: 3px; }
    .ld-e { background: #059669; } .ld-h { background: #3b82f6; } .ld-a { background: #f59e0b; } .ld-b { background: #f97316; } .ld-l { background: #ef4444; }

    /* Action items */
    .action-item { padding: 6px 8px; margin-bottom: 5px; border-radius: 4px; font-size: 8.5px; }
    .action-high { background: #fef2f2; border-left: 3px solid #ef4444; color: #991b1b; }
    .action-medium { background: #fffbeb; border-left: 3px solid #f59e0b; color: #78350f; }
    .action-low { background: #f0fdf4; border-left: 3px solid #059669; color: #065f46; }
    .action-label { font-size: 7px; font-weight: 700; text-transform: uppercase; margin-bottom: 2px; }

    /* Performer card */
    .perf-card { display: table-cell; width: 20%; text-align: center; padding: 6px 4px; border: 1px solid #e5e7eb; border-radius: 6px; background: #f9fafb; vertical-align: top; }
    .perf-name { font-size: 8px; font-weight: 600; color: #111827; margin-bottom: 2px; overflow: hidden; }
    .perf-score { font-size: 16px; font-weight: 700; }

    /* Footer */
    .footer { margin-top: 14px; padding-top: 8px; border-top: 2px solid #e5e7eb; text-align: center; }
    .footer-brand { font-size: 10px; font-weight: 700; color: #4f46e5; }
    .footer-text { font-size: 7.5px; color: #9ca3af; margin-top: 2px; }
    .footer-conf { font-size: 7px; color: #d1d5db; margin-top: 4px; font-style: italic; }
</style>
