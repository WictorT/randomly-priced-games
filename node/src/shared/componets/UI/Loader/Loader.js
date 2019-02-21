import React from 'react';
import { withStyles } from '@material-ui/core/styles';
import CircularProgress from '@material-ui/core/CircularProgress';

const styles = theme => ({
    progress: {
        margin: theme.spacing.unit * 2,
    },
    root: {
        textAlign: 'center',
        paddingTop: '40%'
    },
});

function Loader(props) {
    const { classes } = props;
    return (
        <div className={classes.root}>
            <CircularProgress className={classes.progress} />
        </div>
    );
}

export default withStyles(styles)(Loader);