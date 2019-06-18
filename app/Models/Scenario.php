<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;

class Scenario extends Model
{
  public $timestamps = false;
  public function steps()
  {
    return $this->hasMany('App\Models\ScenarioStep');
  }

  /*
  public function objective(){
      return $this->belongsTo('App\Models\CheckList');
  }
  */

  public function delete(){
    $this->steps()->delete();
    parent::delete();
  }
}
