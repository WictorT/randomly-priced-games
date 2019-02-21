import {FETCH_PRODUCTS_SUCCESS} from "./actionsTypes";
import axios from 'axios'

export function fetchProducts() {
    return async dispatch => {
        const host = process.env.REACT_APP_API_PUBLIC_URL
        const response = await axios.get(`${host}/api/products`)
        const products = response.data.data

        dispatch(fetchProductsSuccess(products))
    }
}

export function fetchProductsSuccess(products) {
    return {
        type: FETCH_PRODUCTS_SUCCESS,
        products
    }
}
