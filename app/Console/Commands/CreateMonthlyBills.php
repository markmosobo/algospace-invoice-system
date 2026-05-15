<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\DiaryEntry;
use Carbon\Carbon;

class CreateMonthlyBills extends Command
{
    protected $signature = 'bills:create-monthly';
    protected $description = 'Create monthly bills automatically with proper remind_at';

    public function handle()
    {
        $now = Carbon::now();

        // Define your monthly bills with their due day
        $bills = [
            ['title'=>'Rent','amount'=>4000,'category'=>'office','description'=>'Pay by 5th','due_day'=>5],
            ['title'=>'Electricity','amount'=>500,'category'=>'office','description'=>'Token based','due_day'=>1],
            ['title'=>'Internet','amount'=>2000,'category'=>'office','description'=>'Pay by 3rd','due_day'=>3],
            ['title'=>'Mikrotik','amount'=>500,'category'=>'office','description'=>'Pay by 5th','due_day'=>5],
        ];

        foreach($bills as $bill){
            // Calculate remind_at for each bill
            $entry_date = $now->copy()->startOfMonth(); // store the bill as for the month
            $remind_at = $now->copy()->startOfMonth()->day($bill['due_day']);

            DiaryEntry::updateOrCreate(
                [
                    'title' => $bill['title'],
                    'type' => 'reminder', // now set as reminder so overview fetches it
                    'entry_date' => $entry_date
                ],
                [
                    'amount' => $bill['amount'],
                    'category' => $bill['category'],
                    'status' => 'pending',
                    'description' => $bill['description'],
                    'remind_at' => $remind_at,
                ]
            );

            $this->info("Created monthly bill: ".$bill['title']." due on ".$remind_at->format('Y-m-d'));
        }
    }
}