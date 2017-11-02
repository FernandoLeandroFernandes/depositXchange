<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Exchange extends Model
{
	protected $fillable = [
		'origin_id',
		'destination_id',
		'amount'
	];

	public function origin() {
		return $this->belongsTo('App\Bank');
	}

	public function destination() {
		return $this->belongsTo('App\Bank');
	}

}
