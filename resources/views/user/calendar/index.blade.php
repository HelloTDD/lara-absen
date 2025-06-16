@extends('layouts.app')
@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Calendar</h3>
            </div>
            <div class="card-body">
                <div id="calendar"></div>
            </div>
        </div>
    </div>
</div>
@endsection
@push('scripts')
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.17/index.global.min.js'></script>
    <script>
        $(document).ready(function() {
            var calendarEl = $('#calendar');
            var calendar = new FullCalendar.Calendar(calendarEl[0], {
            initialView: 'dayGridMonth',
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek,timeGridDay'
            },
            events: {!! json_encode($event_finals) !!},
            editable: true,
            navLinks: true, // can click day/week names to navigate views
            selectable: true,
            selectMirror: true,
            select: function(arg) {
                let title = prompt('Event Title:');
                $.ajax({
                    url: '{{ route('calendar.store') }}',
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        title: title,
                        start_date: arg.startStr,
                        end_date: arg.endStr,
                        allDay: arg.allDay
                    },
                    success: function(e) {
                        if (e.status === 'success') {

                             Swal.fire({
                                    icon: 'success',
                                    title: 'Berhasil',
                                    text: e.message || 'Success add the event.'
                            });

                            if (title) {
                                calendar.addEvent({
                                    title: title,
                                    start: arg.start,
                                    end: arg.end,
                                    allDay: arg.allDay
                                })
                            }
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error:', error);
                    }
                });
                calendar.unselect()
            },
            eventClick: function(arg) {
                // console.log(arg.event.id);
                if (confirm('Are you sure you want to delete this event?')) {
                    $.ajax({
                        url: '{{ url("/calendar/delete/") }}/' + arg.event.id,
                        type: 'DELETE',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            if (response.status === 'success') {

                                Swal.fire({
                                    icon: 'success',
                                    title: 'Success',
                                    text: response.message || 'Event deleted successfully.'
                                });

                                arg.event.remove();
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: response.message || 'Failed to delete the event.'
                                });
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error('Error:', error);
                        }
                    });
                }
            },
            dayMaxEvents: true
            });
            calendar.render();
        });
    </script>
@endpush