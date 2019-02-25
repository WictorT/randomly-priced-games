import {FETCH_PRODUCT_SUCCESS, FETCH_PRODUCTS_SUCCESS} from "./actionsTypes"
import axios from "axios"

export function fetchProducts(queryParams = null) {
    return async dispatch => {
        const host = process.env.REACT_APP_API_PUBLIC_URL
        const response = await axios.get(`${host}/api/products`, queryParams)
        const products = response.data

        updatePerPage(queryParams)

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

export function updatePerPage(queryParams) {

    if (queryParams !== null) {
        const { params } = queryParams
        localStorage.setItem('perPage', params.per_page)
    }
}
