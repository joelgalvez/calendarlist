<template>
  <div class="event page">
    <Modal @close="onClose">
      <div class="event" v-if="event">
        <h2>{{event.title}}</h2>
        <div class="dates">
          Start: {{getStart()}}<br>
          End: {{getEnd()}}
        </div>
        <img v-if="event.image" :src="event.image" alt="">
        <template v-if="html(event.description)">
          <div class="description" v-html="event.description"></div>
        </template>
        <template v-else>
          <div class="description" v-html="linebreaks(event.description)"></div>
        </template>
        
        <a :href="event.url" target="_blank">{{event.url}}</a>
        <h3>Website</h3>
        <a :href="domains[domain.name].webpage" target="_blank">{{domains[domain.name].webpage}}</a>
      </div>
    </Modal>
  </div>
</template>
<script>
import Modal from "./Modal.vue";
import DialogButton from "./DialogButton.vue";

import moment from 'moment';
import isHTML from 'is-html';

export default {
	name: 'Event',
	props: {
        dictionary: {},
        domain: {},
        event: {},
        domains: {},
        content: {}
	},
	components: {
		Modal: Modal,
		DialogButton: DialogButton
    },
    methods: {
      html(str) {
        if(str) {
          return isHTML(str);
        } else {
          return false;
        }
      },
      getStart() {
        return moment(this.event.start).format("LLL");
      },
      getEnd() {
        return moment(this.event.end).format("LLL");
      },
      onClose() {
        this.$emit('close');
      },
      linebreaks(str) {
        if(str) {
          return str.replace(/(?:\r\n|\r|\n)/g, '<br>');
        } else {
          return str;
        }
      },
      getEvent() {
        const domainObj = this.domains[this.content.name];
        const eventsArr = domainObj.events.filter(event => event.uid === this.$route.params.event);
        if(eventsArr.length<1) {
          alert('could not find event');
        } else {
          return eventsArr[0];
        }
      }
    },
    mounted() {
      
    }
}
</script>
<style lang="scss" scoped>

  img {
    max-width: 100%;
    height: auto;
  }
  .dates {
    font-size: 0.8rem;
    margin: 1rem 0;
  }
  a {
    color:red;
  }

  h3 {
    margin-top:3rem;
  }
  b {
    font-weight: bold;
  }
  i {
    font-style:italic;
  }

</style>
