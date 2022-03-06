<template>
  <div>
    <playlist-main v-if="info" :playlist-id="info.playlistId" :channel-id="info.channelId" :polling-interval="info.pollingInterval"/>
  </div>
</template>

<script>
import PlaylistMain from './components/PlaylistMain.vue'

const DEFAULT_CONFIG = {
  channelId: 'UCae_bZXgQM6ga2_dAo5A-Pg',
  playlistId: 'PLSgEVW8-8rnAz34S07_z_05ynlJftlvZE',
  pollingInterval: 300000
}

export default {
  name: 'App',
  components: {
    PlaylistMain
  },
  created() {
    // eslint-disable-next-line no-undef,no-prototype-builtins
    if (!window.hasOwnProperty('eypInfo')) {
      this.info = {
        ...DEFAULT_CONFIG
      }
    } else {
      this.info = {
        ...DEFAULT_CONFIG,
        // eslint-disable-next-line no-undef
        ...eypInfo
      };
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
