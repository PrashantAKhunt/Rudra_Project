<?php
   
namespace App\Imports;
   
use App\BankTransaction;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
    
class BankTransactionImport implements ToModel, WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new BankTransaction([
            'company_id'     => $row['company_id'],
            'bank_id'    => $row['bank_id'], 
            'tx_id' => $row['transaction_id'], 
            'tx_date'     => $row['transaction_date'],
            'particular'    => $row['particular'], 
            'cheque_num' => $row['cheque_number'], 
            'internal'     => $row['internal'],
            'voucher_type'    => $row['voucher_type'], 
            'project' => $row['project'], 
            'head_id'     => $row['head_id'],
            'sub_head'    => $row['sub_head'], 
            'received' => $row['received'], 
            'paid'     => $row['paid'],
            'balance'    => $row['balance'], 
            'narration' => $row['narration'], 
            'remark'     => $row['remark'],
          
        ]);
    }
}