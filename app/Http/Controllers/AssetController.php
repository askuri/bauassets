<?php

namespace App\Http\Controllers;

use App\Asset;
use App\Assetname;
use Illuminate\Http\Request;

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
            //'assets' => Asset::all(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
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
