<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\PersonalAccount;

class LedgerEntry extends Model
{
    protected $table = 'ledger_entries';

    protected $fillable = [
        'entry_date',
        'debit_account_id',
        'credit_account_id',
        'amount',
        'entry_type',
        'reference',
        'description',
        'created_by',
    ];

    /* =======================
       Relationships
    ======================= */

    public function debitAccount()
    {
        return $this->belongsTo(PersonalAccount::class, 'debit_account_id');
    }

    public function creditAccount()
    {
        return $this->belongsTo(PersonalAccount::class, 'credit_account_id');
    }

    /* =======================
       Safety & Business Rules
    ======================= */

    protected static function booted()
    {
        static::creating(function ($entry) {

            // 1️⃣ Basic validations
            if ($entry->amount <= 0) {
                throw new \Exception('Ledger amount must be greater than zero.');
            }

            if ($entry->debit_account_id === $entry->credit_account_id) {
                throw new \Exception('Debit and Credit accounts must be different.');
            }

            // 2️⃣ Load related accounts
            $debit = $entry->debitAccount;
            $credit = $entry->creditAccount;

            // 3️⃣ Business rules

            // Owner draws can only come from shop working capital
            if($entry->entry_type === 'owner_draw') {
                if(!$credit || $credit->category !== 'shop_working_capital') {
                    throw new \Exception('Owner draw must come from shop working capital.');
                }
            }

            // Sales must credit revenue accounts
            if($entry->entry_type === 'sale') {
                if(!$credit || !in_array($credit->category, ['shop_working_capital','company_revenue'])) {
                    throw new \Exception('Sale must credit a revenue account.');
                }
            }

            // Expenses can only debit expense accounts (optional, implement if you have expense categories)
            // Capital injections can be any valid account
            
            // 5️⃣ Loans / repayments: must involve LOAN accounts
            if(in_array($entry->entry_type, ['loan','loan_repayment'])) {
                $allowed_accounts = ['LOAN RECEIVABLE','LOAN PAYABLE'];
                if(
                    !in_array($debit->name, $allowed_accounts) &&
                    !in_array($credit->name, $allowed_accounts)
                ) {
                    throw new \Exception('Loan entries must involve LOAN RECEIVABLE or LOAN PAYABLE accounts.');
                }
            }


        });
    }
}
