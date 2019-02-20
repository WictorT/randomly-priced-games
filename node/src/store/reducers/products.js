import {FETCH_PRODUCTS_SUCCESS} from "../actions/actionsTypes";

const intialState = {
    products: []
}

export default function products(state = intialState, action) {
    switch (action.type) {
        case FETCH_PRODUCTS_SUCCESS: {

            return {
                ...state, products: action.products
            }
        }

        default: {
            return state
        }
    }
}