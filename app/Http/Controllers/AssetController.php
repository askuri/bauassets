<?php

namespace App\Http\Controllers;

use App\Asset;
use App\Assetname;
use App\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AssetController extends Controller
{
    /**
     * Show an asset by a given search string.
     * If found it redirects to the asset.
     * If not found it shows an error message.
     */
    public function showBySearch(Request $request) {
        $request->flash(); // make old flash data available to the next page again
        
        $asset_search = $request->validate([
            'asset_search' => 'required',
        ]);
        try {
            $assetname = Assetname::where('name', '=', $asset_search)->firstOrFail();
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return back()->withErrors(['asset_search' => 'Could not find asset called '.old('asset_search').'. Please make sure
        you pick one of the names from the suggestions. If you do not see suggestions, try using another (up to date) browser.']);
        }
        
        return redirect()->route('assets.show', $assetname->id);
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('asset.index', [
            'assets' => Asset::with(['category', 'assetnames'])->get(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->authorize('create', Asset::class);
        return view('asset.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->authorize('create', Asset::class);
        $validatedData = $request->validate([
            'location' => 'required',
            'category' => 'required|integer|min:1',
            'stock' => 'nullable|integer|min:1',
            'assetnames_language' => 'array',
            'assetnames_language.*' => 'nullable|min:2|max:2',
            'assetnames_name' => 'array',
            'assetnames_name.*' => 'nullable|distinct|unique:App\Assetname,name',
        ]);
        
        $asset = DB::transaction(function () use ($validatedData) {
            //create asset
            $asset = new Asset();
            $asset->location = $validatedData['location'];
            $asset->stock = $validatedData['stock'];
            
            // add the category as given by the user
            $asset->category()->associate(Category::find($validatedData['category']));
            
            // save asset so we know the id
            $asset->save();
            
            // add names
            for ($i = 0; $i < count($validatedData['assetnames_language']); $i++) {
                $language = $validatedData['assetnames_language'][$i];
                $name = $validatedData['assetnames_name'][$i];
                
                if (!empty($language) && !empty($name)) { // language and name are required
                    $assetname = new Assetname();
                    $assetname->language = $language;
                    $assetname->name = $name;
                    $asset->assetnames()->save($assetname);
                }
            }
            return $asset;
        });
        
        return redirect()->route('assets.show', $asset->id);
    }

    /**
     * Display the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function show(Asset $asset)
    {
        return view('asset.show', [
            'asset' => $asset,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
