import React, {Component} from "react"
import {withStyles} from "@material-ui/core/styles"
import AppBar from "@material-ui/core/AppBar"
import Toolbar from "@material-ui/core/Toolbar"
import Typography from "@material-ui/core/Typography"
import {NavLink} from 'react-router-dom'


const styles = {
    root: {
        flexGrow: 1,
    },
    grow: {
        flexGrow: 1,
    },
    link: {
        color: '#ffffff',
        fontWeight: 500,
        fontFamily: 'Roboto',
        textTransform: 'uppercase',
        letterSpacing: '0.02857em',
        textDecoration: 'none',
        '&:hover': {
            color: 'yellow'
        }
    }
};

class Header extends Component {


    render() {
        const { classes } = this.props

        return (
            <div className={classes.root}>
                <AppBar position="static">
                    <Toolbar>
                        <Typography variant="h6" color="inherit" className={classes.grow}>
                            Logo
                        </Typography>
                        <NavLink className={classes.link} to={'/'}>Products</NavLink>
                    </Toolbar>
                </AppBar>
            </div>
        )
    }
}

export default withStyles(styles)(Header);


