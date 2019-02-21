import {FETCH_PRODUCTS_SUCCESS, FETCH_PRODUCT_SUCCESS} from "../actions/actionsTypes"

const intialState = {
    products: [],
    loading: false,
    product: null,
}

export default function products(state = intialState, action) {
    switch (action.type) {
        case FETCH_PRODUCTS_SUCCESS: {

            return {
                ...state, loading: false, products: action.products
            }
        }

        case FETCH_PRODUCT_SUCCESS:
            return {
                ...state, loading: false, product: action.product
            }

        default: {
            return state
        }
    }
}