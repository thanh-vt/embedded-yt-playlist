<template src="./PlaylistSecondary.html">
</template>

<script>
import PlaylistRepository from "@/PlaylistRepository";
import PlaylistItemRepository from '@/PlaylistItemRepository'

export default {
  name: "PlaylistSecondary",
  props: {
    channelId: String,
    playlistId: String,
  },
  mounted() {
    this.fetchPlaylistDetail();
    this.fetchPlaylistItems();
  },
  data() {
    return {
      playerSrc: null,
      videoItems: []
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
      if (playlistDetail.data.items.length) {
        this.playerSrc = playlistDetail.data.items[0].player.embedHtml;
        const height = this.playerSrc.match(/(?<=height=").*?(?=[\\*"])/g);
        this.playerSrc = this.playerSrc.replace(height, "540");
        const width = this.playerSrc.match(/(?<=width=").*?(?=[\\*"])/g);
        this.playerSrc = this.playerSrc.replace(width, "960");
      }
      setTimeout(() => {

        const eypHeader = document.querySelector('.eyp-header');
        eypHeader.style.animationDuration = (eypHeader.clientWidth) / (100) +"s";
        console.log(eypHeader.style.animationDuration);
      }, 1000);
    },
    async fetchPlaylistItems() {
      const searchParams = new URLSearchParams();
      searchParams.append('part', 'contentDetails');
      searchParams.append('part', 'id');
      searchParams.append('part', 'snippet');
      searchParams.append('part', 'status');
      searchParams.append('key', 'AIzaSyAq9YUJhA6zruSRQMXM-r_OJ8Yo4wkxH9Y');
      searchParams.append('playlistId', this.playlistId);
      searchParams.append('maxResults', '5');
      const playlistData = await PlaylistItemRepository.get(searchParams);
      this.videoItems = playlistData.data.items.map(e => {
        return {
          id: e.snippet.resourceId.videoId,
          title: e.snippet.title
        }
      })
    }
  }
}
</script>

<style scoped src="./PlaylistSecondary.css">

</style>
