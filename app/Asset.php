<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Asset extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'assets';
    
    /**
     * The model's default values for attributes.
     *
     * @var array
     */
    protected $attributes = [
        'stock' => 1,
    ];
    
    
    /**
     * All asset names concatenated
     * 
     * @return string " / " separated string
     */
    public function getNamesString(): string {
        $out = '';
        $assetnames = $this->assetnames;
        foreach ($assetnames as $name) {
            $out .= $name['name'] . ' / ';
        }
        
        return trim($out, ' / ');
    }

    public function assetnames() {
        return $this->hasMany('App\Assetname');
    }
    
    public function loans()
    {
        return $this->belongsToMany('App\Loan');
    }
}
