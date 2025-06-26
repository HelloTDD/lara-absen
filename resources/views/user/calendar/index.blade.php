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
        $(document).ready(function () {
            let shift = {!! json_encode($shift) !!};
            let html = `<input type="text" name="title" id="title" class="swal2-input" placeholder="Event Title">
                            <div id="shift" class="d-none d-flex justify-content-center">
                                <select name="shift" class="swal2-input me-0 shifts" disabled>
                                    <option value="">Select Shift</option>`;

                                    for (let i = 0; i < shift.length; i++) {
                                        html += `<option value="${shift[i].id}">${shift[i].shift_name}</option>`;
                                    }

                        html += `</select>
                                <select name="overtime" class="swal2-input ms-2 shifts" disabled>
                                    <option value="off">Tidak Lembur</option>
                                    <option value="LEMBUR">Lembur</option>
                                    <option value="HOLYDAY">Libur</option>
                                </select>
                            </div>
                            <div class="d-flex justify-content-center gap-3 mt-3 align-items-center mb-2">
                                <div class="swal2-input-group">
                                    <input type="radio" name="types" placeholder="Event Title" value="event" checked>
                                    <label class="swal2-label">Event</label>
                                </div>
                                <div class="swal2-input-group">
                                    <input type="radio" name="types" placeholder="Event Title" value="shift">
                                    <label class="swal2-label">Shift</label>
                                </div>
                            </div>`;

            var calendarEl = $('#calendar');
            var calendar = new FullCalendar.Calendar(calendarEl[0], {
                initialView: 'dayGridMonth',
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth'
                },
                events: {!! json_encode($event_finals) !!},
                editable: true,
                navLinks: true, // can click day/week names to navigate views
                selectable: true,
                selectMirror: true,
                select: function (arg) {
                    Swal.fire({
                        html: html,
                        title: "Input Event",
                        showCancelButton: true,
                        confirmButtonText: 'Save',
                        preConfirm: () => {
                            const title = document.getElementById('title').value;
                            const shift = document.querySelector('select[name="shift"]').value;
                            const overtime = document.querySelector('select[name="overtime"]').value;
                            const type = document.querySelector('input[name="types"]:checked').value;
                            console.log(overtime);
                            if ((!title && type === 'event') || (type === 'shift' && !shift)) {
                                if (type === 'shift') {
                                    Swal.showValidationMessage('Please select a shift');
                                } else {
                                    Swal.showValidationMessage('Please enter a title');
                                }
                                return false;
                            }


                            return { title, type, shift, overtime };
                        }
                    }).then((result) => {
                        let data_event, type_cal;
                        if (result.value.type === 'shift') {
                            data_event = result.value.shift;
                            type_cal = 'shift';
                            overtime = result.value.overtime;
                        } else {
                            data_event = result.value.title;
                            type_cal = 'event';
                            overtime = '';
                        }
                        if (result.isConfirmed) {
                            $.ajax({
                                url: '{{ route('calendar.store') }}',
                                type: 'POST',
                                data: {
                                    _token: '{{ csrf_token() }}',
                                    data: data_event,
                                    type: result.value.type,
                                    start_date: arg.startStr,
                                    end_date: arg.endStr,
                                    overtime: overtime
                                },
                                success: function (e) {
                                    let status;
                                    if (e.status === 'success') {
                                        status = e.status;
                                        calendar.addEvent({
                                            id: e.data?.id,
                                            title: "{{ Auth::user()->name }}:" + e.data?.title,
                                            start: arg.start,
                                            end: arg.end,
                                            allDay: arg.allDay,
                                            extendedProps: {
                                                type: type_cal
                                            }
                                        });
                                    } else {
                                        status = e.status;
                                    }
                                    Swal.fire({
                                        icon: status,
                                        title: status == 'success' ? 'Berhasil' : 'Gagal',
                                        text: e.message || 'Success add the event.'
                                    });
                                },
                                error: function (xhr, status, error) {
                                    console.error('Error:', [error, xhr, status]);
                                }
                            });
                        }
                    });

                    // Add event listener within the Swal dialog
                    $(document).on('change', 'input[name="types"]', function () {
                        if ($(this).val() === 'shift') {
                            $('#title').addClass('d-none');
                            $('#title').attr('disabled', true);

                            $('#shift').removeClass('d-none');
                            $('.shifts').attr('disabled', false);
                        } else {
                            $('#title').removeClass('d-none');
                            $('#title').attr('disabled', false);

                            $('#shift').addClass('d-none');
                            $('.shifts').attr('disabled', true);
                        }
                    });

                    calendar.unselect();
                },
                eventClick: function (arg) {
                    if (confirm('Are you sure you want to delete this event?')) {
                        $.ajax({
                            url: '{{ url("/calendar/delete/") }}/' + arg.event.id,
                            type: 'DELETE',
                            data: {
                                _token: '{{ csrf_token() }}'
                            },
                            success: function (response) {
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
                            error: function (xhr, status, error) {
                                console.error('Error:', error);
                            }
                        });
                    }
                },
                dayMaxEvents: true,
                eventDrop: function (e) {
                    let url, data;
                    let id = e.event.id;
                    let [type, actualId, userId] = id.split('_');
                    if (type === 'event') {
                        url = "{{ route('calendar.update', '') }}/" + actualId;
                        data = {
                            _token: '{{ csrf_token() }}',
                            _method: 'PUT',
                            start_date: e.event.startStr,
                            end_date: e.event.endStr
                        };
                    } else {
                        url = "{{ route('user-shift.ajax.update', '') }}/" + actualId;
                        data = {
                            _token: '{{ csrf_token() }}',
                            _method: 'PUT',
                            start_date_shift: e.event.startStr,
                            end_date_shift: e.event.endStr,
                            user_id: e.event.extendedProps.user,
                            shift_id: e.event.extendedProps.shift_id
                        };
                    }
                    console.log([type, actualId, userId])
                    $.ajax({
                        url: url,
                        type: "POST",
                        data: data,
                        success: function (response) {
                            console.log(response);
                        },
                        error: function (xhr, status, error) {
                            console.error('Error:', error);
                        }
                    })
                }
            });
            calendar.render();
        });
    </script>
@endpush