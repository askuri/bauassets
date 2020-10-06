<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Asset extends Model
{
    
    use HasFactory;
    
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

    /**
     * Names of the asset are not directly stored. There's a table
     * just for asset names and this is the relation for it.
     * @return type
     */
    public function assetnames() {
        return $this->hasMany('App\Assetname');
    }
    
    /**
     * Many Assets can belong to many loans (N:M)
     * @return type
     */
    public function loans()
    {
        return $this->belongsToMany('App\Loan');
    }
    
    /**
     * One asset is part of one category. Categories can have many
     * categories.
     * @return type
     */
    public function category() {
        return $this->belongsTo('App\Category');
    }
}
