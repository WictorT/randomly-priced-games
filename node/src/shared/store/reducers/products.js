import {FETCH_PRODUCTS_SUCCESS, FETCH_PRODUCT_SUCCESS} from "../actions/actionsTypes"

const intialState = {
    products: [],
    product: null,
}

export default function products(state = intialState, action) {
    switch (action.type) {
        case FETCH_PRODUCTS_SUCCESS: {

            return {
                ...state, products: action.products
            }
        }

        case FETCH_PRODUCT_SUCCESS:
            return {
                ...state, product: action.product
            }

        default: {
            return state
        }
    }
}