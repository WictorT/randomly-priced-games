import React, {Component} from 'react'
import Lists from "../../shared/componets/Lists/Lists"

class App extends Component {

    render() {
        const { history } = this.props

        return (
            <React.Fragment>
                <Lists history={history} />
            </React.Fragment>
        )
    }
}

export default App