<template>
  <section>
    <div v-if="playerSrc" v-html="playerSrc"></div>
  </section>
</template>

<script>
import PlaylistRepository from "@/PlaylistRepository";

export default {
  name: "PlaylistSecondary",
  props: {
    channelId: String,
    playlistId: String,
  },
  mounted() {
    this.fetchPlaylistDetail();
  },
  data() {
    return {
      playerSrc: null
    }
  },
  methods: {
    async fetchPlaylistDetail() {
      const searchParams = new URLSearchParams();
      searchParams.append('part', 'contentDetails');
      searchParams.append('part', 'id');
      searchParams.append('part', 'player');
      searchParams.append('part', 'snippet');
      searchParams.append('part', 'status');
      searchParams.append('key', 'AIzaSyAq9YUJhA6zruSRQMXM-r_OJ8Yo4wkxH9Y');
      // searchParams.append('id', this.playlistId);
      searchParams.append('channelId', this.channelId);
      searchParams.append('maxResults', '1')
      const playlistDetail = await PlaylistRepository.get(searchParams);
      console.log(playlistDetail);
      this.playerSrc = playlistDetail.data.items[0].player.embedHtml;
    }
  }
}
</script>

<style scoped src="./PlaylistSecondary.css">

</style>
