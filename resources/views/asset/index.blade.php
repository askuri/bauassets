@extends('layouts.app')

@section('title', 'All assets')

@section('content')
<h3>All assets</h3>

<h5>Search</h5>
<form action="{{ route('assets.show_by_search') }}" method="get" class="form-inline">
    @include('includes.asset_search_field')
    <button type="submit" class="btn btn-primary mb-2">Search</button>
</form>

<h5>Catalog</h5>
<table class="table table-sm">
    <thead>
        <tr>
            <th scope="col">Names</th>
            <th scope="col">Category</th>
            <th scope="col">Location</th>
            <th scope="col">Stock (total)</th>
        </tr>
    </thead>
    <tbody class="table-hover">
    @foreach($assets as $asset)
        <tr data-href="{{ route('assets.show', $asset->id) }}">
            <td>{{ $asset->getNamesString() }}</td>
            <td>{{ $asset->category->name }}</td>
            <td>{{ $asset->location }}</td>
            <td>{{ $asset->stock }}</td>
        </tr>
    @endforeach
    </tbody>
</table>

@endsection