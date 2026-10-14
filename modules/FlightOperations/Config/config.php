<?php

return [
    'name' => 'Flight Operations',
    'enabled' => true,
    
    // Airline that user-created flights are filed under (airlines.id) and the route code
    // used to mark them. Previously hard-coded in the controller.
    'airline_id' => 1,
    'route_code' => 'FFM',

    // Flight number generation settings (used by validation and the Generate button)
    'flight_number_min' => 1,
    'flight_number_max' => 5000,
    
    // Default flight settings -- NOTE: currently not used anywhere in the module
    'default_flight_time' => 120, // minutes
    'default_distance' => 500, // nautical miles
];
