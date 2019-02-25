import React, {Component} from 'react'
import {Route, Switch, withRouter} from 'react-router-dom'
import Layout from "./shared/hoc/Layout"
import ProductDetails from "./app/ProductDetails/ProductDetails"
import Products from "./app/Products/Products"

class App extends Component {

    render() {

        return(
            <Layout>
                <Switch>
                    <Route path="/" exact component={Products} ></Route>
                    <Route path="/:id" component={ProductDetails} ></Route>
                </Switch>
            </Layout>
        )
    }
}

export default withRouter(App)
