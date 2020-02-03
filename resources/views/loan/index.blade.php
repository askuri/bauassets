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
{{-- todo: this makes way too many requests to the policy. pull this code out of foreach --}}
    @can('update', $loan)
        <tr data-href="{{ route('loans.edit', $loan->id) }}" class="loan-status-color-{{ $loan->getStatus() }}">
    @else
        <tr data-href="{{ route('loans.show', $loan->id) }}" class="loan-status-color-{{ $loan->getStatus() }}">
    @endcan
            <td>{{ config('app.conventions.loan_prefix') }}{{ $loan->id }}</td>
            <td>{{ $loan->borrower_name }}</td>
            <td>{{ $loan->borrower_room }}</td>
            <td>{{ $loan->getStatusText() }}</td>
        </tr>
@endforeach
    </tbody>
</table>

<script>
    /*
     * Make whole row clickable by adding data-href attribute to it
     */
$(function(){
    $('.table tr[data-href]').each(function(){
        $(this).css('cursor','pointer').hover(
            function(){ 
                $(this).addClass('active'); 
            },  
            function(){ 
                $(this).removeClass('active'); 
            }).click( function(){ 
                document.location = $(this).attr('data-href'); 
            }
        );
    });
});
</script>
@endsection