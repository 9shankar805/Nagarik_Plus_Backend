<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\EmergencyContact;
use App\Models\Hospital;
use Illuminate\Http\JsonResponse;

class EmergencyController extends Controller
{
    public function index(): JsonResponse
    {
        $contacts = EmergencyContact::active()
                                    ->get()
                                    ->map(fn($c) => [
                                        'id'          => $c->id,
                                        'name'        => $c->name,
                                        'name_np'     => $c->name_np,
                                        'number'      => $c->number,
                                        'description' => $c->description,
                                        'category'    => $c->category,
                                        'icon'        => $c->icon,
                                        'color'       => $c->color,
                                    ]);

        $dbHospitals = Hospital::where('is_active', true)->get();

        if ($dbHospitals->isNotEmpty()) {
            $hospitals = $dbHospitals;
        } else {
            $hospitals = [
                [
                    'id' => 1,
                    'name' => 'Teaching Hospital',
                    'name_np' => 'शिक्षण अस्पताल',
                    'address' => 'Maharajgunj, Kathmandu',
                    'address_np' => 'महाराजगञ्ज, काठमाडौं',
                    'phone' => '01-4412765',
                    'type' => 'Government',
                ],
                [
                    'id' => 2,
                    'name' => 'Bir Hospital',
                    'name_np' => 'वीर अस्पताल',
                    'address' => 'Tundikhel, Kathmandu',
                    'address_np' => 'टुण्डिखेल, काठमाडौं',
                    'phone' => '01-4261944',
                    'type' => 'Government',
                ],
                [
                    'id' => 3,
                    'name' => 'Narayani Zonal Hospital',
                    'name_np' => 'नारायणी अञ्चल अस्पताल',
                    'address' => 'Birgunj, Parsa',
                    'address_np' => 'वीरगञ्ज, पर्सा',
                    'phone' => '051-520111',
                    'type' => 'Government',
                ],
                [
                    'id' => 4,
                    'name' => 'Norvic Hospital',
                    'name_np' => 'नोरभिक अस्पताल',
                    'address' => 'Thapathali, Kathmandu',
                    'address_np' => 'थापाथली, काठमाडौं',
                    'phone' => '01-4584242',
                    'type' => 'Private',
                ],
            ];
        }

        return response()->json([
            'success' => true,
            'data' => [
                'contacts' => $contacts,
                'hospitals' => $hospitals,
            ],
        ]);
    }
}
