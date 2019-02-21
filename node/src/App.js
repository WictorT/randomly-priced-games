import React, {Component} from 'react'
import {Route, Switch, withRouter} from 'react-router-dom'
import Layout from "./shared/hoc/Layout"
import HomePage from "./app/HomePage/HomePage"
import ProductDetails from "./app/ProductDetails/ProductDetails"

class App extends Component {

    render() {

        return(
            <Layout>
                <Switch>
                    <Route path="/" exact component={HomePage} ></Route>
                    <Route path="/:id" exact component={ProductDetails} ></Route>
                </Switch>
            </Layout>
        )
    }
}

export default withRouter(App)
