<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Simulation extends Model
{
	protected $fillable = [
		'id', 
		'description', 
		'max_connections', 
        'exchanged_amount',
        'total_amount'
    ];

}
