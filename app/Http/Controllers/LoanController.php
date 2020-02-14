<?php

namespace App\Http\Controllers;

use App\Loan;
use App\Asset;
use App\Assetname;
use App\Mail\LoanGiven;
use App\Mail\LoanReturned;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;

class LoanController extends Controller
{
    
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
        $this->authorize('create', Loan::class);
        
        $validatedData = $request->validate([
            'borrower_name' => 'required|max:191',
            'borrower_room' => 'integer|required|min:101|max:1113',
            'borrower_email' => 'email|required|max:191',
        ]);
        
        $loan = new Loan();
        $loan->borrower_name = $validatedData['borrower_name'];
        $loan->borrower_room = $validatedData['borrower_room'];
        $loan->borrower_email = $validatedData['borrower_email'];
        $loan->issuer()->associate(Auth::user());
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
     * @param  Loan  $loan automatic conversion from id in url to model
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, Loan $loan)
    {
        $this->authorize('update', $loan);
        
        return view('loan.edit', [
            'loan' => $loan,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  Loan  $loan automatic conversion from id in url to model
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Loan $loan)
    {
        $this->authorize('update', $loan);
        
        $validatedData = $request->validate([
            'borrower_name' => 'required|max:191',
            'borrower_room' => 'integer|required|min:101|max:1113',
            'borrower_email' => 'email|required|max:191',
            'comment' => 'nullable',
            'statusupdate_hand_out' => 'nullable',
            'statusupdate_return' => 'nullable',
        ]);
        
        
        if (isset($validatedData['statusupdate_hand_out'])) {
            $loan->setStatusHandedOut();
            Mail::to($loan->borrower_email)->send(new LoanGiven($loan));
        }else if (isset($validatedData['statusupdate_return'])) {
            $loan->setStatusReturned();
            Mail::to($loan->borrower_email)->send(new LoanReturned($loan));
        } else {
            // special: these info should only be updated if the loan is
            // not marked as immutable
            $this->authorize('updateImmutable', $loan);
            
            $loan->borrower_name = $validatedData['borrower_name'];
            $loan->borrower_room = $validatedData['borrower_room'];
            $loan->borrower_email = $validatedData['borrower_email'];
            $loan->comment = $validatedData['comment'];
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
