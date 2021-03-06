@extends('layouts.app')

@section('title', 'All loans')

@section('content')
<h3>Recent loans</h3>
<h5>ordered by modification date</h5>
<table class="table table-sm">
    <thead>
        <tr>
            <th scope="col">ID</th>
            <th scope="col">Name</th>
            <th scope="col">Room</th>
            <th scope="col">Status</th>
        </tr>
    </thead>
    <tbody class="table-hover">
    @foreach($loans as $loan)
        <tr data-href="{{ route('loans.show', $loan->id) }}" class="loan-status-color-{{ $loan->getStatus() }}">
            <td>{{ config('app.conventions.loan_prefix') }}{{ $loan->id }}</td>
            <td>{{ $loan->borrower_name }}</td>
            <td>{{ $loan->borrower_room }}</td>
            <td>{{ $loan->getStatusText() }}</td>
        </tr>
    @endforeach
    </tbody>
</table>

@endsection