<?php

namespace Database\Seeders;

use App\Models\Advisory;
use App\Models\CenterPurok;
use App\Models\EmergencyContact;
use App\Models\EvacuationCenter;
use App\Models\Household;
use App\Models\Incident;
use App\Models\IncidentNote;
use App\Models\ReliefDistribution;
use App\Models\ReliefItem;
use App\Models\SmsLog;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ---------- Staff and admin accounts (sign in with email) ----------
        User::create([
            'name'     => 'Barangay Staff',
            'email'    => 'staff@brgy.test',
            'password' => 'password123',   // hashed by the cast
            'role'     => 'staff',
        ]);

        User::create([
            'name'     => 'Barangay Admin',
            'email'    => 'admin@brgy.test',
            'password' => 'password123',
            'role'     => 'admin',
        ]);

        // ---------- Households: 3 per Purok (1-8) ----------
        $names = [
            'Santos', 'Reyes', 'Cruz', 'Bautista', 'Garcia', 'Mendoza', 'Torres', 'Flores',
            'Ramos', 'Aquino', 'Castillo', 'Navarro', 'Villanueva', 'Pascual', 'Domingo', 'Salazar',
            'Rivera', 'Mercado', 'Aguilar', 'Soriano', 'Valdez', 'Marquez', 'Padilla', 'Lim',
        ];
        $safety = ['safe', 'safe', 'safe', 'evacuated', 'needs_help'];

        $i = 0;
        for ($purok = 1; $purok <= 8; $purok++) {
            for ($n = 0; $n < 3; $n++) {
                Household::create([
                    'head_name' => 'Juan ' . $names[$i % count($names)],
                    'purok'     => $purok,
                    'members'   => rand(2, 7),
                    'safety'    => $safety[$i % count($safety)],
                ]);
                $i++;
            }
        }

        // ---------- Demo resident (same login you tested in Postman) ----------
        $demoHousehold = Household::create([
            'head_name' => 'Juan Dela Cruz',
            'purok'     => 3,
            'members'   => 4,
            'safety'    => 'safe',
        ]);

        $resident = User::create([
            'household_id'  => $demoHousehold->id,
            'name'          => 'Juan Dela Cruz',
            'mobile_number' => '9171234567',   // sign in with 09171234567
            'password'      => 'password123',
            'role'          => 'resident',
        ]);

        // ---------- Evacuation centers and the Puroks they serve ----------
        $centers = [
            ['name' => 'Barangay Covered Court',        'capacity' => 300, 'occupants' => 0,   'status' => 'standby',       'puroks' => [1, 2, 3]],
            ['name' => 'Elementary School Gym',         'capacity' => 250, 'occupants' => 235, 'status' => 'near_capacity', 'puroks' => [4, 5, 6]],
            ['name' => 'Barangay Hall Multipurpose',    'capacity' => 120, 'occupants' => 40,  'status' => 'open',          'puroks' => [7, 8]],
        ];

        foreach ($centers as $c) {
            $center = EvacuationCenter::create([
                'name'      => $c['name'],
                'capacity'  => $c['capacity'],
                'occupants' => $c['occupants'],
                'status'    => $c['status'],
            ]);

            foreach ($c['puroks'] as $p) {
                CenterPurok::create([
                    'evacuation_center_id' => $center->id,
                    'purok'                => $p,
                ]);
            }
        }

        // ---------- Advisories ----------
        $heavyRain = Advisory::create([
            'title'     => 'Heavy rainfall warning',
            'message'   => 'Orange rainfall warning is in effect. Prepare go-bags and monitor water levels in low-lying areas.',
            'severity'  => 'orange',
            'status'    => 'active',
            'issued_at' => now()->subHours(3),
        ]);

        Advisory::create([
            'title'     => 'Evacuation centers on standby',
            'message'   => 'All evacuation centers are ready. Residents in flood-prone Puroks may proceed if water rises.',
            'severity'  => 'yellow',
            'status'    => 'active',
            'issued_at' => now()->subHours(1),
        ]);

        Advisory::create([
            'title'     => 'Relief goods distribution schedule',
            'message'   => 'Relief packs will be distributed at the barangay hall from 8 AM to 4 PM.',
            'severity'  => 'info',
            'status'    => 'archived',
            'issued_at' => now()->subDays(5),
        ]);

        // SMS log is pending: sent = 0 and sent_at = null until a provider confirms delivery
        SmsLog::create([
            'advisory_id' => $heavyRain->id,
            'recipients'  => 1,
            'sent'        => 0,
            'failed'      => 0,
            'sent_at'     => null,
        ]);

        // ---------- Emergency contacts (sample numbers, replace with real ones) ----------
        $contacts = [
            ['name' => 'National Emergency Hotline', 'phone' => '911',           'category' => 'Emergency'],
            ['name' => 'Barangay Hall',              'phone' => '09000000001',   'category' => 'Barangay'],
            ['name' => 'Barangay Health Center',     'phone' => '09000000002',   'category' => 'Medical'],
            ['name' => 'Fire Station',               'phone' => '09000000003',   'category' => 'Fire'],
            ['name' => 'Police Station',             'phone' => '09000000004',   'category' => 'Police'],
        ];
        foreach ($contacts as $contact) {
            EmergencyContact::create($contact);
        }

        // ---------- Relief items and a few distributions ----------
        $rice    = ReliefItem::create(['name' => 'Rice',            'unit' => 'kg',   'quantity_in_stock' => 500]);
        $canned  = ReliefItem::create(['name' => 'Canned goods',    'unit' => 'cans', 'quantity_in_stock' => 300]);
        $water   = ReliefItem::create(['name' => 'Drinking water',  'unit' => 'L',    'quantity_in_stock' => 800]);
        ReliefItem::create(['name' => 'Hygiene kit', 'unit' => 'kit', 'quantity_in_stock' => 120]);

        $households = Household::take(3)->get();
        foreach ($households as $h) {
            ReliefDistribution::create([
                'household_id'   => $h->id,
                'relief_item_id' => $rice->id,
                'quantity'       => 5,
                'distributed_at' => now()->subDay(),
            ]);
        }
        ReliefDistribution::create([
            'household_id'   => $households->first()->id,
            'relief_item_id' => $water->id,
            'quantity'       => 10,
            'distributed_at' => now()->subDay(),
        ]);

        // ---------- Sample assistance requests from the demo resident ----------
        $medical = Incident::create([
            'reporter_id' => $resident->id,
            'type'        => 'medical',
            'priority'    => 'high',
            'status'      => 'in_progress',
            'description' => 'Elderly family member needs a nurse.',
        ]);

        IncidentNote::create([
            'incident_id' => $medical->id,
            'text'        => 'Health worker dispatched.',
        ]);

        Incident::create([
            'reporter_id' => $resident->id,
            'type'        => 'relief',
            'priority'    => 'medium',
            'status'      => 'pending',
            'description' => 'Need food packs for 4 people.',
        ]);
    }
}