<div class="form-group mb-2">
    <datalist id="list_asset_names">
        @foreach(App\Assetname::all() as $name)
            <option label="{{ $name->name }}" value="{{ $name->name }}">
        @endforeach
    </datalist>
    <input type="search" class="form-control" id="asset_search"
           list="list_asset_names" name="asset_search"
           placeholder="Asset name ..."
           autocomplete="off"
           value="{{ session('no_asset_found') ? old('asset_search') : '' }}">
</div>