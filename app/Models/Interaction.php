<?php

namespace App\Models;

use App\Traits\FormattedDates;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Interaction extends Model
{
    use SoftDeletes;
    use FormattedDates;
    protected $appends = ['account_ui'];
    public function characterization(){
        return $this->belongsTo('App\Models\Characterization');
    }

    public function client(){
        return $this->belongsTo('App\Models\Client');
    }

    public function account(){
        return $this->belongsTo('App\Models\Account');
    }

    public function getAccountUiAttribute(){
        //  error_log($this->account->setAppends([])->data->ui);
        //  (string)$this->account->setAppends([])->data->ui;
         return Account::find($this->account_id)->setAppends([])->data->ui;
    }
}
