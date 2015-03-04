$(function () {
    var date = new Date();
    var d = date.getDate();
    var m = date.getMonth();
    var y = date.getFullYear();


    $('#calendar-holder').fullCalendar({
        header: {
            right: 'prev,next today',
            left: 'title',
            center: 'month,agendaWeek,agendaDay'
        },
        buttonText: {
            prev: '<i class="icon-chevron-left cal_prev" />',
            next: '<i class="icon-chevron-right cal_next" />'
        },
        aspectRatio: 1.4,
        selectable: true,
        selectHelper: true,
        select: function () {
            calendar.fullCalendar('unselect');
        },
        editable: false,
        theme: false,

        eventColor: '#bcdeee',
        dayClick: function (date, allDay, jsEvent, view) {
            if (view.name == 'agendaDay')
                return;

            jQuery('#calendar-holder').fullCalendar('changeView', 'agendaDay');
            jQuery('#calendar-holder').fullCalendar('gotoDate', date);
        }, eventClick: function (calEvent, jsEvent, view) {

            if (view.name == 'agendaDay')
                return;

            jQuery('#calendar-holder').fullCalendar('changeView', 'agendaDay');
            jQuery('#calendar-holder').fullCalendar('gotoDate', calEvent._start);

        },
        axisFormat: 'HH:mm',
        timeFormat: {
            month: 'H:mm',
            agenda: 'H:mm{ - H:mm}'
        },
        firstHour: 8,
        minTime: 0,
        maxTime: 24,
        viewDisplay: function (view) {

        },
        eventSources: [
            {
                url: Routing.generate('fullcalendar_loader', {'start' : 0, 'end': 100000000000000000000}),
                type: 'POST',
                // A way to add custom filters to your event listeners
                data: {
                },
                error: function() {
                    //alert('There was an error while fetching Google Calendar!');
                }
            }
        ]


    })

});
