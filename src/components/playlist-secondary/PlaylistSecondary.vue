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
  async mounted() {
    const playlistId = await this.fetchPlaylistDetail();
    await this.fetchPlaylistItems(playlistId);
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
      if (this.playlistId) {
        searchParams.append('id', this.playlistId);
      } else {
        searchParams.append('channelId', this.channelId);
      }
      searchParams.append('maxResults', '1')
      const playlistDetail = await PlaylistRepository.get(searchParams);
      if (playlistDetail.data.items.length) {
        this.playerSrc = playlistDetail.data.items[0].player.embedHtml;
        // const height = this.playerSrc.match(/(?<=height=").*?(?=[\\*"])/g);
        // this.playerSrc = this.playerSrc.replace(height, "90%");
        // const width = this.playerSrc.match(/(?<=width=").*?(?=[\\*"])/g);
        // this.playerSrc = this.playerSrc.replace(width, "93%");
        console.log(this.playerSrc);
        const height = this.playerSrc.match(/(height="\d+")/g);
        this.playerSrc = this.playerSrc.replace(height, "height=\"90%\"");
        const width = this.playerSrc.match(/width="\d+"/g);
        this.playerSrc = this.playerSrc.replace(width, "width=\"93%\"");
        this.playerSrc = this.playerSrc.replace('http:', "https:");
        return playlistDetail.data.items[0].id;
      } else return null;
    },
    async fetchPlaylistItems(playlistId) {
      const searchParams = new URLSearchParams();
      searchParams.append('part', 'contentDetails');
      searchParams.append('part', 'id');
      searchParams.append('part', 'snippet');
      searchParams.append('part', 'status');
      searchParams.append('key', 'AIzaSyAq9YUJhA6zruSRQMXM-r_OJ8Yo4wkxH9Y');
      searchParams.append('playlistId', playlistId);
      searchParams.append('maxResults', '5');
      const playlistData = await PlaylistItemRepository.get(searchParams);
      this.videoItems = playlistData.data.items.map(e => {
        return {
          id: e.snippet.resourceId.videoId,
          title: e.snippet.title
        }
      });
      setTimeout(() => {
        const eypHeader = document.querySelector('.eyp-header');
        eypHeader.style.animationDuration = (eypHeader.clientWidth) / (100) +"s";
      }, 0);
    }
  }
}
</script>

<style scoped src="./PlaylistSecondary.css">

</style>
