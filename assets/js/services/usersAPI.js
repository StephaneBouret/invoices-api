import axios from "axios";
import { USERS_API } from "../config";

function register(user) {
    return axios.post(
        USERS_API, user);
}

function find(id) {
    return axios
        .get(USERS_API + "/" + id)
        .then(response => response.data);
}

function update(currentId, user) {
    return axios.put(
        USERS_API + "/" + currentId, user);
}

export default {
    register,
    find,
    update
};