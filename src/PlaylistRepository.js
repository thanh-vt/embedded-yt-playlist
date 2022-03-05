import Repository from "@/Repository";

const resource = '/playlistItems'

export default {
  get(params) {
    return Repository.get(`${resource}`, {
      params: params
    });
  }
}
