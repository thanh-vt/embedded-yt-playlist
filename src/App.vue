<template>
  <div v-if="info">
    <playlist-main v-if="mode" :playlist-id="info.playlistId" :channel-id="info.channelId" :polling-interval="info.pollingInterval"/>
    <playlist-secondary v-else :playlist-id="info.playlistId" :channel-id="info.channelId"></playlist-secondary>
  </div>
</template>

<script>
import PlaylistMain from './components/playlist-main/PlaylistMain.vue'
import PlaylistSecondary from "@/components/playlist-secondary/PlaylistSecondary";

const DEFAULT_CONFIG = {
  channelId: 'UCae_bZXgQM6ga2_dAo5A-Pg',
  playlistId: 'PLSgEVW8-8rnAz34S07_z_05ynlJftlvZE',
  pollingInterval: 300000
}

export default {
  name: 'App',
  components: {
    PlaylistSecondary,
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
      mode: 0,
      info: null
    }
  }
}
</script>

<style src="./App.css">
#app {
  font-family: Avenir, Helvetica, Arial, sans-serif;
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
  text-align: center;
  color: #2c3e50;
  margin-top: 60px;
}
</style>
