import {FETCH_PRODUCT_SUCCESS, FETCH_PRODUCTS_SUCCESS, PRODUCTS_PAGINATION} from "./actionsTypes"
import axios from "axios"

export function fetchProducts(per_page = '', page = '') {
    return async dispatch => {
        let params = ''

        if (per_page !== '' && page !== '') {
            params = `/?per_page=${per_page}&page=${page}`
        } else if (per_page !== '') {
            params = `/?per_page=${per_page}`
        }

        const host = process.env.REACT_APP_API_PUBLIC_URL
        const response = await axios.get(`${host}/api/products${params}`)
        const products = response.data

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
