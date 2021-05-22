<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    const FICOHSA_TC= 'FICOHSA_TC'; 
    use HasFactory;
    protected $hidden = ['accountable','accountable_type','accountable_id'];
    public function client()
    {
      return $this->belongsTo('App\Models\Client');
    }

    public function accountable(){
        return $this->morphTo();
    }

    public function demand(){
      return $this->belongsTo('App\Models\Demand');
    }
    public function interactions(){
      return $this->hasMany('App\Models\Interaction');
    }
    public function interactionsHistory(){
      $interactions = $this->interactions()->orderBy('created_at','asc')->get();
      $history = '';
      foreach ($interactions as $interaction) { 
           $history .= $interaction->created_at." ".$interaction->message. " .- ###### "; 
      }
      return $history;
    }
    public function lastExtrajudicialInteraction(){
      $lastInteraction = $this->interactions()->where('interaction_status_type','Extrajudicial')->orderBy('created_at','desc')->first();
      return $lastInteraction;
    }

    public function lastJudicialInteraction(){
      $lastInteraction = $this->interactions()->where('interaction_status_type','Judicial')->orderBy('created_at','desc')->first();
      return $lastInteraction;
    }

    public function lastGeneralInteraction(){
      $lastInteraction = $this->interactions()->orderBy('created_at','desc')->first();
      return $lastInteraction;
    }

    public function paymentPromise(){
      return $this->hasOne('App\Models\PaymentPromise');
    }

    public function documentRequest(){
      return $this->hasOne('App\Models\DocumentRequest');
    }
}
