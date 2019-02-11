import React, {Component} from 'react'
import Header from "./componets/Header/Header";
import Lists from "./componets/Lists/Lists";

class App extends Component {

    render() {

        return (
            <React.Fragment>
                <Header />
                <Lists />
            </React.Fragment>
        )
    }
}

export default App;
