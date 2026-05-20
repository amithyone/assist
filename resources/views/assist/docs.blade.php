@extends('layouts.assist')

@section('content')
@php $i = $intro ?? []; @endphp
<div class="assist-docs-layout">
    <aside class="assist-docs-sidebar">
        <input type="search" class="assist-input" placeholder="Search docs..." style="margin-bottom: 24px;" disabled title="Search coming soon">
        @include('assist.docs._sidebar')
    </aside>
    <article class="assist-docs-content">
        <p class="assist-eyebrow">{{ $i['eyebrow'] ?? 'Docs' }} &rsaquo; Overview</p>
        <h1>{{ $i['heading'] ?? 'Assist documentation' }}</h1>
        @if (!empty($i['body_html']))
            <div class="assist-docs-intro">{!! $i['body_html'] !!}</div>
        @else
            <p>{{ $i['subheading'] ?? 'Everything you need to install Assist, connect DaVinci Resolve, run automated assemblies, and stay in creative control.' }}</p>
        @endif

        @include('assist.docs._content')

        <p class="assist-text-muted" style="margin-top: 48px; font-size: 11px;">
            Last updated: May 2026 · {{ config('assist.company_name', 'Amithyone Media') }}
        </p>
    </article>
</div>
@endsection
