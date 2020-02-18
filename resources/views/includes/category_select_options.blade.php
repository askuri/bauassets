@foreach(App\Category::all() as $category)
    <option value="{{ $category->id }}">{{ $category->name }}</option>
@endforeach