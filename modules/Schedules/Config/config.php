<?php

return [
    // true  = /flights (frontend.flights.index) shows the unique pairs page.
    //         /flights/search, /flights/{id} and /flights/bids stay stock.
    // false = core /flights is untouched; the pairs page lives only at /schedules.
    'override_flights_index' => true,

    // URL for the module's own routes (always registered, works even if the override is off).
    // The bid form posts to {uri}/bids.
    'uri' => 'schedules',
];
