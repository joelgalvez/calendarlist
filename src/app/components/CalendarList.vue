<template>
    <div class="calendar-list">
        <div class="grid">
            <div class="calendar-area">
                <!-- <Dropdown :lists="lists"></Dropdown> -->
                <Calendar ref="calendar" @onCloseCalendar="onCloseCalendar" :show="showCalendarMobile" :domains="domains" ></Calendar>
            </div>
            <div class="panel">

				<!-- <div class="cross" @click="onClose">
					<svg version="1.1" baseProfile="tiny" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"
						x="0px" y="0px" width="20px" height="20px" viewBox="0 0 20 20" xml:space="preserve">
						<line fill="#FFFFFF" stroke="#000000" stroke-width="2" stroke-miterlimit="10" x1="0.5" y1="0.5" x2="19.4" y2="19.4"/>
						<line fill="#FFFFFF" stroke="#000000" stroke-width="2" stroke-miterlimit="10" x1="19.4" y1="0.5" x2="0.5" y2="19.4"/>
					</svg>
				</div> -->

                <div class="domains">
                    <div class="domain" v-for="domain in domains" :key="domain.name">
                        <DomainCheckbox
                            :ref="'checkbox-'+domain.name"
                            :checked="domain.loaded"
                            :domain="domain.webpage"
                            :error="domain.error"
                            :disabled="domain.disabled"
                            :loading="domain.loading"
                            :color="domain.color"
                            :title="domain.title"
                            :status="domain.status"
                            :errorStatus="domain.errorStatus"
                            :alternativeSource="domain.alternativeSource"
                            v-on:onClick="onClickCalendar(domain.name)"
                            >
                        </DomainCheckbox>
                    </div>
                </div>
                <div class="bwrap" v-if="!showCalendarMobile">
                    <DialogButton class="show-events" @click.native="onShowEventsMobile">Show Events</DialogButton>
                </div>

            </div>
        </div>
    </div>
</template>
<script>
import Modal from "./Modal.vue";
import DialogButton from "./DialogButton.vue";

import ICAL from "ical.js"
import moment from 'moment';

import Calendar from "./Calendar.vue";
import DomainCheckbox from "./DomainCheckbox.vue";

export default {
	name: 'CalendarList',
    data: function() {
        return {
            showCalendarMobile: true,
            domains: {}
        }
    },
	props: {
        // dictionary: {},
        // content: {}
        src: String
	},
	components: {
		Modal: Modal,
		DialogButton: DialogButton,

        DomainCheckbox,
		Calendar

    },
    methods: {
        stripDomain(str) {
            return str.replace('https://', '').replace('http://','');
        },
        validURL(str) {
            var pattern = new RegExp('^(https?:\\/\\/)?'+ // protocol
                '((([a-z\\d]([a-z\\d-]*[a-z\\d])*)\\.)+[a-z]{2,}|'+ // domain name
                '((\\d{1,3}\\.){3}\\d{1,3}))'+ // OR ip (v4) address
                '(\\:\\d+)?(\\/[-a-z\\d%_.~+]*)*'+ // port and path
                '(\\?[;&a-z\\d%_.~+=-]*)?'+ // query string
                '(\\#[-a-z\\d_]*)?$','i'); // fragment locator
            return !!pattern.test(str);
        },
        onCloseErrorModal() {
            this.showError = false;
        },
        onShowEventsMobile () {
            this.showCalendarMobile = true;
        },
        onCloseCalendar () {
            this.showCalendarMobile = false;
        },
        genColor (seed) {
            let color = Math.floor((Math.abs(Math.sin(seed) * 16777215)) % 16777215);
            color = color.toString(16);
            // pad any colors shorter than 6 characters with leading 0s
            while(color.length < 6) {
                color = '0' + color;
            }

            return color;
        },
        loadCalendar(name) {

            if(this.domains[name].disabled || this.domains[name].loaded || this.domains[name].loading) {
                return;
            }

            this.domains[name].loading = true;


            fetch(`proxy.php?name=${name}`)
            .then(response => response.text())
            .then((data) => {

                this.domains[name].loading = false;

                // if(o.apoHeaders.error) {
                // 	this.domains[name].error = true;
                // 	this.domains[name].errorStatus = (o.apoHeaders.message?(o.apoHeaders.message+' '):'') + '(code ' + o.apoHeaders.code + ')';
                // }


                const jcalData = ICAL.parse(data);

                const comp = new ICAL.Component(jcalData);
                const eventComps = comp.getAllSubcomponents("vevent");

                const today = moment();
                const futureEvents = eventComps.filter( item => {
                    const startDate = item.getFirstPropertyValue('dtstart')
                    const start = moment(startDate.toString());
                    return start >= today;
                });

                this.domains[name].status = `${futureEvents.length}&nbsp;events`;

                //from https://stackoverflow.com/questions/9404685/import-ical-ics-with-fullcalendar
                const events = eventComps.map( item => {


                    const toreturn = {
                        "title": this.stripDomain(this.domains[name].title) + ' - ' + item.getFirstPropertyValue("summary"),
                        "domain": name,
                        "location": item.getFirstPropertyValue("location"),
                        "description": item.getFirstPropertyValue("description"),
                        "url": item.getFirstPropertyValue("url"),
                        "image": item.getFirstPropertyValue("attach"),
                        "uid": item.getFirstPropertyValue("uid")
                    };

                    toreturn.start = item.getFirstPropertyValue("dtstart").toString();

                    if (item.getFirstPropertyValue("dtend")) {
                        toreturn.end = item.getFirstPropertyValue("dtend").toString();
                    }

                    if (item.getFirstPropertyValue('rrule')) {
                        toreturn.rrule = item.getFirstPropertyValue('rrule').toString();
                    }

                    return toreturn;

                });


                this.domains[name].events = events; //might not need to be reactive
                // this.$set(this.domains[name], 'test', 'test');

                let c = '#'+this.genColor(this.hashCode(this.domains[name].title));


                this.$set(this.domains[name], 'color', c);
                this.$set(this.domains[name], 'loaded', true);


                this.$refs.calendar.getApi().addEventSource({
                    id: name,
                    events: events,
                    color: this.domains[name].color
                })




            })
        },
        unloadCalendar(domainName) {
            if(this.domains[domainName].loaded) {
                this.$set(this.domains[domainName], 'status', '');
                this.$set(this.domains[domainName], 'loaded', false);
                this.$refs.calendar.getApi().getEventSourceById(domainName).remove();
                this.$forceUpdate();
            }
        },
        unloadAllCalendars() {
            for(let domainName of Object.keys(this.domains)) {
                if(this.domains[domainName].loaded) {
                    this.$set(this.domains[domainName], 'status', '');
                    this.$set(this.domains[domainName], 'loaded', false);
                    this.$refs.calendar.getApi().getEventSourceById(domainName).remove();
                    this.$forceUpdate();
                }
            }
        },
        onClickCalendar(domainName) {

            if(!this.domains[domainName].loaded) {
                this.loadCalendar(domainName);
            } else {
                this.unloadCalendar(domainName);
            }

        },
        hashCode(string) {
            var hash = 0, i, chr;
            for (i = 0; i < string.length; i++) {
                chr   = string.charCodeAt(i);
                hash  = ((hash << 5) - hash) + chr;
                hash |= 0; // Convert to 32bit integer
            }
            return hash;
        },
        loadCalendars() {
            this.domains = {};
            fetch(this.$root.src)
            .then(response => response.text())
            .then((data) => {
                let list = JSON.parse(data);

                for(let cal of list) {

                    let d = {
                        'name': cal.name,
                        'title': cal.title,
                        'webpage': cal.webpage,
                        'disabled': cal.ics==''
                    }
                    this.$set(this.domains, cal.name, d );

                    if(!d.disabled) {
                        this.loadCalendar(d.name);
                    }


                }
            })
        }
    },
    mounted() {
        this.loadCalendars();
    }
}

