@extends('layouts.app')

@section('title', 'All assets')

@section('content')
<h3>All assets</h3>

<h5>Search</h5>
<form action="{{ route('assets.show_by_search') }}" method="get" class="form-inline">
    @include('includes.asset_search_field')
    <button type="submit" class="btn btn-primary mb-2">Search</button>
</form>

<h5>Catalog (work in progress)</h5>


@endsection