import React, {Component} from 'react'
import {Route, Switch, withRouter} from 'react-router-dom'
import Layout from "./shared/hoc/Layout"
import ProductDetails from "./app/ProductDetails/ProductDetails"
import Products from "./app/Products/Products"
import Login from "./app/Login/Login"
import Register from "./app/Register/Register"

class App extends Component {

    render() {

        return(
            <Layout>
                <Switch>
                    <Route path="/login" exact component={Login} ></Route>
                    <Route path="/register" exact component={Register} ></Route>
                    <Route path="/products" exact component={Products} ></Route>
                    <Route path="/products/:id" component={ProductDetails} ></Route>
                </Switch>
            </Layout>
        )
    }
}

export default withRouter(App)
