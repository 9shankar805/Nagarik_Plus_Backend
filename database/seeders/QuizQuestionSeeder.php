<?php

namespace Database\Seeders;

use App\Models\QuizQuestion;
use Illuminate\Database\Seeder;

class QuizQuestionSeeder extends Seeder
{
    public function run(): void
    {
        $questions = [
            ['question' => 'What does a red traffic light mean?', 'options' => ['Go slowly', 'Stop completely', 'Speed up', 'Yield to right'], 'correct_index' => 1, 'difficulty' => 'easy', 'explanation' => 'A red traffic light means you must stop completely before the stop line.'],
            ['question' => 'What is the speed limit in a residential area in Nepal?', 'options' => ['40 km/h', '50 km/h', '30 km/h', '60 km/h'], 'correct_index' => 2, 'difficulty' => 'easy', 'explanation' => 'Speed limit in residential areas is 30 km/h.'],
            ['question' => 'When should you use hazard lights?', 'options' => ['When parking illegally', 'When your vehicle is broken down or in emergency', 'When driving in fog', 'When turning right'], 'correct_index' => 1, 'difficulty' => 'medium', 'explanation' => 'Hazard lights are used when your vehicle breaks down or is in an emergency situation.'],
            ['question' => 'What does a yellow dashed center line mean?', 'options' => ['No passing allowed', 'Passing allowed with caution', 'Stop zone ahead', 'School zone'], 'correct_index' => 1, 'difficulty' => 'medium', 'explanation' => 'A yellow dashed line means passing is allowed when safe to do so.'],
            ['question' => 'Which document is NOT required while driving in Nepal?', 'options' => ['Driving license', 'Vehicle bluebook', 'Insurance certificate', 'PAN card'], 'correct_index' => 3, 'difficulty' => 'easy', 'explanation' => 'PAN card is not a mandatory document while driving.'],
            ['question' => 'At an uncontrolled intersection, who has the right of way?', 'options' => ['The vehicle on the left', 'The vehicle on the right', 'The larger vehicle', 'The faster vehicle'], 'correct_index' => 1, 'difficulty' => 'medium', 'explanation' => 'At uncontrolled intersections, vehicles on the right have priority.'],
            ['question' => 'What is the blood alcohol limit for drivers in Nepal?', 'options' => ['0.05%', '0.08%', '0.03%', '0.10%'], 'correct_index' => 2, 'difficulty' => 'hard', 'explanation' => 'The legal blood alcohol limit is 0.03% for professional drivers.'],
            ['question' => 'What does a solid white line on the road mean?', 'options' => ['Overtaking is allowed', 'Do not cross the line', 'Parking zone', 'Speed bump ahead'], 'correct_index' => 1, 'difficulty' => 'medium', 'explanation' => 'A solid white line means you should not cross or change lanes.'],
            ['question' => 'What is the minimum age to get a four-wheeler driving license in Nepal?', 'options' => ['16 years', '18 years', '21 years', '17 years'], 'correct_index' => 1, 'difficulty' => 'easy', 'explanation' => 'The minimum age for a four-wheeler driving license is 18 years.'],
            ['question' => 'What should you do when you see a school bus with flashing red lights?', 'options' => ['Overtake quickly', 'Stop and wait', 'Honk to warn children', 'Slow down to 20 km/h'], 'correct_index' => 1, 'difficulty' => 'medium', 'explanation' => 'When a school bus has flashing red lights, you must stop and wait for children to board or alight.'],
            ['question' => 'What does a triangular road sign indicate?', 'options' => ['Mandatory instruction', 'Warning or hazard ahead', 'Information', 'Road closed'], 'correct_index' => 1, 'difficulty' => 'easy', 'explanation' => 'Triangular signs are warning signs indicating potential hazards ahead.'],
            ['question' => 'How far should you park from a fire hydrant?', 'options' => ['3 meters', '5 meters', '10 meters', '2 meters'], 'correct_index' => 1, 'difficulty' => 'hard', 'explanation' => 'You must not park within 5 meters of a fire hydrant.'],
            ['question' => 'What does a green traffic light mean?', 'options' => ['Stop', 'Caution', 'Go when safe', 'Give way'], 'correct_index' => 2, 'difficulty' => 'easy', 'explanation' => 'Green light means you may proceed when it is safe to do so.'],
            ['question' => 'What is the speed limit on highways in Nepal?', 'options' => ['60 km/h', '70 km/h', '80 km/h', '100 km/h'], 'correct_index' => 2, 'difficulty' => 'medium', 'explanation' => 'The general speed limit on highways is 80 km/h.'],
            ['question' => 'Is it mandatory to wear a seatbelt in Nepal?', 'options' => ['Only for drivers', 'Only for front passengers', 'For all occupants', 'Not mandatory'], 'correct_index' => 2, 'difficulty' => 'easy', 'explanation' => 'Seatbelts are mandatory for all occupants of a vehicle in Nepal.'],
            ['question' => 'What does a circular red border sign mean?', 'options' => ['Warning sign', 'Prohibition sign', 'Information sign', 'Guide sign'], 'correct_index' => 1, 'difficulty' => 'medium', 'explanation' => 'Circular signs with red border are prohibition signs.'],
            ['question' => 'When must you use headlights?', 'options' => ['Only at night', 'Dusk to dawn and in poor visibility', 'Only in fog', 'Only in rain'], 'correct_index' => 1, 'difficulty' => 'medium', 'explanation' => 'Headlights must be used from dusk to dawn and any time visibility is reduced.'],
            ['question' => 'What is tailgating?', 'options' => ['Overtaking on left', 'Following too closely behind another vehicle', 'Parking at the rear', 'Reversing on highway'], 'correct_index' => 1, 'difficulty' => 'easy', 'explanation' => 'Tailgating means following another vehicle too closely, which is dangerous and illegal.'],
            ['question' => 'What should you do when an ambulance approaches from behind?', 'options' => ['Speed up', 'Stop in middle', 'Pull over to left and let it pass', 'Ignore it'], 'correct_index' => 2, 'difficulty' => 'easy', 'explanation' => 'Always pull over to the left side and stop to allow emergency vehicles to pass.'],
            ['question' => 'How often must a driving license be renewed in Nepal?', 'options' => ['Every 2 years', 'Every 3 years', 'Every 5 years', 'Every 10 years'], 'correct_index' => 2, 'difficulty' => 'easy', 'explanation' => 'Driving license must be renewed every 5 years in Nepal.'],
        ];

        foreach ($questions as $q) {
            QuizQuestion::create(array_merge($q, [
                'category'  => 'driving_license',
                'is_active' => true,
            ]));
        }
    }
}
