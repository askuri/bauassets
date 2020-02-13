<?php

namespace App;

use App\Asset;
use Illuminate\Database\Eloquent\Model;

class Loan extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'loans';
    
    public const STATUS_CREATED = 0;
    public const STATUS_HANDED_OUT = 1;
    public const STATUS_RETURNED = 2;
    
    /**
     * Returns the status of the loan.
     * Possible values:
     * - 'created'
     * - 'handed_out'
     * - 'returned'
     */
    /*
    public function getStatus(): string {
        if (empty($this->date_given) && empty($this->date_returned)) {
            return 'created';
        } else if (!empty($this->date_given) && empty($this->date_returned)) {
            return 'handed_out';
        } else if (!empty($this->date_given) && !empty($this->date_returned)) {
            return 'returned';
        }
    }
    */
    public function getStatus(): int {
        if (empty($this->date_given) && empty($this->date_returned)) {
            return self::STATUS_CREATED;
        } else if (!empty($this->date_given) && empty($this->date_returned)) {
            return self::STATUS_HANDED_OUT;
        } else if (!empty($this->date_given) && !empty($this->date_returned)) {
            return self::STATUS_RETURNED;
        }
    }
    
    public function getStatusText(): string {
        switch ($this->getStatus()) {
            case 0: return 'Created';
            case 1: return 'Awaiting return';
            case 2: return 'Returned';
        }
    }
    
    /**
     * Set the loan's assets as handed out
     * 
     * Remember to call save() after
     */
    public function setStatusHandedOut() {
        $this->date_given = now();
    }
    
    /**
     * Set the loan's assets as returned
     * 
     * Remember to call save() after
     */
    public function setStatusReturned() {
        $this->date_returned = now();
    }
    
    /**
     * Defines whether the loan should not be altered
     * because of its status. 
     * This does not provide any write protection!
     * 
     * @return bool
     */
    public function isImmutable(): bool {
        return $this->getStatus() >= self::STATUS_HANDED_OUT;
    }
    
    /**
     * Belongs to Many relation to App\Assets
     * @return type
     */
    public function assets()
    {
        return $this->belongsToMany('App\Asset');
    }
    
    /** 
     * Belongs to relation to Users.
     * One loan always has one issuer.
     * 
     * @return type
     */
    public function issuer() {
        return $this->belongsTo(\App\User::class, 'issuer_user_id', 'id');
    }
}
