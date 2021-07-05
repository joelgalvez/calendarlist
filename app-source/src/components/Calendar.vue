<template>
    <div :class="{calendar:true,show:show}" ref="calendar">
        <div class="fullcalendar">
            <FullCalendar ref="fullCalendar" :options="options" />
        </div>
        <div class="select-calendars" ref="closeCalendar" @click="onCloseCalendar">
            Select Calendars
        </div>
        <!--    This below is a bit hacky, would be better to solve this with the router
                But also want to avoid using a router at all, for the time being -->
        <Event v-if="showEvent" @close="onClose" :domain="currentDomain" :event="currentEvent" :domains="domains" :dictionary="dictionary"></Event>
    </div>
</template>
<script>

    import Event from './Event.vue';
    import FullCalendar from '@fullcalendar/vue';
    import interactionPlugin from '@fullcalendar/interaction'

	// import rrulePlugin from '@fullcalendar/rrule';

	import dayGridPlugin from '@fullcalendar/daygrid';
	import timeGridPlugin from '@fullcalendar/timegrid';
    import listPlugin from '@fullcalendar/list';

    export default {
        name: 'Calendar',
        props: {
            //for mobile
            show: {default: false, type: Boolean},
            domains: Object,
            dictionary: Object
        },
        components: {
            FullCalendar,
            Event
        },
        data: function() {
            return {
                currentDomain: null,
                currentEvent: null,
                showEvent: false,
                options: {
                    listDaySideFormat: true,
                    height: 'calc(100vh - 1rem)',

                    plugins: [ dayGridPlugin, timeGridPlugin, listPlugin, interactionPlugin],
                    headerToolbar: {
                        left: 'dayGridMonth,timeGridWeek,timeGridDay,list60',
                        center: 'title',
                        right: 'today,prev,next'
                    },
                    initialView: 'list60',
                    dateClick: this.onDateClick,
                    eventClick: this.onEventClick,
                    views: {
                        list60: {
                            type: 'list',
                            duration: { 
                                days: 60 
                            },
                            buttonText: 'list',
                            listDayFormat: { month: 'short', day: 'numeric', weekday: 'long' }
                        }
                    }
                }
            }
        },
        mounted() {

        },
        methods: {
            onClose() {
                this.showEvent = false;
            },
            getApi() {
                return this.$refs.fullCalendar.getApi();
            },
            onCloseCalendar() {
                this.$emit('onCloseCalendar');
            },
            onDateClick(e) {
            },
            onEventClick(info) {
                // console.log(info);
                this.showEvent = true;
                info.jsEvent.preventDefault(); // Default is to go the url

                const domainName = info.event._def.extendedProps.domain;
                const uid = info.event._def.extendedProps.uid;
                
                this.currentDomain = this.domains[domainName];

                this.currentEvent = this.currentDomain.events.find(event => { 
                    console.log(event.uid);
                    return event.uid == uid;
                });
            }
        }
    }
</script>

<style lang="scss">



    .fullcalendar *,
    .fullcalendar *::after,
    .fullcalendar *::before {
		box-sizing: content-box;
	}

    .calendar {

        .fc-button-primary:not(:disabled):active, 
        .fc-button-primary:not(:disabled).fc-button-active {
            text-decoration: underline;;
            color:black;
        }
        @media (max-width: 930px) {
            .fc-timeGridWeek-button,
            .fc-dayGridMonth-button {
                // display:none;
            }
            .fc-toolbar-title {
                margin: 0.5rem;
            }
        }
        button {
            color:black !important;
            background-color:white !important;
            border:0;
        }

        .select-calendars {
            &.hidden {
                display:none;
            }
            display:none;
            @media (max-width: 930px) {
                display:block;
            }
            position:fixed;
            top:4.5rem;
            left:0rem;
            
            // padding:0.5rem;
            background-color:white;
            width:5rem;
            height:1.5rem;
            margin:0.5rem;
            text-decoration: underline;
            cursor:pointer;
            svg {
                width:100%;
                height:100%;
                line {
                    fill: none;
                }
            }

        }

        .fc .fc-toolbar-title {
            font-size: 1rem;
        }
        .fc .fc-button-primary:disabled {
            color:#999;
        }

        .fc .fc-cell-shaded, .fc .fc-day-disabled {
            // padding-top:2em;
            // background-color: white;
            // z-index: 20;
        }
        // .fc-list-table .fc-list-sticky > * {
        //     position:static !important;
        // }
        @media (max-width: 600px) {
            .fc-list-event-time {
                width: 5rem;
                white-space: initial;
            }
        }
        @media (max-width: 930px) {
            .fc .fc-toolbar {
                text-align:center;
                display: block;;
            }
        }
    }

</style>