import {FETCH_PRODUCTS_SUCCESS, FETCH_PRODUCT_SUCCESS} from "./actionsTypes"
import axios from 'axios'

export function fetchProducts() {
    return async dispatch => {
        const host = process.env.REACT_APP_API_PUBLIC_URL
            const response = await axios.get(`${host}/api/products?per_page=5`)
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

export function fetchProductById(productId) {
    return async dispatch => {
        const host = process.env.REACT_APP_API_PUBLIC_URL
        const response = await axios.get(`${host}/api/products/${productId}`)
        const product = response.data

        dispatch(fetchProductSuccess(product))
    }
}

export function fetchProductSuccess(product) {
    return {
        type: FETCH_PRODUCT_SUCCESS,
        product
    }
}
