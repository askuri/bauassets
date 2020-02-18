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
            <option value="1">Testname</option>
        </select>
    </div>
    <div class="form-group">
        <label for="stock">Stock</label>
        <input type="number" class="form-control" id="stock" name="stock" value="1">
    </div>
    <h5>Names</h5>
    <div class="form-row">
        <div class="col">
            Language [en, de, fr]
        </div>
        <div class="col">
            Name
        </div>
    </div>
    <div class="form-row">
        <div class="col">
            <input type="text" class="form-control" name="assetnames_lang[]">
        </div>
        <div class="col">
            <input type="text" class="form-control" name="assetnames_name[]">
        </div>
    </div>
    <div class="form-row">
        <div class="col">
            <input type="text" class="form-control" name="assetnames_lang[]">
        </div>
        <div class="col">
            <input type="text" class="form-control" name="assetnames_name[]">
        </div>
    </div>
    <br>
    <button type="submit" class="btn btn-primary mb-2">Add</button>
</form>


@endsection