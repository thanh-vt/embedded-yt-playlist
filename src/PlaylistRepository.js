import Repository from "@/Repository";

const resource = '/playlists'

export default {
  get(params) {
    return Repository.get(`${resource}`, {
      params: params
    });
  }
}
