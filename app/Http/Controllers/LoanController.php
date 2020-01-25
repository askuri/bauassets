<?php

namespace App\Http\Controllers;

use App\Loan;
use App\Asset;
use App\Assetname;
use Illuminate\Http\Request;

class LoanController extends Controller
{
    
    public function __construct() {
        $this->middleware('authenticate_as:werkzeugag');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $loans = Loan::orderBy('updated_at', 'desc')->get();
        return view('loan.index', [
            'loans' => $loans,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('loan.new');
    }

    /**
     * Store a newly created loan in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|max:191',
            'room' => 'required|max:4',
        ]);
        
        $loan = new Loan();
        $loan->borrower_name = $validatedData['name'];
        $loan->borrower_room = $validatedData['room'];
        $loan->save();
        
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
        $loan = Loan::find($id);
        return view('loan.show', [
            'loan' => $loan,
        ]);
    }

    /**
     * Show the form for editing the loan.
     * Associated assets are updated in AssetLoanController.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $id)
    {
        $loan = Loan::find($id);
        // for now, if the loan is immutable, just redirect to @show
        if ($loan->isImmutable()) {
            return redirect()->route('loans.show', $id);
        }
        
        return view('loan.edit', [
            'loan' => $loan,
            'allAssetnames' => Assetname::all(),
        ]);
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
        $validatedData = $request->validate([
            'borrower_name' => 'required|max:191',
            'borrower_room' => 'required|max:4',
            'comment' => 'nullable',
            'statusupdate_hand_out' => 'nullable',
            'statusupdate_return' => 'nullable',
        ]);
        
        $loan = Loan::find($id);
        
        if ($loan->isImmutable()) {
            throw new \Exception('Cannot delete assets from this loan because it\'s immutable');
        }
        
        $loan->borrower_name = $validatedData['borrower_name'];
        $loan->borrower_room = $validatedData['borrower_room'];
        $loan->comment = $validatedData['comment'];
        if (isset($validatedData['statusupdate_hand_out'])) {
            $loan->setStatusHandedOut();
        }
        if (isset($validatedData['statusupdate_return'])) {
            $loan->setStatusReturned();
        }
        $loan->save();
        
        return redirect()->route('loans.edit', $loan->id);
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
