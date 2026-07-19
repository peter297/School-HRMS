<?php

namespace App\Console\Commands;

use App\Models\Employees;
use App\Models\User;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

#[Signature('app:link-users-to-employees')]
#[Description('Command description')]
class LinkUsersToEmployees extends Command
{
    /**
     * Execute the console command.
     */

    protected $signature = 'hrms:link-users';
    protected $description = 'Link existing users to employee records by matching their email address';
    public function handle()
    {
        $linked = 0;
        $skipped = 0;
        $notFound = 0;

        $users = User::all();

        foreach($users as $user){
            $employee = Employees::where('email', $user->email)
            ->whereNull('user_id')
            ->first();

            if(!$employee){
                $alreadyLinked = Employees::where('email', $user->email)
                ->whereNotNull('user_id')
                ->first();

                if($alreadyLinked){
                    $this->line("<comment>SKIP</comment>  {$user->email} — already linked to employee {$alreadyLinked->staff_number}");
                    $skipped;
                }else{
                    $this->line("  <error>MISS</error>  {$user->email} — no employee record found");
                    $notFound;
                }

                continue;
            }

            DB::table('employees')
                ->where('id', $employee->id)
                ->update(['user_id' => $user->id]);

            $this->line("  <info>LINK</info>  {$user->email} → {$employee->staff_number} ({$employee->full_name})");
            $linked++;


        }
        $this->newLine();
        $this->info("Done. Linked: {$linked} | Already linked: {$skipped} | No match: {$notFound}");

        if ($notFound > 0) {
            $this->warn("Employees with no match records need to be linked manually via the HR portal.");
        }
    }
}
