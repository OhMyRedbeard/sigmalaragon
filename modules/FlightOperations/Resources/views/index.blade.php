@extends('app')

@section('title', 'Flight Operations')

@section('content')
<style>
    .fo-alert {
        position: relative;
        margin-bottom: 1rem;
        padding: .75rem 2.5rem .75rem 1rem;
        border: 1px solid;
        border-radius: .375rem;
    }

    .fo-alert-success {
        background: rgba(25, 135, 84, .15);
        border-color: rgba(25, 135, 84, .6);
    }

    .fo-alert-danger {
        background: rgba(220, 53, 69, .15);
        border-color: rgba(220, 53, 69, .6);
    }

    .fo-alert ul {
        margin: 0;
        padding-left: 1.25rem;
    }

    .fo-alert-close {
        position: absolute;
        top: .35rem;
        right: .6rem;
        padding: 0 .25rem;
        border: 0;
        background: none;
        color: inherit;
        font-size: 1.5rem;
        line-height: 1;
        opacity: .6;
        cursor: pointer;
    }

    .fo-alert-close:hover,
    .fo-alert-close:focus {
        opacity: 1;
    }

    /* Airport type-ahead dropdown (theme has no .list-group styles) */
    .airport-picker {
        position: relative;
    }

    .fo-airport-results {
        position: absolute;
        top: 100%;
        left: 0;
        right: 0;
        z-index: 1050;
        max-height: 15rem;
        overflow-y: auto;
        margin-top: .25rem;
        /* Opaque: it floats over the next form field. Takes the card's own colour (white), then the page colour. */
        background: var(--bs-card-bg, var(--bs-body-bg, Canvas));
        color: var(--bs-body-color, CanvasText);
        border: 1px solid rgba(128, 128, 128, .4);
        border-radius: .375rem;
        box-shadow: 0 .5rem 1rem rgba(0, 0, 0, .15);
    }

    .fo-airport-results[hidden] {
        display: none;
    }

    .fo-airport-option,
    .fo-airport-empty {
        display: block;
        width: 100%;
        padding: .5rem .75rem;
        border: 0;
        background: transparent;
        color: inherit;
        font: inherit;
        text-align: left;
    }

    .fo-airport-option {
        cursor: pointer;
    }

    .fo-airport-option:hover,
    .fo-airport-option:focus {
        background: rgba(128, 128, 128, .2);
        outline: none;
    }

    .fo-airport-empty {
        opacity: .7;
    }
</style>
<section id="flightops">
    <div class="d-flex flex-wrap align-items-center gap-3 mb-4">
        <div>
            <h1 class="h3 fw-bold mb-1">Flight Operations</h1>
            <p class="text-secondary mb-0">Book your next flight and view your bids </p>
        </div>
    </div>
</section>

{{-- Self-contained alert styling: this theme doesn't ship Bootstrap's .alert / .btn-close CSS.
Translucent colours so it reads on both light and dark themes. --}}


@if(session('success'))
    <div class="fo-alert fo-alert-success" role="alert">
        {{ session('success') }}
        <button type="button" class="fo-alert-close" aria-label="Close">&times;</button>
    </div>
@endif

{{-- The controller flashes 'error' on failures; the old view never displayed it. --}}
@if(session('error'))
    <div class="fo-alert fo-alert-danger" role="alert">
        {{ session('error') }}
        <button type="button" class="fo-alert-close" aria-label="Close">&times;</button>
    </div>
@endif

@if($errors->any())
    <div class="fo-alert fo-alert-danger" role="alert">
        <ul>
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="fo-alert-close" aria-label="Close">&times;</button>
    </div>
@endif

