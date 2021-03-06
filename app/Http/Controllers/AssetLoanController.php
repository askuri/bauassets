<?php
/**
 * Manage the asset <-> loan relation
 * 
 */

namespace App\Http\Controllers;

use App\Loan;
use App\Asset;
use App\Assetname;
use Illuminate\Http\Request;

class AssetLoanController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
        $request->flash(); // make old flash data available to the next page again
        
        $validatedData = $request->validate([
            'loan_id' => 'required|integer',
            'asset_search' => 'nullable', // why is this nullable?!
        ]);
        
        // find loan
        $loan = Loan::find($validatedData['loan_id']);
        
        // restrict to authorized users
        $this->authorize('attachAsset', $loan);
        
        // only update if not immutable
        $this->authorize('updateImmutable', $loan);
        
        // find asset
        try {
            $assetname = Assetname::where('name', '=', $validatedData['asset_search'])
                    ->firstOrFail();
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return back()->withErrors(['asset_search' => 'Could not find asset called '.old('asset_search').'. Please make sure
        you pick one of the names from the suggestions. If you do not see suggestions, try using another (up to date) browser.']);
        }
        
        // we got all information, now attach the asset to the loan
        $loan->assets()->attach($assetname->asset_id); // attach an asset to a loan
        
        return back();
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $id)
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
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $validatedData = $request->validate([
            'loan_id' => 'required|integer',
            'asset_id' => 'required|integer',
        ]);
        
        $loan = Loan::find($validatedData['loan_id']);
        
        // restrict to authorized users
        $this->authorize('detachAsset', $loan);
        
        // only update if not immutable
        $this->authorize('updateImmutable', $loan);
        
        $loan->assets()->detach($validatedData['asset_id']); // attach an asset to a loan
        
        return redirect()->route('loans.edit', $loan->id);
    }
}
