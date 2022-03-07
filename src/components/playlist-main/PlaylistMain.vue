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
    console.log('root', document.querySelector('.eyp-m-slides'));
    this.mSlideObserver = new IntersectionObserver(((entries) => {
      console.log(entries[entries.length ? entries.length - 1 : null].target);
    }), {
      root: document.querySelector('.eyp-m-slides'),
      rootMargin: '0px',
      threshold: [0.9]
    });

    //
    if (localStorage.getItem('newestVideoSeen')) {
      this.newestVideoSeen = JSON.parse(localStorage.getItem('newestVideoSeen'));
    }
    this.videoItems = [
      {
        type: 0,
        id: 'img1',
        imgSrc: 'image-1.jpg'
      },
      {
        type: 0,
        id: 'img2',
        imgSrc: 'image-2.jpg'
      },
      {
        type: 0,
        id: 'img3',
        imgSrc: 'image-3.png'
      },
      {
        type: 0,
        id: 'img4',
        imgSrc: 'image-4.jpg'
      },
      {
        type: 0,
        id: 'img5',
        imgSrc: 'image-5.jpg',
      },
      {
        type: 0,
        id: 'img6',
        imgSrc: 'image-6.jpg'
      },
      {
        type: 1,
        id: '>',
        btnText: '>'
      }
    ]
    setTimeout(() => {
      this.videoItems.forEach(video => {
        let target = document.getElementById(`eyp-m-slide-${video.id}`);
        console.log(target);
        this.mSlideObserver.observe(target);
      })
    })
    // this.fetchPlaylist(0);
    // setInterval(() => this.fetchPlaylistItems(0, true), this.pollingInterval)
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
      hasSeenNewestVideo: false,
      mSlideObserver: null
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
        prevBtn = [{type: -1, id: '<', btnText: '<'}, ]
      }
      let nextBtn = []
      if (this.nextPageToken) {
        nextBtn = [{type: 1, id: '>', btnText: '>'}]
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
    selectVideo(video) {
      // TODO
      // if (video !== this.selectedVideoItem && this.selectedVideoItem) {
      //   const first = document.querySelector(`div#eyp_vid_${this.selectedVideoItem.id} iframe`);
      //   if (first) {
      //     first.contentWindow.postMessage('{"event":"command","func":"' + 'pauseVideo' + '","args":""}', '*');
      //   }
      // }
      this.selectedVideoItem = video;


      // for mobile touch
      if (video.type === 0 && this.$refs.eypMContainer.style.visibility !== 'hidden') {
        const slideRef = this.$refs[`eypMSlide#${video.id}`];
        console.log(slideRef);
        if (slideRef) {
          slideRef.scrollIntoView({
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
