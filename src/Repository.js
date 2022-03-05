import axios from 'axios'

const baseDomain = 'https://youtube.googleapis.com/youtube/v3'
const baseUrl = `${baseDomain}`;

export default axios.create({
  baseURL: baseUrl
})
