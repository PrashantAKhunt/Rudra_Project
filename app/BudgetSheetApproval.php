<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BudgetSheetApproval extends Model
{
    protected $table="budget_sheet_approval";
    
    public function budgetsheet_file() {
        return $this->hasMany('App\Budget_sheet_file','budget_sheet_id','id');
    }
    
}
