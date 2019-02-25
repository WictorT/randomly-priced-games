import {FETCH_PRODUCTS_SUCCESS, FETCH_PRODUCT_SUCCESS} from "../actions/actionsTypes"

const intialState = {
    products: [],
    productsLoader: true,
    product: null,
    perPageProducts: 0
}

export default function products(state = intialState, action) {
    switch (action.type) {
        case FETCH_PRODUCTS_SUCCESS:
            return {
                ...state,
                productsLoader: false,
                products: action.products.data,
                perPage: action.products.per_page,
                totalPages: action.products.total_pages
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