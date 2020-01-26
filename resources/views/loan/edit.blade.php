@extends('layouts.app')

@section('title', 'New loan')

@section('content')
<h3>Edit loan (ID <i>{{ config('app.conventions.loan_prefix') }}{{ $loan->id }}</i>)</h3>
<form method="post" action="{{ route('loans.update', $loan->id) }}">
    @csrf
    @method('PUT')
    <div class="form-group">
        <label for="name">Name</label>
        <input type="text" class="form-control" id="name" name="borrower_name" value="{{ $loan->borrower_name }}" required>
    </div>
    <div class="form-group">
        <label for="room">Room (e.g. 104)</label>
        <input type="number" class="form-control" id="room" name="borrower_room" value="{{ $loan->borrower_room }}" required>
    </div>
    <div class="form-group">
        <label for="email">E-Mail (used to send confirmation)</label>
        <input type="email" class="form-control" id="email" name="borrower_email" value="{{ $loan->borrower_email }}" required>
    </div>
    <div class="form-group">
        <label for="comment">Comment</label>
        <textarea type="text" class="form-control" id="comment" name="comment">{{ $loan->comment }}</textarea>
    </div>
    
    <!-- status -->
    <div class="alert alert-warning">
        <p>Danger Zone: changes cannot be undone!</p>
        <p>To change status, please tick the box and click update below.</p>
        
        <div class="form-check">
            <input name="statusupdate_create" value="true" class="form-check-input" type="checkbox" id="statusupdate_create" disabled checked>
            <label class="form-check-label" for="statusupdate_create">
                Created <span class="badge badge-light">{{ $loan->created_at }}</span>
            </label>
        </div>
        <div class="form-check">
            <input name="statusupdate_hand_out" value="true" class="form-check-input" type="checkbox" id="statusupdate_hand_out"
                    {{ $loan->getStatus() == App\Loan::STATUS_HANDED_OUT-1 ? '' : 'disabled' }}
                    {{ $loan->getStatus() < App\Loan::STATUS_HANDED_OUT ? '' : 'checked' }}>
            <label class="form-check-label" for="statusupdate_hand_out">
                @if($loan->getStatus() < App\Loan::STATUS_HANDED_OUT)
                    &gt; Hand out now
                @else 
                    Handed out <span class="badge badge-light">{{ $loan->date_given }}</span>
                @endif
            </label>
        </div>
        <div class="form-check">
            <input name="statusupdate_return" value="true" class="form-check-input" type="checkbox" value="" id="statusupdate_return"
                    {{ $loan->getStatus() == App\Loan::STATUS_RETURNED-1 ? '' : 'disabled' }}
                    {{ $loan->getStatus() < App\Loan::STATUS_RETURNED ? '' : 'checked' }}>
            <label class="form-check-label" for="statusupdate_return">
                @if($loan->getStatus() < App\Loan::STATUS_RETURNED)
                    &gt; Return
                @else
                    Returned <span class="badge badge-light">{{ $loan->date_returned }}</span>
                @endif
            </label>
        </div>
    </div>
    
    <button type="submit" class="btn btn-primary">Update loan</button>
</form>

<hr>

<h3>Assets</h3>
@forelse($loan->assets as $asset)
    <div class="border">
        <form method="post" action="{{ route('assetsloans.destroy') }}">
            @csrf
            @method('DELETE')
            {{ $asset->getNamesString() }}
            <input type="hidden" name="asset_id" value="{{ $asset->id }}">
            <input type="hidden" name="loan_id" value="{{ $loan->id }}">
            <button type="submit" class="btn-sm btn-danger" >X</button>
        </form>
    </div>
@empty
<div class="alert alert-info">
    No assets are part of this transaction yet. Please add some below.
</div>
@endforelse
<br>

<h5>Add asset</h5>
@if (session('no_asset_found'))
<div class="alert alert-warning">
    <p>Could not find asset called {{ old('asset_search') }}. Please make sure
        you pick one of the names from the suggestions.</p>
    <p>If you do not see suggestions, try using another (up to date) browser.</p>
</div>
@endif
<form method="post" action="{{ route('assetsloans.store') }}" class="form-inline">
    @csrf
    <input type="hidden" name="loan_id" value="{{ $loan->id }}">
    
    <div class="form-group mb-2">
        <datalist id="list_asset_names">
            @foreach($allAssetnames as $name)
                <option label="{{ $name->name }}" value="{{ $name->name }}">
            @endforeach
        </datalist>
        <input type="search" class="form-control" id="asset_search"
               list="list_asset_names" name="asset_search"
               placeholder="Asset name ..."
               value="{{ session('no_asset_found') ? old('asset_search') : '' }}">
    </div>
    <button type="submit" class="btn btn-primary mb-2">Add</button>
</form>
@endsection