</script>
<style lang="scss" scoped>

.calendar-list {
    .grid {
        padding:0.5rem 0;
        @media (min-width:930px) {
            display:grid;
            grid-template-columns: 1fr auto;
            padding:0.5rem;
            grid-gap:0.5rem;

        }

    }

    background-color: white;

    .panel {

        // grid-column: 1 / 2;
        width:18rem;

        overflow-y:scroll;

        @media (max-width: 930px) {
            width:100%;
        }
        // flex: 1 1 auto;
        // font-size:1.15rem;
        // padding-top:1rem;
        // @media (min-width: 931px) {
            // padding-top:4rem;
        // }
        line-height:1.15;
        font-weight:300;

        // resize: horizontal;

        // background-color:gray;

        .subscribe {
            margin:2rem;
            margin-top: 4.25rem;
            display:inline-block;
			// display:flex;
			// justify-content: center;
			// // align-items: center;

            // // font-size: 1.6rem;
            // line-height:1.3;
            // font-weight: 500;
            // color:white;
			// margin:1.5rem;
            // margin-top:4.25rem;
            // text-shadow: 0 0 0.25rem rgba(255, 255, 255, 0.1) ;
			// display:block;
			// padding-bottom: 1px;;
            // text-align: center;
            // text-decoration: underline;
			// // background-color:#ff4c4c;
			// // border:2px solid red;
			// cursor: pointer;
            // color:red;
			// // background-color: white;
			// // margin: 0.5rem 1rem;
			// // padding: 0.25rem 0.75rem;
			// // border-radius: 0.5rem;
			// // background-color: #f96969;
            // // border:2px solid;
			// // border: 2px solid;
            // // border-radius:0.25rem;
            // padding:0.75rem;

			box-shadow: 0 0 1rem rgba(255,100,100,0.8);

		}
		.cross {
			svg {
				width:100%;
				height:100%;
			}
			cursor: pointer;
			width:2rem;
			margin:0rem 1rem;
			height:100%;
			display:flex;
			justify-content: center;
			align-items: center;
		}

        .domains {
            margin-bottom: 10rem;
        }

        .bg {
            display:none;
            @media (max-width: 930px) {
                display:block;
            }
            pointer-events: none;
            background: linear-gradient(to bottom, rgba(255, 255, 255, 0), rgba(255, 255, 255, 1), rgba(255, 255, 255, 1));
            height:12rem;
            position:fixed;
            bottom:0;
            left:0;
            width:100%;
        }

        .bwrap {
            position:fixed;
            bottom:2.5rem;
            left:0;
            width:100%;
            margin-bottom:2rem;
            .show-events {
                display:none;
                @media (max-width: 930px) {
                    display:inline-block;
                }
            }
            text-align: center;
        }

    }
    .calendar {


        // box-shadow: 0 0 2rem rgba(0, 0, 0, 0.267);

        background-color:white;
        @media (max-width: 930px) {
            width:100%;
            grid-row: 2 / 3;

            display:none;
            pointer-events:none;
            .fc-center {
                // width:100%;
                // h2 {
                // 	font-size: 1rem;
                // 	font-weight: 500;
                // }
                display:none;
            }
        }
        &.show {
            z-index:2;
            @media (max-width: 930px) {
                position:absolute;

                // height:100vh;
            }
            display:block;
            pointer-events:all;
        }

    }
}

</style>
