@extends('layouts.app')

@section('title', 'Edit Asset')

@section('content')
<h3>Edit Asset (ID <i>{{ config('app.conventions.asset_prefix') }}{{ $asset->id }}</i>)
<a href="{{ route('assets.show', $asset->id) }}" class="btn btn-secondary btn-sm">Stop editing</a>
</h3>

<form action="{{ route('assets.update', $asset->id) }}" method="post" class="">
    @method('PUT')
    @csrf
    <div class="form-group">
        <label for="location">Location</label>
        <input type="text" class="form-control" id="location" name="location" value="{{ $asset->location }}" required>
    </div>
    <div class="form-group">
        <label for="category">Category</label>
        <select class="form-control" id="category" name="category" required>
            @include('includes.category_select_options', ['selectedId' => $asset->category->id])
        </select>
    </div>
    <div class="form-group">
        <label for="stock">Stock</label>
        <input type="number" class="form-control" id="stock" name="stock" value="{{ $asset->stock }}">
    </div>
    <h5>Names</h5>
    <p>Leave lines empty if not required or the name should be deleted. Per line all fields must be filled. If
        not all fields on one line are filled, the line will be ignored. Names
        must be unique. If only one name is not unique, the whole action
        will fail.</p>
    <div class="form-row">
        <div class="col">
            Language [en, de, fr]
        </div>
        <div class="col">
            Name
        </div>
    </div>
    @for($i=0; $i < 8; $i++)
        <div class="form-row">
            <input type="hidden" name="assetnames_id[]" value="{{ isset($asset->assetnames[$i]) ? $asset->assetnames[$i]->id : '' }}">
            <div class="col">
                <input type="text" class="form-control" name="assetnames_language[]" value="{{ isset($asset->assetnames[$i]) ? $asset->assetnames[$i]->language : '' }}" 
                       @if($i==0) required @endif>
            </div>
            <div class="col">
                <input type="text" class="form-control" name="assetnames_name[]" value="{{ isset($asset->assetnames[$i]) ? $asset->assetnames[$i]->name : '' }}"
                       @if($i==0) required @endif>
            </div>
        </div>
    @endfor
    <br>
    <button type="submit" class="btn btn-primary mb-2">Apply</button>
</form>
@endsection