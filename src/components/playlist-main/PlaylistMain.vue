<template src="./PlaylistMain.html">

</template>

<script>
import PlaylistRepository from "@/PlaylistRepository";
import VideoRepository from "@/VideoRepository";

export default {
  name: 'PlaylistMain',
  components: {
    // Carousel3d,
    // Slide
  },
  props: {
    channelId: String,
    playlistId: String,
    pollingInterval: {
      type: Number,
      default: 300000
    }
  },
  filters: {
    linkLabel(item, videoItems) {
      if (item.type) {
        return item.btnText;
      } else {
        return videoItems.filter(e => e.type === 0).indexOf(item) + 1
      }
    }
  },
  mounted() {
    if (localStorage.getItem('newestVideoSeen')) {
      this.newestVideoSeen = JSON.parse(localStorage.getItem('newestVideoSeen'));
    }
    this.fetchPlaylist(0);
    setInterval(() => this.fetchPlaylistItems(0, true), this.pollingInterval)
  },
  data() {
    return {
      videoItems: [],
      size: 5,
      total: 0,
      prevPageToken: null,
      nextPageToken: null,
      selectedVideoItem: null,
      newestVideoSeen: null,
      hasSeenNewestVideo: false
    }
  },
  computed: {
    headerText() {
      return this.newestVideoSeen ? this.newestVideoSeen.title : 'Không tìm thấy video';
    },
  },
  methods: {
    async fetchPlaylist(type) {
      try {
        if (!this.playlistId) {
          return
        }
        await this.fetchPlaylistItems(type, false);
      } catch (e) {
        console.error(e);
      }
    },
    convertDataAndSelectDefault(type, videoItems) {
      const videos = videoItems.data.items.map(e => {
        const src = e.player.embedHtml.match(/(?<=src=").*?(?=[\\*"])/g);
        const srcJs = `${src}?enablejsapi=1`;
        return {
          id: e.id,
          title: e.snippet.title,
          playerHtml: e.player.embedHtml.replace(src, srcJs),
          type: 0,
          thumbnail: e.snippet.thumbnails.standard?.url
        }
      })
      let prevBtn = []
      let selectedIndex = 0;
      if (this.prevPageToken) {
        prevBtn = [{type: -1, btnText: '<'}, ]
      }
      let nextBtn = []
      if (this.nextPageToken) {
        nextBtn = [{type: 1, btnText: '>'}]
      }
      if (type > 0) {
        selectedIndex = 0;
      } else if (type < 0) {
        selectedIndex = videos.length - 1;
      }
      this.videoItems = [
        ...prevBtn,
        ...videos,
        ...nextBtn
      ];
      this.selectVideo(videos[selectedIndex]);
    },
    async fetchPlaylistItems(type, checkNewestVideoSeen = false) {
      const searchParams = new URLSearchParams();
      searchParams.append('part', 'contentDetails');
      searchParams.append('part', 'id');
      searchParams.append('part', 'snippet');
      searchParams.append('part', 'status');
      searchParams.append('key', 'AIzaSyAq9YUJhA6zruSRQMXM-r_OJ8Yo4wkxH9Y');
      searchParams.append('playlistId', this.playlistId);
      searchParams.append('maxResults', '6')
      if (type > 0) {
        searchParams.append('pageToken', this.nextPageToken);
      } else if (type < 0) {
        searchParams.append('pageToken', this.prevPageToken);
      }
      const playlistData = await PlaylistRepository.get(searchParams);

      const playlistItems = playlistData.data.items;
      const videoIds = playlistItems.map(e => e.contentDetails.videoId);
      const firstItem = playlistData.data.items.length ? playlistData.data.items[0] : null;
      if (type === 0) {
        if (this.newestVideoSeen) {
          if (firstItem) {
            this.hasSeenNewestVideo = this.newestVideoSeen.id === firstItem.contentDetails.videoId;
          }
        } else {
          if (firstItem) {
            this.hasSeenNewestVideo = false;
            this.newestVideoSeen = {
              id: firstItem.contentDetails.videoId,
              title: firstItem.snippet.title
            };
          }
        }
        if (firstItem) {
          localStorage.setItem('newestVideoSeen', JSON.stringify({
            id: firstItem.contentDetails.videoId,
            title: firstItem.snippet.title
          }));
          if ((!this.hasSeenNewestVideo && checkNewestVideoSeen) || !checkNewestVideoSeen) {
            this.size = playlistData.data.pageInfo.resultPerPage;
            this.total = playlistData.data.pageInfo.totalResults;
            this.prevPageToken = playlistData.data.prevPageToken;
            this.nextPageToken = playlistData.data.nextPageToken;
            const videoItems = await this.fetchVideos(videoIds);
            this.convertDataAndSelectDefault(type, videoItems);
          }
        }
      } else if (type === 1 || type === -1) {
        this.size = playlistData.data.pageInfo.resultPerPage;
        this.total = playlistData.data.pageInfo.totalResults;
        this.prevPageToken = playlistData.data.prevPageToken;
        this.nextPageToken = playlistData.data.nextPageToken;
        const videoItems = await this.fetchVideos(videoIds);
        this.convertDataAndSelectDefault(type, videoItems);
      }
      return playlistData;
    },
    async fetchVideos(...videoIds) {
      const searchParams2 = new URLSearchParams();
      searchParams2.append('part', 'contentDetails');
      searchParams2.append('part', 'id');
      searchParams2.append('part', 'snippet');
      searchParams2.append('part', 'status');
      searchParams2.append('part', 'player');
      searchParams2.append('key', 'AIzaSyAq9YUJhA6zruSRQMXM-r_OJ8Yo4wkxH9Y');
      for (const videoId of videoIds) {
        searchParams2.append('id', videoId)
      }
      return await VideoRepository.get(searchParams2);
    },
    selectVideo(image) {
      if (image !== this.selectedVideoItem && this.selectedVideoItem) {
        const first = document.querySelector(`div#eyp_vid_${this.selectedVideoItem.id} iframe`);
        if (first) {
          first.contentWindow.postMessage('{"event":"command","func":"' + 'pauseVideo' + '","args":""}', '*');
        }
      }
      this.selectedVideoItem = image;
      const index = this.videoItems.indexOf(this.selectedVideoItem);

      // for mobile touch
      if (index >= 0) {
        const link = document.getElementById(`eyp-m-slide-${index + 1}`);
        if (link) {
          link.scrollIntoView({
            behavior: 'smooth'
          });
        }
      }
    }
  }
}
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped src="./PlaylistMain.css">
</style>
