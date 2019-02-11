import {FETCH_PRODUCTS_SUCCESS} from "./actionsTypes";
import axios from 'axios'

export function fetchProducts() {
    return async dispatch => {

        try {
            const response = await axios.get('http://192.168.99.100/api/products')
            const products = response.data.data

            dispatch(fetchTodosSuccess(products))
        } catch (e) {
            console.log(e)
        }
    }
}

export function fetchTodosSuccess(products) {
    return {
        type: FETCH_PRODUCTS_SUCCESS,
        products
    }
}