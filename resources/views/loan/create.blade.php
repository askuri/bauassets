@extends('layouts.app')

@section('title', 'New loan')

@section('content')
<form method="post" action="{{ route('loans.store') }}">
    @csrf
    <div class="form-group">
        <label for="borrower_name">Name</label>
        <input type="text" class="form-control" id="borrower_name" name="borrower_name" required>
    </div>
    <div class="form-group">
        <label for="borrower_room">Room (e.g. 104)</label>
        <input type="number" class="form-control" id="borrower_room" name="borrower_room" required>
    </div>
    <div class="form-group">
        <label for="borrower_email">E-Mail (used to send confirmation)</label>
        <input type="borrower_email" class="form-control" id="borrower_email" name="borrower_email" required>
    </div>
    <div class="form-group">
        <p>Assets will be added in the next step.</p>
    </div>
    <button type="submit" class="btn btn-primary">Create loan</button>
</form>
@endsection