import Repository from "@/Repository";

const resource = '/videos'

export default {
  get(params) {
    return Repository.get(`${resource}`, {
      params: params
    });
  }
}
