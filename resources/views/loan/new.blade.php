@extends('layouts.app')

@section('title', 'New loan')

@section('content')
<form method="post" action="{{ route('loans.store') }}">
    @csrf
    <div class="form-group">
        <label for="name">Name</label>
        <input type="text" class="form-control" id="name" name="name">
    </div>
    <div class="form-group">
        <label for="room">Room (e.g. 104)</label>
        <input type="number" class="form-control" id="room" name="room">
    </div>
    <div class="form-group">
        <p>Assets will be added in the next step.</p>
    </div>
    <button type="submit" class="btn btn-primary">Create loan</button>
</form>
@endsection