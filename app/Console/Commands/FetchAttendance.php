<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Rats\Zkteco\Lib\ZKTeco;
use App\Models\zkt_attendence;
use App\Models\zkt_machine;
use Illuminate\Console\Command;

class FetchAttendance extends Command
{
    protected $signature = 'attendance:fetch';
    protected $description = 'Fetch attendance data from ZKTeco K40';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $zkt_info = zkt_machine::where('Status', 1)->get();
        if(!$zkt_info){
            $this->info('Cannot find any machine');
        }
        foreach ($zkt_info as $info) {
            $this->info($info->IP);
            $this->info($info->port);
            $zk = new ZKTeco($info->IP, $info->port); 

            if ($zk->connect()) {
                $attendanceData = $zk->getAttendance();
                foreach ($attendanceData as $attendance) {
                    $timestamp = Carbon::parse($attendance['timestamp']);
                    $date = $timestamp->toDateString();
                    $time = $timestamp->toTimeString();
    
                    // Check if the record already exists
                    $existingAttendance = zkt_attendence::where('user_id', $attendance['id'])
                        ->where('date', $date)
                        ->where('time', $time)
                        ->first();
                    if (!$existingAttendance) {
                        zkt_attendence::create([
                            'user_id' => $attendance['id'],
                            'Machine_Id' => $info->id,
                            'date' => $date,
                            'time' => $time,
                        ]);
                    }
                }
                $zk->disconnect();
                $this->info('Attendance data fetched successfully.');
            } else {
                $this->error('Unable to connect to the device.');
            }
        }

    }
}
