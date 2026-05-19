@extends('layouts.assist')

@section('title', 'Home')

@section('content')
    @include('assist.sections.hero')
    @include('assist.sections.editing-engine')
    @include('assist.sections.features')
    @include('assist.sections.workspace')
    @include('assist.sections.interoperability')
@endsection