{{--  --}}
<div class="row">
    <div class="col-lg-3">
        <div class="card border-0 shadow-sm bg-white">
            <div class="card-body pb-2 align-items-center">
                <h2 class="h6 fw-semibold">Create new flight</h2>
                <form method="POST" action="{{ route('flightoperations.create-flight') }}">
                    @csrf

                    {{-- Aircraft Selection --}}
                    <div class="form-group mb-3">
                        <label for="aircraft_id" class="mb-1">Aircraft</label>
                        <select name="aircraft_id" id="aircraft_id" class="form-select" required>
                            @foreach($aircraft as $ac)
                                <option value="{{ $ac->id }}" {{ old('aircraft_id') == $ac->id ? 'selected' : '' }}>{{ $ac->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group mb-3">
                        <label for="flight_number" class="mb-1">Flight Number</label>
                        <div class="input-group">
                            <input type="number" name="flight_number" id="flight_number" class="form-control" min="{{ $flightNumberMin }}" max="{{ $flightNumberMax }}" value="{{ old('flight_number') }}" required>
                            <button type="button" id="generate_flight_number" class="btn btn-warning">Random</button>
                        </div>
                    </div>
                    @foreach(['dpt' => ['Origin', 'selectorigin'], 'arr' => ['Destination', 'selectdestination']] as $prefix => [$labelKey, $placeholderKey])
                    @php($picked = $oldAirports->get((string) old($prefix . '_airport_id')))
                    <div class="form-group mb-3 airport-picker" data-prefix="{{ $prefix }}">
                        <label for="{{ $prefix }}_airport_search" class="mb-1">{{ $labelKey }}</label>
                        <input type="text" id="{{ $prefix }}_airport_search" class="form-control" autocomplete="off" required placeholder="Begin typing to search" value="{{ $picked ? $picked->icao . ' - ' . $picked->name : '' }}">
                        <input type="hidden" name="{{ $prefix }}_airport_id" id="{{ $prefix }}_airport_id" value="{{ old($prefix . '_airport_id') }}">
                        <div class="fo-airport-results airport-results" hidden></div>
                    </div>
                    @endforeach
                    <button type="submit" class="btn btn-warning">Create Flight &amp; Add to Bids</button>
                </form>
            </div>
        </div>
    </div>
    <div class="col-lg-9">
        <div class="card border-0 shadow-sm bg-white">
            <div class="card-body pb-2 align-items-center">
                
                <h2 class="h6 fw-semibold">View your active bids</h2>
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th scope="col">Flight Number</th>
                            <th scope="col">Departure</th>
                            <th scope="col">Arrival</th>
                            <th scope="col">Distance</th>
                            <th scope="col">Created</th>
                            <th scope="col"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($bids as $bid)
                        @php($flight = $bid->flight)
                        @continue(!$flight)
                        @php($flightNumber = ($flight->airline?->icao) . $flight->flight_number)
                        @php($simbrief = $simbriefs->get($bid->flight_id))
                        <tr>
                            <td>{{ $flightNumber }}</td>
                            <td>{{ $flight->dpt_airport?->icao }}</td>
                            <td>{{ $flight->arr_airport?->icao }}</td>
                            <td>{{ $flight->distance }}nm</td>
                            <td>{{ $bid->created_at?->diffForHumans() }}</td>
                            <td>
                                <div class="dropdown">
                                    <a href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false" class="dropdown-toggle">Actions</a>
                                    <div class="dropdown-menu dropdown-menu-dark dropdown-menu-end">
                                        <div class="dropdown-item">
                                            <div class="mb-1">
                                                <button type="button" class="btn btn-sm btn-danger remove-bid w-100" data-url="{{ route('flightoperations.delete-bid', ['bidId' => $bid->id]) }}" data-flight-number="{{ $flightNumber }}">Remove Bid</button>
                                            </div>
                                            @if($simbriefEnabled)
                                                <div class="mb-1">
                                                    @if($simbrief)
                                                        <a href="{{ route('frontend.simbrief.briefing', $simbrief->id) }}" class="btn btn-sm btn-secondary w-100">View SimBrief Flight Plan</a>
                                                    @else
                                                        <a href="{{ route('frontend.simbrief.generate', ['flight_id' => $flight->id, 'aircraft_id' => $bid->aircraft_id]) }}" class="btn btn-sm btn-secondary w-100">Plan with SimBrief</a>
                                                    @endif
                                                </div>
                                            @endif
                                            <div class="mb-1">
                                                <a href="vmsacars:bid/{{ $bid->id }}" class="btn btn-sm btn-primary w-100">Send to VMSACARS</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted">No active bids</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- Native <dialog>: hidden by the browser until showModal(), so it doesn't depend on the theme shipping Bootstrap's modal CSS/JS.
    Don't put .card (display:flex) on the <dialog> itself or it would override the browser's "hidden when closed" rule. --}}
        <dialog id="remove_bid_dialog" class="p-0 border-0 bg-transparent" style="width: 90%; max-width: 28rem;">
            <div class="card">
                <div class="card-body text-center">
                    <span class="mdi mdi-close-circle-outline display-1 text-danger"></span>
                    <p id="remove_bid_message" class="mb-0"></p>
                </div>
                <div class="card-footer d-flex justify-content-center gap-2">
                    {{-- Plain form POST: CSRF-protected, flash messages work, no XHR/API-key needed. --}}
                    <form id="remove_bid_form" method="POST" action="">
                        @csrf
                        <button type="submit" class="btn btn-success">Yes, delete.</button>
                    </form>
                    <button type="button" id="remove_cancel" class="btn btn-danger">No, don't delete.</button>
                </div>
            </div>
        </dialog>
        @endsection

        @section('scripts')
            <script>
                document.addEventListener('DOMContentLoaded', () => {
                    // Dismiss buttons on the alerts (no Bootstrap JS needed)
                    document.querySelectorAll('.fo-alert-close').forEach((btn) => {
                        btn.addEventListener('click', () => btn.closest('.fo-alert').remove());
                    });

                    // Flight number generator (range comes from Config/config.php)
                    const flightNumber = document.getElementById('flight_number');
                    const min = {{ (int) $flightNumberMin }};
                    const max = {{ (int) $flightNumberMax }};

                    const generateFlightNumber = () => {
                        // .value, not setAttribute('value'): the attribute stops updating the field once the user has typed in it
                        flightNumber.value = Math.floor(Math.random() * (max - min + 1)) + min;
                    };

                    if (!flightNumber.value) {
                        generateFlightNumber();
                    }
                    document.getElementById('generate_flight_number').addEventListener('click', generateFlightNumber);

                    // Airport type-ahead: 2+ characters, debounced, max 20 results from the server
                    const searchUrl = @json(route('flightoperations.airports'));

                    document.querySelectorAll('.airport-picker').forEach((picker) => {
                        const prefix = picker.dataset.prefix;
                        const input = document.getElementById(prefix + '_airport_search');
                        const hidden = document.getElementById(prefix + '_airport_id');
                        const results = picker.querySelector('.airport-results');
                        let timer = null;
                        let requestSeq = 0;

                        const closeResults = () => {
                            results.hidden = true;
                            results.replaceChildren();
                        };

                        const choose = (item) => {
                            hidden.value = item.id;
                            input.value = item.label;
                            input.setCustomValidity('');
                            closeResults();
                        };

                        const render = (items) => {
                            results.replaceChildren();

                            if (items.length === 0) {
                                const empty = document.createElement('div');
                                empty.className = 'fo-airport-empty';
                                empty.textContent = 'No airports found';
                                results.appendChild(empty);
                            }

                            items.forEach((item) => {
                                const option = document.createElement('button');
                                option.type = 'button';
                                option.className = 'fo-airport-option';
                                option.textContent = item.label; // textContent: never parse server data as HTML
                                option.addEventListener('click', () => choose(item));
                                results.appendChild(option);
                            });

                            results.hidden = false;
                        };

                        const search = async (term) => {
                            const seq = ++requestSeq;
                            try {
                                const response = await fetch(searchUrl + '?q=' + encodeURIComponent(term), {
                                    headers: { 'Accept': 'application/json' },
                                });
                                if (!response.ok) {
                                    throw new Error('HTTP ' + response.status);
                                }
                                const items = await response.json();
                                if (seq === requestSeq) { // ignore out-of-order responses
                                    render(items);
                                }
                            } catch (error) {
                                if (seq === requestSeq) {
                                    closeResults();
                                }
                            }
                        };

                        input.addEventListener('input', () => {
                            hidden.value = ''; // typing invalidates any earlier selection
                            input.setCustomValidity('');
                            clearTimeout(timer);

                            const term = input.value.trim();
                            if (term.length < 2) {
                                requestSeq++;
                                closeResults();
                                return;
                            }
                            timer = setTimeout(() => search(term), 250);
                        });

                        input.addEventListener('keydown', (event) => {
                            if (event.key === 'Escape') {
                                closeResults();
                            } else if (event.key === 'ArrowDown' || (event.key === 'Enter' && !results.hidden)) {
                                const first = results.querySelector('button');
                                if (first) {
                                    event.preventDefault();
                                    event.key === 'Enter' ? first.click() : first.focus();
                                }
                            }
                        });

                        results.addEventListener('keydown', (event) => {
                            const current = document.activeElement;
                            if (event.key === 'ArrowDown' && current.nextElementSibling) {
                                event.preventDefault();
                                current.nextElementSibling.focus();
                            } else if (event.key === 'ArrowUp') {
                                event.preventDefault();
                                (current.previousElementSibling || input).focus();
                            } else if (event.key === 'Escape') {
                                input.focus();
                                closeResults();
                            }
                        });

                        document.addEventListener('click', (event) => {
                            if (!picker.contains(event.target)) {
                                closeResults();
                            }
                        });
                    });

                    // Text alone isn't enough: an airport must have been picked from the list
                    const createForm = document.querySelector('form[action="{{ route('flightoperations.create-flight') }}"]');
                    createForm.addEventListener('submit', (event) => {
                        for (const prefix of ['dpt', 'arr']) {
                            const input = document.getElementById(prefix + '_airport_search');
                            if (!document.getElementById(prefix + '_airport_id').value) {
                                event.preventDefault();
                                input.setCustomValidity('Please pick an airport from the list.');
                                input.reportValidity();
                                return;
                            }
                        }
                    });

                    // Remove-bid confirmation dialog
                    const dialog = document.getElementById('remove_bid_dialog');
                    const form = document.getElementById('remove_bid_form');
                    const message = document.getElementById('remove_bid_message');

                    document.querySelectorAll('.remove-bid').forEach((btn) => {
                        btn.addEventListener('click', () => {
                            form.action = btn.dataset.url;
                            // textContent, not innerHTML, so data attributes can never inject markup
                            message.textContent = 'Are you sure you want to delete ' + btn.dataset.flightNumber +
                                '? Once confirmed, this cannot be undone.';
                            dialog.showModal();
                        });
                    });

                    document.getElementById('remove_cancel').addEventListener('click', () => dialog.close());

                    // Clicking the dimmed backdrop (outside the card) closes it; Escape works natively
                    dialog.addEventListener('click', (event) => {
                        if (event.target === dialog) {
                            dialog.close();
                        }
                    });
                });
            </script>
        @endsection