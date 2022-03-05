<template>
  <playlist-main v-if="info" :playlist-id="info.playlistId"/>
</template>

<script>
import PlaylistMain from './components/PlaylistMain.vue'

export default {
  name: 'App',
  components: {
    PlaylistMain
  },
  created() {
    // eslint-disable-next-line no-undef,no-prototype-builtins
    if (!window.hasOwnProperty('eypInfo')) {
      this.info = {
        playlistId: 'PLSgEVW8-8rnAz34S07_z_05ynlJftlvZE'
      }
    } else {
      // eslint-disable-next-line no-undef
      this.info = eypInfo;
    }
    console.log(this.info);
  },
  data() {
    return {
      info: null
    }
  }
}
</script>

<style src="./app.css">
#app {
  font-family: Avenir, Helvetica, Arial, sans-serif;
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
  text-align: center;
  color: #2c3e50;
  margin-top: 60px;
}
</style>
