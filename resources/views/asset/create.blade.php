@extends('layouts.app')

@section('title', 'Create asset')

@section('content')
<h3>Create asset</h3>

<form action="{{ route('assets.store') }}" method="post" class="">
    @csrf
    <div class="form-group">
        <label for="location">Location</label>
        <input type="text" class="form-control" id="location" name="location" required>
    </div>
    <div class="form-group">
        <label for="category">Category</label>
        <select class="form-control" id="category" name="category" required>
            @include('includes.category_select_options')
        </select>
    </div>
    <div class="form-group">
        <label for="stock">Stock</label>
        <input type="number" class="form-control" id="stock" name="stock" value="1">
    </div>
    <h5>Names</h5>
    <p>Leave lines empty if not required. Per line all fields must be filled. If
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
            <div class="col">
                <input type="text" class="form-control" name="assetnames_language[]" 
                       @if($i==0) required @endif>
            </div>
            <div class="col">
                <input type="text" class="form-control" name="assetnames_name[]"
                       @if($i==0) required @endif>
            </div>
        </div>
    @endfor
    <br>
    <button type="submit" class="btn btn-primary mb-2">Add</button>
</form>


@endsection