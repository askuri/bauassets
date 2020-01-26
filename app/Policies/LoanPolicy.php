<?php

namespace App\Policies;

use App\Loan;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

class LoanPolicy
{
    use HandlesAuthorization;
    
    private $immutableLoanMessage = 'This loan is immutable and can\'t be edited. You can still view it.';

    /**
     * Determine whether the user can view any loans.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function viewAny(User $user)
    {
        //
    }

    /**
     * Determine whether the user can view the loan.
     *
     * @param  \App\User  $user
     * @param  \App\Loan  $loan
     * @return mixed
     */
    public function view(User $user, Loan $loan)
    {
        //
    }

    /**
     * Determine whether the user can create loans.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->role == 'moderator';
    }

    /**
     * Determine whether the user can update the loan.
     * Checks if it is immutable, too.
     *
     * @param  \App\User  $user
     * @param  \App\Loan  $loan
     * @return mixed
     */
    public function update(User $user, Loan $loan)
    {
        if ($loan->isImmutable()) {
            return Response::deny($this->immutableLoanMessage);
        }
        return $user->role == 'moderator';
    }

    /**
     * Determine whether the user can delete the loan.
     *
     * @param  \App\User  $user
     * @param  \App\Loan  $loan
     * @return mixed
     */
    public function delete(User $user, Loan $loan)
    {
        //
    }

    /**
     * Determine whether the user can restore the loan.
     *
     * @param  \App\User  $user
     * @param  \App\Loan  $loan
     * @return mixed
     */
    public function restore(User $user, Loan $loan)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the loan.
     *
     * @param  \App\User  $user
     * @param  \App\Loan  $loan
     * @return mixed
     */
    public function forceDelete(User $user, Loan $loan)
    {
        //
    }
    
    /**
     * Determine whether the user can attach more assets to the loan.
     * Checks if it is immutable, too.
     *
     * @param  \App\User  $user
     * @param  \App\Loan  $loan
     * @return mixed
     */
    public function attachAsset(User $user, Loan $loan) {
        if ($loan->isImmutable()) {
            return Response::deny($this->immutableLoanMessage);
        }
        return $user->role == 'moderator';
    }
    
    /**
     * Determine whether the user can detach more assets to the loan.
     * Checks if it is immutable, too.
     *
     * @param  \App\User  $user
     * @param  \App\Loan  $loan
     * @return mixed
     */
    public function detachAsset(User $user, Loan $loan) {
        if ($loan->isImmutable()) {
            return Response::deny($this->immutableLoanMessage);
        }
        return $user->role == 'moderator';
    }
}
