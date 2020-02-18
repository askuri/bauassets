@extends('layouts.app')

@section('title', 'Asset Details')

@section('content')
<h3>Asset Details</h3>
<p>Category: {{ $asset->category->name }}</p>
<p>Stock (total): {{ $asset->stock }}</p>
<p>Location: {{ $asset->location }}</p>

<h5>Names</h5>
<ul>
@foreach($asset->assetnames as $name)
    <li>{{ $name->name }}</li>
@endforeach
</ul>

<h5>Recently part of loans ...</h5>

<table class="table table-sm">
    <thead>
        <tr>
            <th scope="col">Borrower name</th>
            <th scope="col">Borrower room</th>
            <th scope="col">Issuer</th>
            <th scope="col">Status</th>
        </tr>
    </thead>
    <tbody class="table-hover">
@foreach($asset->loans as $loan)
{{-- todo: this makes way too many requests to the policy. pull this code out of foreach --}}
    @can('update', $loan)
        <tr data-href="{{ route('loans.edit', $loan->id) }}" class="loan-status-color-{{ $loan->getStatus() }}">
    @else
        <tr data-href="{{ route('loans.edit', $loan->id) }}" class="loan-status-color-{{ $loan->getStatus() }}">
    @endcan
            <td>{{ $loan->borrower_name }}</td>
            <td>{{ $loan->borrower_room }}</td>
            <td>{{ $loan->issuer->name }}</td>
            <td>{{ $loan->getStatusText() }}</td>
        </tr>
@endforeach
    </tbody>
</table>

@endsection