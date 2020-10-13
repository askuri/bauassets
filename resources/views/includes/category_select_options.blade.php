@foreach(App\Category::all() as $category)
    <option value="{{ $category->id }}" {{ isset ($selectedId) && $selectedId == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
@endforeach