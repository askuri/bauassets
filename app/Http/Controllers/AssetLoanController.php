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
        $validatedData = $request->validate([
            'loan_id' => 'required|integer',
            'asset_id' => 'required|integer',
        ]);
        
        $loan = Loan::find($validatedData['loan_id']);
        if ($loan->isImmutable()) {
            throw new \Exception('Cannot delete assets from this loan because it\'s immutable');
        }
        
        $loan->assets()->attach($validatedData['asset_id']); // attach an asset to a loan
        
        return redirect()->route('loans.edit', $loan->id);
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
        $loan->assets()->detach($validatedData['asset_id']); // attach an asset to a loan
        
        return redirect()->route('loans.edit', $loan->id);
    }
}
