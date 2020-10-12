@extends('layouts.app')

@section('title', 'Show loan')

@section('content')
<h3>Showing loan (ID <i>{{ config('app.conventions.loan_prefix') }}{{ $loan->id }}</i>)
@can('update', $loan)
    <a href="{{ route('loans.edit', $loan->id) }}" class="btn btn-secondary btn-sm"><i class="fa fa-pencil"></i></a>
@endcan
</h3>

<p><i>Issued by {{ $loan->issuer->name }}</i></p>

<p>Borrower name: {{ $loan->borrower_name }}</p>
<p>Borrower room: {{ $loan->borrower_room }}</p>
<p>Comment: {{ $loan->comment }}</p>
<p>Status: {{ $loan->getStatusText() }}</p>
   
<hr>

<h3>Assets</h3>
@forelse($loan->assets as $asset)
    @if($loop->first) <ul> @endif
        <li>{{ $asset->getNamesString() }}</li>
    @if($loop->last) </ul> @endif
@empty
<div class="alert alert-info">
    No assets are part of this transaction yet.
</div>
@endforelse

@endsection